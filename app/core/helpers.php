<?php

function renderIcon(string $name): string {
    $path = __DIR__ . "/../../public/assets/icons/{$name}.svg";

    if (!file_exists($path)) {
        return '';
    }

    return file_get_contents($path);
}