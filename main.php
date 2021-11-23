<?php

declare(strict_types=1);

$interface = '0.0.0.0';
$port = 8000;

// Properly configuring server MIME types:
// https://developer.mozilla.org/en-US/docs/Learn/Server-side/Configuring_server_MIME_types
$contentTypes = [
    'html' => 'text/html;charset=utf-8',
    'css' => 'text/css',
    'js' => 'text/javascript'
];

$defaultHeaders = [
    'Server' => 'PHP ' . phpversion()
];

function fileMimeDetector(string $requestedFile, array $contentTypes): string
{
    $fileExtension = pathinfo($requestedFile, PATHINFO_EXTENSION);
    return $contentTypes[$fileExtension];
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

$handleFileResponse = function (string $requestedFile) use ($contentTypes) {
    $body = file_get_contents($requestedFile);
    return ['200 OK', ['Content-Type' => fileMimeDetector($requestedFile, $contentTypes)], $body];
};

$handleNotFoundResponse = function () {
    return [
        '404 Not Found',
        ['Content-Type' => 'text/html'],
        "<html lang='fa' dir='rtl'><head><title>یافت نشد</title></head><body><h3>همچین فایلی وجود ندارد</h3></body></html>"
    ];
};


echo "Server is running on $interface:$port" . PHP_EOL;

while ($conn = socket_accept($sock)) {
    $request = '';
    while (!str_ends_with($request, "\r\n\r\n")) {
        $request .= socket_read($conn, 1024);
    }
    $parsedData = explode("\r", $request);
    $requestedFile = basename(explode(" ", $parsedData[0])[1]);
    if (file_exists($requestedFile) === false) {
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
            $conn,
            implode("\r\n", array(
                'HTTP/1.1 ' . $code,
                $header,
                $body
            ))
        );
        socket_close($conn);
    } else {
        list($code, $headers, $body) = $handleFileResponse($requestedFile);
        $headers += $defaultHeaders;
        if (!isset($headers['Content-Length'])) {
            $headers['Content-Length'] = strlen($body);
        }
        $header = '';
        foreach ($headers as $k => $v) {
            $header .= $k . ': ' . $v . "\r\n";
        }
        socket_write(
            $conn,
            implode("\r\n", array(
                'HTTP/1.1 ' . $code,
                $header,
                $body
            ))
        );
        socket_close($conn);
    }
    echo $code . ' ' . $parsedData[0] . PHP_EOL;
}