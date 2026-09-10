<?php

$app = require __DIR__ . '/../config/app.php';

if ($app['env'] === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

date_default_timezone_set($app['timezone']);

require __DIR__ . '/../app/core/Session.php';
Session::start();

require __DIR__ . '/../app/core/helpers.php';
require __DIR__ . '/../app/core/Database.php';
require __DIR__ . '/../app/models/User.php';
require __DIR__ . '/../app/core/Validator.php';
require __DIR__ . '/../app/core/Auth.php';
require __DIR__ . '/../app/core/Middleware.php';
require __DIR__ . '/../app/core/Controller.php';
require __DIR__ . '/../app/core/Router.php';

(new Router())->dispatch();