<?php

namespace Core;

use RuntimeException;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        $viewFile = BASE_PATH . '/app/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View {$view} không tồn tại.");
        }

        extract($data, EXTR_SKIP);

        $pageTitle = $pageTitle ?? config('app.name', 'Application');
        $bodyClass = $bodyClass ?? '';
        $pageStyles = $pageStyles ?? [];
        $pageScripts = $pageScripts ?? [];
        $layout = $layout ?? 'layouts/main';
        $layoutFile = BASE_PATH . '/app/views/' . $layout . '.php';

        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Layout {$layout} không tồn tại.");
        }

        require $layoutFile;
    }

    protected function abort(int $statusCode, string $message): void
    {
        http_response_code($statusCode);

        if ($statusCode === 404) {
            require BASE_PATH . '/app/views/errors/404.php';
            exit;
        }

        echo '<h1>' . $statusCode . '</h1>';
        echo '<p>' . e($message) . '</p>';
        exit;
    }
}
