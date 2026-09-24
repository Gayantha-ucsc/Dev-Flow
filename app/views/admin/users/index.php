<?php
// Expects: $users, $totalCount, $allCount, $page, $totalPages, $perPage,
//          $rangeStart, $rangeEnd, $filters (['q','status','type'])
$q      = $filters['q'] ?? '';
$status = $filters['status'] ?? 'all';
$type   = $filters['type'] ?? 'all';
?>
<div class="admin-users-page">

    <?php if (!empty($newTempPassword)): ?>
        <div class="card admin-temp-pass">
            <strong>Account created.</strong> Temporary password for
            <strong><?= htmlspecialchars($newTempPassword['name']) ?></strong>
            (<?= htmlspecialchars($newTempPassword['username']) ?>):
            <code><?= htmlspecialchars($newTempPassword['password']) ?></code>
            <span class="admin-users-table__muted"> - shown once. They must change it on first login.</span>
        </div>
    <?php endif; ?>

    <div class="admin-page-header">
        <div>
            <h1>User management</h1>
            <p>Manage all registered accounts across DevFlow.</p>
        </div>
        <button type="button" class="btn-primary" id="createUserBtn" data-url="<?= url('admin/users/create') ?>">
            <?= renderIcon('user-plus') ?>
            Create user
        </button>
    </div>

    <form class="card admin-users-toolbar" method="get" action="<?= url('admin/users') ?>" id="adminUsersFilterForm">
        <div class="projects-search">
            <?= renderIcon('search') ?>
            <input type="text" name="q" id="adminUserSearch" placeholder="Search by name or email" value="<?= htmlspecialchars($q) ?>">
        </div>

        <div class="admin-users-toolbar__selects">
            <label class="admin-select">
                <span>Status:</span>
                <select name="status" id="adminStatusFilter">
                    <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Deactivated</option>
                </select>
            </label>

            <label class="admin-select">
                <span>Type:</span>
                <select name="type" id="adminTypeFilter">
                    <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All</option>
                    <option value="standard" <?= $type === 'standard' ? 'selected' : '' ?>>Standard</option>
                    <option value="temporary" <?= $type === 'temporary' ? 'selected' : '' ?>>Temporary</option>
                    <option value="admin" <?= $type === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </label>

            <button type="button" class="icon-btn-sm" id="adminFiltersReset" aria-label="Reset filters" title="Reset filters">
                <?= renderIcon('loader-circle') ?>
            </button>
        </div>
    </form>

    <div class="card admin-users-table-card">

        <?php if (empty($users)): ?>
            <?php
                $variant = 'card';
                $icon    = 'users';
                $heading = 'No matching users';
                $subtext = 'Try a different search term or clear your filters.';
            ?>
            <?php include __DIR__ . '/../../partials/empty-state.php'; ?>
        <?php else: ?>

            <div class="admin-users-table-scroll">
                <table class="admin-users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Joined Date</th>
                            <th class="admin-users-table__actions-head">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php
                                $typeMeta = adminAccountTypeMeta($user);
                                $isSelf   = $user['user_id'] === ($currentUser['user_id'] ?? null);
                            ?>
                            <tr data-user-id="<?= (int) $user['user_id'] ?>"
                                data-edit-url="<?= url('admin/users/' . (int) $user['user_id'] . '/edit') ?>"
                                data-delete-url="<?= url('admin/users/' . (int) $user['user_id'] . '/delete') ?>"
                                data-user-name="<?= htmlspecialchars($user['name']) ?>"
                                data-active="<?= $user['is_active'] ? '1' : '0' ?>">
                                <td>
                                    <div class="admin-user-cell">
                                        <span class="avatar avatar--<?= avatarColorClass($user['name']) ?>">
                                            <?= htmlspecialchars(initials($user['name'])) ?>
                                        </span>
                                        <span class="admin-user-cell__body">
                                            <span class="admin-user-cell__name">
                                                <?= htmlspecialchars($user['name']) ?>
                                                <?php if ($isSelf): ?><span class="member-cell__you">(You)</span><?php endif; ?>
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td class="admin-users-table__muted"><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge js-status-badge badge--<?= $user['is_active'] ? 'success' : 'neutral' ?>">
                                        <span class="status-dot"></span>
                                        <span class="js-status-label"><?= $user['is_active'] ? 'Active' : 'Deactivated' ?></span>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge--<?= $typeMeta['tone'] ?>">
                                        <?php if ($typeMeta['icon']): ?><?= renderIcon($typeMeta['icon']) ?><?php endif; ?>
                                        <?= htmlspecialchars($typeMeta['label']) ?>
                                    </span>
                                </td>
                                <td class="admin-users-table__muted"><?= htmlspecialchars(date('M j, Y', strtotime($user['joined_at']))) ?></td>
                                <td>
                                    <div class="admin-users-table__actions">
                                        <button type="button" class="icon-btn-sm js-edit-user" title="Edit user" aria-label="Edit <?= htmlspecialchars($user['name']) ?>">
                                            <?= renderIcon('pencil') ?>
                                        </button>
                                        <button type="button" class="icon-btn-sm js-reset-password" title="Reset password" aria-label="Reset password for <?= htmlspecialchars($user['name']) ?>">
                                            <?= renderIcon('key') ?>
                                        </button>
                                        <button type="button"
                                                class="status-switch js-toggle-active <?= $user['is_active'] ? 'is-on' : '' ?>"
                                                role="switch"
                                                aria-checked="<?= $user['is_active'] ? 'true' : 'false' ?>"
                                                title="<?= $user['is_active'] ? 'Deactivate account' : 'Reactivate account' ?>"
                                                <?= $isSelf ? 'disabled' : '' ?>>
                                            <span class="status-switch__knob"></span>
                                        </button>
                                        <button type="button" class="icon-btn-sm icon-btn-sm--danger js-delete-user" title="Delete user" aria-label="Delete <?= htmlspecialchars($user['name']) ?>" <?= $isSelf ? 'disabled' : '' ?>>
                                            <?= renderIcon('trash-2') ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-users-footer">
                <p class="admin-users-footer__count">
                    Showing <strong><?= (int) $rangeStart ?>-<?= (int) $rangeEnd ?></strong> of <strong><?= (int) $totalCount ?></strong> users
                </p>
                <?php
                    $baseUrl    = '/admin/users';
                    $baseParams = ['q' => $q, 'status' => $status, 'type' => $type];
                ?>
                <?php include __DIR__ . '/../../partials/pagination.php'; ?>
            </div>

        <?php endif; ?>
    </div>
</div>

<!-- Reactivate confirmation -->
<div class="modal-overlay" id="deleteUserModal" hidden>
    <div class="modal-backdrop" data-close-modal></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Delete user account</h2>
            <button type="button" class="icon-btn" data-close-modal aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">
                Permanently delete <strong id="deleteUserName"></strong>? This cannot be undone.
                If they still hold active project memberships, those will need to be handled first.
            </p>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-close-modal>Cancel</button>
                <button type="button" class="btn-sm btn-sm--danger-outline" id="confirmDeleteUser">Delete</button>
            </div>
        </div>
    </div>
</div>

<form method="post" id="deleteUserForm" hidden>
    <?= csrfField() ?>
</form>