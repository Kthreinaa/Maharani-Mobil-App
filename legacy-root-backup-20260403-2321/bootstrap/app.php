<?php

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require $autoload;
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (str_starts_with($class, $prefix)) {
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = $baseDir . $relative . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

$router = require __DIR__ . '/../routes/web.php';

return $router;
