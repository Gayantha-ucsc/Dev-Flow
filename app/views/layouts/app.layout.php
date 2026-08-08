<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../partials/head-meta.php'; ?>
    <?php include __DIR__ . '/../partials/project-wizard-modal.php'; ?>
    <?php include __DIR__ . '/../partials/icon-data.php'; ?>

    <link rel="stylesheet" href="<?= url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/navbar.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/sidebar.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/empty-state.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/card.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/badge.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/projects.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/project-detail.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/team.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/modal.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/wizard.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/datepicker.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/components/toast.css') ?>">

    <script src="<?= url('assets/js/datepicker.js') ?>"></script>
    <script src="<?= url('assets/js/workflow-step.js') ?>"></script>
    <script src="<?= url('assets/js/team-step.js') ?>"></script>
    <script src="<?= url('assets/js/review-step.js') ?>"></script>
    <script src="<?= url('assets/js/toast.js') ?>"></script>
    <script src="<?= url('assets/js/wizard.js') ?>"></script>
    <script src="<?= url('assets/js/projects.js') ?>"></script>
    <script src="<?= url('assets/js/team.js') ?>"></script>

</head>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        <div class="app-shell__main">
            <?php include __DIR__ . '/../partials/navbar.php'; ?>
            <main class="app-shell__content">
                <?php include __DIR__ . '/../partials/flash-message.php'; ?>
                <?= $content ?>
            </main>
        </div>
    </div>
    <script src="<?= url('assets/js/global.js') ?>"></script>
</body>
</html>