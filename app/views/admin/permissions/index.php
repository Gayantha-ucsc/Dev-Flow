<?php
// Expects: $roles, $groups, $lastUpdatedBy, $lastUpdatedAt, $allowedCount, $restrictedCount
?>
<div class="admin-permissions-page">

    <div class="admin-page-header">
        <div>
            <h1>Roles &amp; permissions</h1>
            <p>Control what each role can do across every project. Changes apply system-wide.</p>
        </div>
    </div>

    <div class="team-banner team-banner--info">
        <?= renderIcon('info') ?>
        <div>
            A Manager only gets Team Lead-style actions (like creating tasks) if it's switched on here - it's never
            hardcoded to the role. Toggle the <strong>Manager</strong> column for the relevant permission to change that.
        </div>
    </div>

    <div class="card permission-table-card">
        <div class="permission-table-scroll">
            <table class="permission-table" id="permissionTable">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <?php foreach ($roles as $role): ?>
                            <th>
                                <span class="permission-table__role-head">
                                    <span class="role-dot role-dot--<?= memberRoleTone($role) ?>"></span>
                                    <?= htmlspecialchars(memberRoleLabel($role)) ?>
                                </span>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $group): ?>
                        <tr class="permission-group-row">
                            <td colspan="<?= count($roles) + 1 ?>">
                                <?= renderIcon($group['icon']) ?>
                                <?= htmlspecialchars($group['label']) ?>
                            </td>
                        </tr>
                        <?php foreach ($group['permissions'] as $permission): ?>
                            <tr>
                                <td><?= htmlspecialchars($permission['label']) ?></td>
                                <?php foreach ($roles as $role): ?>
                                    <?php $isAllowed = !empty($permission['roles'][$role]); ?>
                                    <td>
                                        <button type="button"
                                                class="permission-switch js-permission-toggle <?= $isAllowed ? 'is-on' : '' ?>"
                                                role="switch"
                                                aria-checked="<?= $isAllowed ? 'true' : 'false' ?>"
                                                data-permission="<?= htmlspecialchars($permission['key']) ?>"
                                                data-role="<?= htmlspecialchars($role) ?>"
                                                aria-label="<?= htmlspecialchars($permission['label'] . ' - ' . memberRoleLabel($role)) ?>">
                                            <span class="permission-switch__knob"></span>
                                        </button>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="permission-table-footer">
            <span>
                <?= renderIcon('clock') ?>
                Last updated by <strong><?= htmlspecialchars($lastUpdatedBy) ?></strong>,
                <?= htmlspecialchars(timeAgo($lastUpdatedAt)) ?>
            </span>
            <span class="permission-table-footer__counts">
                <span><span class="role-dot role-dot--primary"></span> <strong id="permAllowedCount"><?= (int) $allowedCount ?></strong> allowed</span>
                <span><span class="role-dot role-dot--neutral"></span> <strong id="permRestrictedCount"><?= (int) $restrictedCount ?></strong> restricted</span>
            </span>
        </div>
    </div>
</div>