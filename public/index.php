<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

date_default_timezone_set('Asia/Ho_Chi_Minh');

$sessionPath = BASE_PATH . '/storage/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

if (is_dir($sessionPath) && is_writable($sessionPath)) {
    session_save_path($sessionPath);
}

session_start();

$GLOBALS['config'] = [
    'app' => require BASE_PATH . '/config/app.php',
    'database' => require BASE_PATH . '/config/database.php',
];

spl_autoload_register(function (string $class): void {
    $mappings = [
        'Core\\' => BASE_PATH . '/core/',
        'App\\Controllers\\' => BASE_PATH . '/app/controllers/',
        'App\\Models\\' => BASE_PATH . '/app/models/',
    ];

    foreach ($mappings as $prefix => $directory) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $relativeClass = substr($class, strlen($prefix));
        $filePath = $directory . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($filePath)) {
            require $filePath;
        }
    }
});

require BASE_PATH . '/core/helpers.php';

$router = new Core\Router();
require BASE_PATH . '/routes/web.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

if ($scriptDirectory !== '/' && $scriptDirectory !== '.' && str_starts_with($requestUri, $scriptDirectory)) {
    $requestUri = substr($requestUri, strlen($scriptDirectory));
}

$requestUri = $requestUri === '' ? '/' : $requestUri;

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $requestUri);
} catch (Throwable $throwable) {
    http_response_code(500);
    $errorMessage = $throwable->getMessage();
    require BASE_PATH . '/app/views/errors/500.php';
}
