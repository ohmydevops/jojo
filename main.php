#!/usr/bin/php
<?php

declare(strict_types=1);

$interface = '0.0.0.0';
$port = 8000;
$webDir = empty(getenv('BASE_WEB_DIR')) ? __DIR__ : getenv('BASE_WEB_DIR');
$defaultHeaders = [
    'Server' => 'joojoo'
];
// Properly configuring server MIME types:
// https://developer.mozilla.org/en-US/docs/Learn/Server-side/Configuring_server_MIME_types
$contentTypes = [
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

function fileMimeDetector(string $requestedFile, array $contentTypes): string
{
    $fileExtension = pathinfo($requestedFile, PATHINFO_EXTENSION);
    return $contentTypes[$fileExtension];
}

function cliLog(string $message): void
{
    $timestamp = date('Y-m-d H:i:s');
    echo "[$timestamp] $message" . PHP_EOL;
}

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($sock === false) {
    echo 'Failed to create socket : ' . socket_strerror(socket_last_error()) . PHP_EOL;
    exit();
}

if (!socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) {
    echo 'Unable to set option on socket: ' . socket_strerror(socket_last_error()) . PHP_EOL;
}

$isBind = socket_bind($sock, $interface, $port);
if ($isBind === false) {
    echo 'Failed to bind socket : ', socket_strerror(socket_last_error()), PHP_EOL;
    exit();
}

$isListen = socket_listen($sock, SOMAXCONN);
if ($isListen === false) {
    echo 'Failed to listen to socket : ', socket_strerror(socket_last_error()), PHP_EOL;
    exit();
}

$handleFileResponse = function (string $requestedFile) use ($contentTypes): array {
    $body = file_get_contents($requestedFile);
    return ['200 OK', ['Content-Type' => fileMimeDetector($requestedFile, $contentTypes)], $body];
};

$handleNotFoundResponse = function (): array {
    $body = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta content="width=device-width,initial-scale=1.0" name="viewport"><meta content="ie=edge" http-equiv="X-UA-Compatible"><title>Not founded</title></head><body><p>File or directory not founded.</p></body></html>';
    return [
        '404 Not Found',
        ['Content-Type' => 'text/html'],
        $body
    ];
};

$parentPID = (string)posix_getpid();
echo "Server is running on $interface:$port and PID: $parentPID" . PHP_EOL;

while ($client = socket_accept($sock)) {
    $pid = pcntl_fork();
    if ($pid === -1) {
        exit("Error forking...\n");
    } elseif ($pid === 0) {
        $request = '';
        while (!str_ends_with($request, "\r\n\r\n")) {
            $request .= socket_read($client, 1024);
        }
        $parsedData = explode("\r", $request);
        $path = parse_url(explode(" ", $parsedData[0])[1])['path'];
        if (!is_file($webDir . $path)) {
            list($code, $headers, $body) = $handleNotFoundResponse();
            $headers += $defaultHeaders;
            if (!isset($headers['Content-Length'])) {
                $headers['Content-Length'] = strlen($body);
            }
            $header = '';
            foreach ($headers as $k => $v) {
                $header .= $k . ': ' . $v . "\r\n";
            }
            socket_write(
                $client,
                implode("\r\n", array(
                    'HTTP/1.1 ' . $code,
                    $header,
                    $body
                ))
            );
        } else {
            list($code, $headers, $body) = $handleFileResponse($webDir . $path);
            $headers += $defaultHeaders;
            if (!isset($headers['Content-Length'])) {
                $headers['Content-Length'] = strlen($body);
            }
            $header = '';
            foreach ($headers as $k => $v) {
                $header .= $k . ': ' . $v . "\r\n";
            }
            socket_write(
                $client,
                implode("\r\n", array(
                    'HTTP/1.1 ' . $code,
                    $header,
                    $body
                ))
            );
        }
        socket_close($client);        
        $PID = (string)posix_getpid();
        cliLog("[PID: " . $PID . '] ' . $code . ' ' . $parsedData[0]);
        exit();
    } else {
        // prevent zombie proccess
        $pid = pcntl_wait($status, WNOHANG);
    }
}