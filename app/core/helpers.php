<?php

function renderIcon(string $name): string {
    $path = __DIR__ . "/../../public/assets/icons/{$name}.svg";

    if (!file_exists($path)) {
        return '';
    }

    return file_get_contents($path);
}

function baseUrl(): string {
    static $base = null;
    if ($base === null) {
        $app = require __DIR__ . '/../../config/app.php';
        $base = rtrim($app['base_url'], '/');
    }
    return $base;
}

function url(string $path = ''): string {
    return baseUrl() . '/' . ltrim($path, '/');
}