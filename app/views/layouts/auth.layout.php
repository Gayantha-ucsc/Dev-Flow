<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../partials/head-meta.php'; ?>
    <?php include __DIR__ . '/../partials/icon-data.php'; ?>

    <link rel="stylesheet" href="<?= url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/toast.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/auth.css') ?>">

    <script src="<?= url('assets/js/toast.js') ?>" defer></script>
</head>
<body class="auth-body">
    <?php include __DIR__ . '/../partials/flash-message.php'; ?>
    <main class="auth-shell">
        <?= $content ?>
    </main>
</body>
</html>