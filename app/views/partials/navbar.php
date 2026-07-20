<?php
// Expects these variables passed in from the controller:
// $currentProjectId = 1
// $userProjects     = [['project_id'=>.., 'name'=>..], ...]  // projects this user belongs to
// $activeRole       = 'team_lead'                             // currently active role context
// $userRoles        = ['team_lead', 'developer']               // ALL roles this user holds on current project
// $currentUser      = ['name' => 'User One', 'profile_picture' => null]
// $unreadCount      = 3
?>
<header class="navbar">
    <!-- Project Switcher -->
    <div class="navbar-left">
        <?php if (!empty($currentProjectId)): ?>
            <?php $projectNames = array_column($userProjects ?? [], 'name', 'project_id'); ?>
            <?php $currentProjectName = $projectNames[$currentProjectId] ?? 'Select Project'; ?>

            <div class="dropdown" data-dropdown>
                <button class="project-switcher" data-dropdown-trigger>
                    <span><?= htmlspecialchars($currentProjectName) ?></span>
                    <!-- TODO: make the svg images assets -->
                    <svg class="chevron" width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>

                <div class="dropdown__menu" data-dropdown-menu>
                    <?php foreach (($userProjects ?? []) as $project): ?>
                        <a href="<?= url('/projects/' . (int)$project['project_id']) ?>"
                        class="dropdown__item <?= $project['project_id'] === ($currentProjectId ?? null) ? 'is-active' : '' ?>">
                        <?= htmlspecialchars($project['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Role Switcher — placeholder data for now, wire to real ProjectMember lookup later -->
            <?php if (count($userRoles ?? []) > 1): ?>
                <div class="dropdown" data-dropdown>
                    <button class="role-badge" data-dropdown-trigger>
                        <?php 
                        if ($currentProjectName === 'Select Project'){
                            $activeRole = 'User';
                        }; ?>
                        <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $activeRole ?? ''))) ?>
                        <svg class="chevron" width="12" height="12" viewBox="0 0 24 24" fill="none">
                            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>

                    <div class="dropdown__menu" data-dropdown-menu>
                        <?php foreach ($userRoles as $role): ?>
                            <a href="?switch_role=<?= urlencode($role) ?>"
                            class="dropdown__item <?= $role === $activeRole ? 'is-active' : '' ?>">
                                <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $role))) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                    <!-- Single role  -->
                    <span class="role-badge role-badge--static">
                        <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $activeRole ?? ''))) ?>
                    </span>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="navbar-right">

        <!-- Notifications -->
         <a href="<?= url('/notifications') ?>" class="icon-btn" aria-label="Notifications">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M13.7 21a2 2 0 01-3.4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <?php if (($unreadCount ?? 0) > 0): ?>
                <span class="badge-count"><?= (int)$unreadCount ?></span>
            <?php endif; ?>
        </a>

        <!-- User Menu -->
        <div class="dropdown" data-dropdown>
            <button class="user-menu-trigger" data-dropdown-trigger>
                <span class="user-name"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                <span class="user-avatar">
                    <?php if (!empty($currentUser['profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($currentUser['profile_picture']) ?>" alt="">
                    <?php else: ?>
                        <img src="<?= url('assets/img/default-avatar.png') ?>" alt="">
                    <?php endif; ?>
                </span>
            </button>
            <div class="dropdown__menu dropdown__menu--right" data-dropdown-menu>
                <a href="<?= url('/profile')  ?>" class="dropdown__item">Profile</a>
                <a href="<?= url('/settings') ?>" class="dropdown__item">Settings</a>
                <a href="<?= url('/logout')   ?>" class="dropdown__item dropdown__item--danger">Log out</a>
            </div>
        </div>

    </div>
</header>