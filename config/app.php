<?php

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/BTL/public/index.php'));
$scriptDirectory = rtrim($scriptDirectory, '/');
$projectDirectory = rtrim(str_replace('\\', '/', dirname($scriptDirectory)), '/');

$dynamicBaseUrl = $scheme . '://' . $host . ($scriptDirectory === '' ? '' : $scriptDirectory);
$dynamicAssetUrl = $scheme . '://' . $host . ($projectDirectory === '' ? '' : $projectDirectory);

return [
    'name' => 'ClearVision',
    'base_url' => getenv('APP_URL') ?: $dynamicBaseUrl,
    'asset_url' => getenv('ASSET_URL') ?: $dynamicAssetUrl,
];
