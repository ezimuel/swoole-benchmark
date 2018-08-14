<?php
// Simple HTTP static file server
$http = new swoole_http_server('0.0.0.0', 9502);

$static = [
    'css'  => 'text/css',
    'js'   => 'text/javascript',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'jpg'  => 'image/jpg',
    'jpeg' => 'image/jpg',
    'mp4'  => 'video/mp4',
    'pdf'  => 'application/pdf'
];
$cacheTypeFile = [];

$http->on("start", function ($server) {
    printf("HTTP server started at %s:%s\n", $server->host, $server->port);
    printf("Master  PID: %d\n", $server->master_pid);
    printf("Manager PID: %d\n", $server->manager_pid);
});

$http->on("request", function ($request, $response) use ($static, $cacheTypeFile) {
    printf(
        "%s - %s - %s %s\n",
        date('Y-m-d H:i:sO', time()),
        $request->server['remote_addr'],
        $request->server['request_method'],
        $request->server['request_uri']
    );
    if (getStaticFile($request, $response, $static, $cacheTypeFile)) {
        return;
    }
    $response->status(404);
    $response->end();
});

$http->start();

function getStaticFile(
    swoole_http_request $request,
    swoole_http_response $response,
    array $static,
    array $cacheTypeFile
) : bool {
    $staticFile = __DIR__ . $request->server['request_uri'];
    if (! isset($cacheTypeFile[$staticFile])) {
        if (! file_exists($staticFile)) {
            return false;
        }
        $type = pathinfo($staticFile, PATHINFO_EXTENSION);
        if (! isset($static[$type])) {
            return false;
        }
        $cacheTypeFile[$staticFile] = $static[$type];
    }
    $response->header('Content-Type', $cacheTypeFile[$staticFile]);
    $response->sendfile($staticFile);
    return true;
}
