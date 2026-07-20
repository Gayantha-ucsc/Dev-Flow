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
$currentProjectId = 4;
$userProjects   = [
    ['project_id' => 1, 'name' => 'Project Alpha'],
    ['project_id' => 2, 'name' => 'Project Beta'],
];
$activeRole = 'team_lead';
$userRoles  = ['team_lead', 'developer', 'user'];
$currentUser = ['name' => 'User One', 'profile_picture' => null];
$unreadCount = 69;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../app/views/partials/head-meta.php'; ?>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/components/navbar.css">
</head>
<body>
    <?php include __DIR__ . '/../app/views/partials/navbar.php'; ?>

    <main style="padding: 24px;">
        <p>Navbar test page.</p>
    </main>

    <script src="assets/js/global.js"></script>
</body>
</html>