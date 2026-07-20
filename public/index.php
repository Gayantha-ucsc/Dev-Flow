<?php

$app = require __DIR__ . '/../config/app.php';

// Autoloader for classes
spl_autoload_register(function ($class) {
    $path = __DIR__ . '/../app/core/' . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
        return;
    }
    $path = __DIR__ . '/../app/models/' . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

if ($app['env'] === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

date_default_timezone_set($app['timezone']);
session_start(); // TODO: Dedicated session stuff

require __DIR__ . '/../app/core/helpers.php';
require __DIR__ . '/../app/core/Controller.php';
require __DIR__ . '/../app/core/Router.php';

(new Router())->dispatch();
