<?php
// Expects these variables passed in from the controller:
// $currentProjectId = 1
// $userProjects     = [['project_id'=>.., 'name'=>..], ...]  // projects this user belongs to
// $activeRole       = 'manager'                               // this user's one role on the current project
// $currentUser      = ['name' => 'User One', 'profile_picture' => null]
// $unreadCount      = 3
?>

<?php
$nonProjectRoutes = ['/dashboard', '/projects', '/settings'];
$isProjectScopedPage = !in_array($currentRoute ?? '', $nonProjectRoutes, true)
    && !str_starts_with($currentRoute ?? '', '/admin');
$showSwitcher = !empty($currentProjectId) && $isProjectScopedPage;
?>
<header class="navbar">
    <!-- Project Switcher -->
    <div class="navbar-left">
        <?php if ($showSwitcher): ?>
            <?php $projectNames = array_column($userProjects ?? [], 'name', 'project_id'); ?>
            <?php $currentProjectName = $projectNames[$currentProjectId] ?? 'Select Project'; ?>

            <div class="dropdown" data-dropdown data-project-switcher>
                <button class="project-switcher" data-dropdown-trigger>
                    <span><?= htmlspecialchars($currentProjectName) ?></span>
                    <!-- TODO: make the svg images assets -->
                    <svg class="chevron" width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>

                <div class="dropdown__menu dropdown__menu--project-switcher" data-dropdown-menu>
                    <?php if (count($userProjects ?? []) > 5): ?>
                        <div class="project-switcher-search">
                            <?= renderIcon('search') ?>
                            <input type="text" placeholder="Search projects..." data-project-search aria-label="Search projects">
                        </div>
                    <?php endif; ?>

                    <?php foreach (($userProjects ?? []) as $project): ?>
                        <a href="<?= url('/projects/' . (int)$project['project_id']) ?>"
                        class="dropdown__item <?= $project['project_id'] === ($currentProjectId ?? null) ? 'is-active' : '' ?>"
                        data-name="<?= htmlspecialchars(strtolower($project['name'])) ?>">
                        <?= htmlspecialchars($project['name']) ?>
                        </a>
                    <?php endforeach; ?>

                    <p class="project-switcher-empty" data-project-search-empty hidden>No projects match your search.</p>
                </div>
            </div>

            <span class="role-badge role-badge--static">
                <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $activeRole ?? 'User'))) ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="navbar-right">

        <!-- Notifications -->
        <div class="dropdown" data-dropdown>
            <button class="icon-btn" data-dropdown-trigger aria-label="Notifications">
                <?= renderIcon('bell') ?>
                <?php if (($unreadCount ?? 0) > 0): ?>
                    <span class="badge-count"><?= (int)$unreadCount ?></span>
                <?php endif; ?>
            </button>
            <div class="dropdown__menu dropdown__menu--right dropdown__menu--notifications" data-dropdown-menu>
                <?php include __DIR__ . '/notification-panel.php'; ?>
            </div>
        </div>

        <!-- User Menu -->
        <div class="dropdown" data-dropdown>
            <button class="user-menu-trigger" data-dropdown-trigger>
                <span class="user-name"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                <span class="avatar avatar--<?= avatarColorClass($currentUser['name'] ?? 'User') ?>">
                    <?php if (!empty($currentUser['profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($currentUser['profile_picture']) ?>" alt="">
                    <?php else: ?>
                        <?= htmlspecialchars(initials($currentUser['name'] ?? 'User')) ?>
                    <?php endif; ?>
                </span>
            </button>
            <div class="dropdown__menu dropdown__menu--right" data-dropdown-menu>
                <a href="<?= url('/settings') ?>" class="dropdown__item">Settings</a>
                <a href="<?= url('/logout')   ?>" class="dropdown__item dropdown__item--danger">Log out</a>
            </div>
        </div>

    </div>
</header>