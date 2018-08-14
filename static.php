<?php
// Simple HTTP static file server
$http = new swoole_http_server('0.0.0.0', 9501);

$http->set([
    'document_root' => __DIR__,
    'enable_static_handler' => true
]);

$http->on("start", function ($server) {
    printf("HTTP server started at %s:%s\n", $server->host, $server->port);
    printf("Master  PID: %d\n", $server->master_pid);
    printf("Manager PID: %d\n", $server->manager_pid);
});
$http->on("request", function ($request, $response) {
    printf(
        "%s - %s - %s %s\n",
        date('Y-m-d H:i:sO', time()),
        $request->server['remote_addr'],
        $request->server['request_method'],
        $request->server['request_uri']
    );
});
$http->start();
