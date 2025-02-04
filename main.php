#!/usr/bin/env php
<?php

declare(strict_types=1);

$interface = '0.0.0.0';
$port = 8000;
$worker_count = get_processor_cores_number();
$workers = [];

$web_dir = empty(getenv('BASE_WEB_DIR')) ? __DIR__ : getenv('BASE_WEB_DIR');
$default_headers = [
    'Server' => 'joojoo',
    'Connection' => 'Keep-alive'
];

// MIME types
$content_types = [
    'html' => 'text/html;charset=utf-8',
    'css' => 'text/css',
    'js' => 'text/javascript',
    'apng' => 'image/apng',
    'gif' => 'image/gif',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'png' => 'image/png',
    'svg' => 'image/svg+xml',
    'webp' => 'image/webp',
    'ogg' => 'audio/ogg',
    'oga' => 'audio/ogg',
    'mp3' => 'audio/mpeg3',
    'wav' => 'audio/wav',
    'mp4' => 'video/mp4',
    '.3gp' => 'video/3gpp',
    'flv' => 'video/x-flv',
    'mov' => 'video/quicktime',
    'mpg4' => 'video/mp4',
    'json' => 'application/json',
    'apk'  => 'application/vnd.android.package-archive'
];

function file_mime_detector(string $requested_file, array $content_types): string
{
    $file_extension = pathinfo($requested_file, PATHINFO_EXTENSION);
    return $content_types[$file_extension] ?? 'application/octet-stream'; // Default MIME type
}

function logging(string $message): void
{
    echo "$message" . PHP_EOL;
}

function get_headers_from_request(string $request): array
{
    return array_reduce(
        explode("\r\n", trim($request)),
        function ($headers, $line) {
            if (strpos($line, ": ") !== false) {
                list($key, $value) = explode(": ", $line, 2);
                $headers[strtolower($key)] = strtolower($value);
            }
            return $headers;
        },
        []
    );
}

function get_first_line_http(string $request): string
{
    return explode("\r\n", trim($request))[0];
}

function handle_file_response(string $requested_file, array $content_types): array
{
    if (!is_readable($requested_file)) {
        return handle_not_found_response();
    }
    $body = file_get_contents($requested_file);
    return ['200', ['Content-Type' => file_mime_detector($requested_file, $content_types)], $body];
}

function handle_not_found_response(): array
{
    $body = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta content="width=device-width,initial-scale=1.0" name="viewport"><meta content="ie=edge" http-equiv="X-UA-Compatible"><title>Not founded</title></head><body><p>File or directory not founded.</p></body></html>';
    return [
        '404',
        ['Content-Type' => 'text/html'],
        $body
    ];
}

function handle_error(string $message, $client_socket): void
{
    logging("Error: $message");
    socket_close($client_socket);
}

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($sock === false) {
    $error = socket_strerror(socket_last_error());
    logging('Failed to create socket: ' . $error);
    exit();
}

if (!socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) {
    $error = socket_strerror(socket_last_error());
    logging('Unable to set option on socket: ' . $error);
    exit();
}

function get_processor_cores_number(): int
{
    return (int) shell_exec('nproc');
}

$is_bind = socket_bind($sock, $interface, $port);
if ($is_bind === false) {
    $error = socket_strerror(socket_last_error());
    logging('Failed to bind socket: ' . $error);
    exit();
}

$is_listen = socket_listen($sock, SOMAXCONN);
if ($is_listen === false) {
    $error = socket_strerror(socket_last_error());
    logging('Failed to listen to socket: ' . $error);
    exit();
}

// socket_set_nonblock($sock);

function worker_process(Socket $socket, string $web_dir, array $content_types)
{
    while ($client = socket_accept($socket)) {
        $request = '';
        while (!str_ends_with($request, "\r\n\r\n")) {
            $data = socket_read($client, 1024);
            if ($data === false) {
                $error = socket_strerror(socket_last_error($client));
                handle_error("Error reading from socket: $error", $client);
                break;
            }
            $request .= $data;
        }

        if (empty($request)) {
            continue;
        }

        // $request_headers = get_headers_from_request($request);
        $request_path = parse_url(explode(" ", get_first_line_http($request))[1])['path'];

        if (!is_file($web_dir . $request_path)) {
            list($code, $headers, $body) = handle_not_found_response();
        } else {
            list($code, $headers, $body) = handle_file_response($web_dir . $request_path, $content_types);
        }

        if (!isset($headers['Content-Length'])) {
            $headers['Content-Length'] = strlen($body);
        }

        $header = '';
        foreach ($headers as $k => $v) {
            $header .= $k . ': ' . $v . "\r\n";
        }
        $response = implode("\r\n", array(
            'HTTP/1.1 ' . $code,
            $header,
            $body
        ));

        $bytes_written = socket_write($client, $response);
        if ($bytes_written === false) {
            $error = socket_strerror(socket_last_error($client));
            handle_error("Error writing to socket: $error", $client);
        }

        socket_getpeername($client, $address);
        socket_close($client);
        logging($address . ' - - ' . "[" . date("d/M/Y:H:i:s O") . "]" . ' ' . get_first_line_http($request) . ' ' . $code . ' ' . strlen($body));
    }
}


for ($i = 0; $i < $worker_count; $i++) {
    $pid = pcntl_fork();
    if ($pid === -1) {
        logging("Failed to fork the process");
        exit();
    } elseif ($pid) {
        $workers[] = $pid;
    } else {
        worker_process($sock, $web_dir, $content_types);
        exit(0);
    }
}

echo "🚀 Server is running on $interface:$port with $worker_count workers." . PHP_EOL;
print_r($workers);

foreach ($workers as $worker_pid) {
    pcntl_waitpid($worker_pid, $status);
}
