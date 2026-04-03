<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = []): string
    {
        $path = __DIR__ . '/../../resources/views/' . $view . '.blade.php';
        if (!file_exists($path)) {
            return 'View not found: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
