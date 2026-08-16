<?php
// Expects:
// $currentProjectId    — null triggers the no-project state
// $currentProjectName
// $activeRole           — user's role on the currently selected project
// $currentRoute         — current URL path
// $currentUser          — used for the is_admin check

$navConfig  = require __DIR__ . '/../../../config/nav.php';
$hasProject = !empty($currentProjectId);
$route      = $currentRoute ?? '';

$isAdmin     = !empty($currentUser['is_admin']);
$isAdminMode = $isAdmin && str_starts_with($route, '/admin');

$visibleProjectItems = [];
if ($hasProject) {
    foreach ($navConfig['project'] as $item) {
        if (in_array($activeRole, $item['roles'], true)) {
            $visibleProjectItems[] = $item;
        }
    }
}

?>
<aside class="sidebar">

    <div class="sidebar__brand">
        <img src="<?= url('assets/img/logo.png') ?>" alt="DevFlow" class="sidebar__logo">
        <span class="sidebar__brand-name">DevFlow</span>
    </div>

    <?php if ($isAdmin): ?>
        <div class="sidebar__mode-toggle" role="tablist">
            <a href="<?= url('/dashboard') ?>" class="sidebar__mode-btn <?= !$isAdminMode ? 'is-active' : '' ?>" role="tab" aria-selected="<?= !$isAdminMode ? 'true' : 'false' ?>">Projects</a>
            <a href="<?= url('/admin/users') ?>" class="sidebar__mode-btn <?= $isAdminMode ? 'is-active' : '' ?>" role="tab" aria-selected="<?= $isAdminMode ? 'true' : 'false' ?>">Admin</a>
        </div>
    <?php endif; ?>

    <?php if ($isAdminMode): ?>

        <nav class="sidebar__nav">
            <?php foreach ($navConfig['admin'] as $item): ?>
                <a href="<?= url($item['href']) ?>"
                   class="sidebar__nav-item <?= $route === $item['href'] ? 'is-active' : '' ?>">
                    <?= renderIcon($item['icon']) ?>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar__spacer"></div>

        <div class="sidebar__footer">
            <a href="<?= url('/settings') ?>" class="sidebar__nav-item">
                <?= renderIcon('settings') ?>
                <span>Settings</span>
            </a>
        </div>

    <?php else: ?>

        <button type="button" class="btn-create-project" data-modal-trigger="project-wizard">
            <?= renderIcon('plus') ?>
            <span>Create a Project</span>
        </button>

        <?php if ($hasProject): ?>
            <div class="sidebar__section-label">
                <?= htmlspecialchars(strtoupper($currentProjectName)) ?>
            </div>
        <?php endif; ?>

        <nav class="sidebar__nav">
            <a href="<?= url('/dashboard') ?>" class="sidebar__nav-item <?= $route === '/dashboard' ? 'is-active' : '' ?>">
                <?= renderIcon('dashboard') ?>
                <span>Dashboard</span>
            </a>
            <a href="<?= url('/projects') ?>" class="sidebar__nav-item <?= $route === '/projects' ? 'is-active' : '' ?>">
                <?= renderIcon('folder') ?>
                <span>Projects</span>
            </a>
            <?php if ($hasProject): ?>
                <?php foreach ($visibleProjectItems as $item): ?>
                    <a href="<?= sidebarHref($item['href'], $currentProjectId) ?>"
                       class="sidebar__nav-item <?= $route === str_replace('{id}', (string) $currentProjectId, $item['href']) ? 'is-active' : '' ?>">
                        <?= renderIcon($item['icon']) ?>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </nav>

        <div class="sidebar__spacer"></div>

        <div class="sidebar__footer">
            <a href="<?= url('/settings') ?>" class="sidebar__nav-item">
                <?= renderIcon('settings') ?>
                <span>Settings</span>
            </a>
        </div>

    <?php endif; ?>

</aside>