<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);
echo $response->getStatusCode(), "\n";
echo substr($response->getContent(), 0, 1000), "\n";
$kernel->terminate($request, $response);
