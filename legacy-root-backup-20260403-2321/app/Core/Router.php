<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $path): string
    {
        $path = $this->normalize($path);
        if (isset($this->routes[$method][$path])) {
            return (string) call_user_func($this->routes[$method][$path]);
        }

        return View::render('pages/404');
    }

    private function normalize(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
