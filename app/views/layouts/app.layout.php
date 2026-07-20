<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../partials/head-meta.php'; ?>
    <link rel="stylesheet" href="<?= url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/navbar.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/sidebar.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/empty-state.css') ?>">
</head>
<body>
    <div style="display:flex;">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        <div style="flex:1; display:flex; flex-direction:column;">
            <?php include __DIR__ . '/../partials/navbar.php'; ?>
            <main style="flex:1;"><?= $content ?></main>
        </div>
    </div>
    <script src="<?= url('assets/js/global.js') ?>"></script>
</body>
</html>