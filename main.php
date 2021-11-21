<?php
declare(strict_types=1);

$interface = '0.0.0.0';
$port = 8000;
$defaults = array(
    'Content-Type' => 'text/html',
    'Server' => 'PHP '. phpversion()
);

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($sock === false) {
    echo 'Failed to create socket : ' . socket_strerror(socket_last_error()) . PHP_EOL;
    exit();
}

if (!socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) {
    echo 'Unable to set option on socket: '. socket_strerror(socket_last_error()) . PHP_EOL;
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

echo "Server is running on $interface:$port" . PHP_EOL;


$app = function($request) {
    $htmlBody = file_get_contents('index.html');
    return array(
        '200 OK',
        array('Content-Type' => 'text/html;charset=utf-8'),
        $htmlBody
    );
};

while ($conn = socket_accept($sock)) {
    $request = '';
    while (!str_ends_with($request, "\r\n\r\n")) {
        $request .= socket_read($conn, 1024);
    }
    list($code, $headers, $body) = $app($request);
    $headers += $defaults;
    if (!isset($headers['Content-Length'])) {
        $headers['Content-Length'] = strlen($body);
    }
    $header = '';
    foreach ($headers as $k => $v) {
        $header .= $k.': '.$v."\r\n";
    }
    socket_write($conn, implode("\r\n", array(
        'HTTP/1.1 '.$code,
        $header,
        $body
    )));
    socket_close($conn);
}
socket_close($sock);