<?php

abstract class Controller {
    // $view is a path relative to app/views/, without .php
    protected function render(string $view, array $data = []): void {
        extract($data);

        ob_start();
        require __DIR__ . "/../views/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . '/../views/layouts/app.layout.php';
    }
}