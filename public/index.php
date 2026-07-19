<?php

$app = require __DIR__ . '/../config/app.php';

if ($app['env'] === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

date_default_timezone_set($app['timezone']);

session_start(); // TODO: Dedicated session stuff

// TODO: TEMP placeholder
echo "<h1>Dev-Flow bootstrap OK</h1>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../app/views/partials/head-meta.php'; ?>
</head>
<body>
</body>
</html>