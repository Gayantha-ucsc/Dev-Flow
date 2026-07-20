<?php
// Expects:
// $currentProjectId    — null triggers the no-project state
// $currentProjectName  
// $activeRole          — currently active role context
// $currentRoute        — current URL path

$navConfig = require __DIR__ . '/../../../config/nav.php';
$hasProject = !empty($currentProjectId);

$visibleNavItems = [];
if ($hasProject) {
    foreach ($navConfig as $item) {
        if (in_array($activeRole, $item['roles'], true)) {
            $visibleNavItems[] = $item;
        }
    }
}
?>
<aside class="sidebar">

    <div class="sidebar__brand">
        <img src="assets/img/logo.png" alt="DevFlow" class="sidebar__logo">
        <span class="sidebar__brand-name">DevFlow</span>
    </div>

    <a href="/projects/create" class="btn-create-project">
        <?= renderIcon('plus') ?>
        <span>Create a Project</span>
    </a>

    <?php if ($hasProject): ?>
        <div class="sidebar__section-label">
            <?= htmlspecialchars(strtoupper($currentProjectName)) ?>
        </div>
        <nav class="sidebar__nav">
            <?php foreach ($visibleNavItems as $item): ?>
                <a href="<?= htmlspecialchars($item['href']) ?>"
                   class="sidebar__nav-item <?= ($currentRoute ?? '') === $item['href'] ? 'is-active' : '' ?>">
                    <?= renderIcon($item['icon']) ?>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php else: ?>
        <nav class="sidebar__nav">
            <a href="/dashboard" class="sidebar__nav-item is-active">
                <?= renderIcon('dashboard') ?>
                <span>Dashboard</span>
            </a>
        </nav>
    <?php endif; ?>

    <div class="sidebar__spacer"></div>

    <div class="sidebar__footer">
        <a href="/settings" class="sidebar__nav-item">
            <?= renderIcon('settings') ?>
            <span>Settings</span>
        </a>
    </div>

</aside>