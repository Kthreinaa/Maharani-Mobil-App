<?php

$router = require __DIR__ . '/../bootstrap/app.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

echo $router->dispatch($method, $path);
