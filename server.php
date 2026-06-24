<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$publicPath = __DIR__.'/public'.$uri;

if ($uri !== '/' && is_file($publicPath)) {
    serveStaticFile($publicPath);
    return;
}

require __DIR__.'/public/index.php';

function serveStaticFile(string $path): void
{
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $mimes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jfif' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/vnd.microsoft.icon',
        'txt' => 'text/plain',
        'mp4' => 'video/mp4',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
    ];

    header('Content-Type: '.($mimes[$extension] ?? 'application/octet-stream'));
    header('Cache-Control: public, max-age=31536000');
    readfile($path);
}
