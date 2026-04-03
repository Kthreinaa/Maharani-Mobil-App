<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): string
    {
        $path = $this->normalizePath($path);
        if (isset($this->routes[$method][$path])) {
            return (string) call_user_func($this->routes[$method][$path]);
        }

        return View::render('pages/404');
    }

    private function normalizePath(string $path): string
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
