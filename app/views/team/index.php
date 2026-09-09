<?php
// Expects: $members, $pendingApprovals, $currentProjectName, $hasTeamLead
$soloNoRequests = count($members) <= 1 && empty($pendingApprovals);
?>
<div class="team-page">

    <div class="team-header">
        <div>
            <h1>Team Members</h1>
            <p>Manage who has access to <?= htmlspecialchars($currentProjectName ?? 'this project') ?> and their roles.</p>
        </div>
        <div class="team-header__actions">
            <a href="<?= url('team/invite-client') ?>" class="btn-outline">Invite Client</a>
            <a href="<?= url('team/add-member') ?>" class="btn-add-member"><?= renderIcon('user-plus') ?> Add Member</a>
        </div>
    </div>

    <?php if (!$hasTeamLead && count($members) > 0): ?>
    <div class="team-banner team-banner--info">
        <?= renderIcon('info') ?>
        <div>
            <strong>No Team Lead assigned yet.</strong>
            The first Team Lead can be added directly without cross-approval.
            <a href="<?= url('team/add-member') ?>">Add Team Lead</a>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($soloNoRequests): ?>

        <?php
            $variant  = 'card';
            $icon     = 'user-plus';
            $heading  = 'Build your team';
            $subtext  = 'Invite your first collaborator to start delivering together. Set roles, assign tasks, and watch the project grow.';
            $ctaText  = 'Add Member';
            $ctaHref  = url('team/add-member');
        ?>
        <?php include __DIR__ . '/../partials/empty-state.php'; ?>

    <?php else: ?>

        <?php if (!empty($pendingApprovals)): ?>
        <div class="card team-pending">
            <div class="card__header">
                <h2 class="card__title">
                    <?= renderIcon('user-check') ?> Pending Approvals
                    <span class="pending-count"><?= count($pendingApprovals) ?></span>
                </h2>
                <a href="<?= url('team/approvals') ?>" class="card__link">View approval queue</a>
            </div>
            <div class="pending-list">
                <?php foreach (array_slice($pendingApprovals, 0, 2) as $req): ?>
                    <?php
                        $reqRole = str_contains($req['requestedBy'], 'Manager') ? 'manager' : 'team_lead';
                        $approver = qualifyingApproverLabel($reqRole);
                    ?>
                    <div class="pending-item">
                        <span class="avatar avatar--<?= avatarColorClass($req['name']) ?>">
                            <?= htmlspecialchars(initials($req['name'])) ?>
                        </span>
                        <div class="pending-item__body">
                            <div class="pending-item__name"><?= htmlspecialchars($req['name']) ?></div>
                            <div class="pending-item__email"><?= htmlspecialchars($req['email']) ?></div>
                            <div class="pending-item__meta">
                                Requested Role:
                                <span class="badge badge--<?= memberRoleTone($req['requestedRole']) ?>"><?= memberRoleLabel($req['requestedRole']) ?></span>
                                &nbsp;&bull;&nbsp; Added by <?= htmlspecialchars($req['requestedBy']) ?>
                                &nbsp;&bull;&nbsp; Awaiting <?= htmlspecialchars($approver) ?>
                            </div>
                        </div>
                        <div class="pending-item__actions">
                            <a href="<?= url('team/approvals') ?>" class="btn-sm btn-sm--primary">Review</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card team-table-card">
            <?php if (count($members) > 1): ?>
            <div class="team-toolbar">
                <div class="projects-search">
                    <?= renderIcon('search') ?>
                    <input type="text" id="memberSearch" placeholder="Search by name or email...">
                </div>
                <div class="projects-filters" id="roleFilters">
                    <button type="button" class="filter-pill is-active" data-filter="all">All Roles</button>
                    <button type="button" class="filter-pill" data-filter="manager">Manager</button>
                    <button type="button" class="filter-pill" data-filter="team_lead">Team Lead</button>
                    <button type="button" class="filter-pill" data-filter="developer">Developer</button>
                    <button type="button" class="filter-pill" data-filter="designer">Designer</button>
                    <button type="button" class="filter-pill" data-filter="client">Client</button>
                </div>
                <div class="projects-sort">
                    <label for="statusFilter">Status:</label>
                    <select id="statusFilter">
                        <option value="all">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <?php endif; ?>

            <div class="team-table-scroll">
                <table class="team-table" id="memberTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <?php
                                $memberInitials = initials($member['name']);
                            ?>
                            <tr data-name="<?= htmlspecialchars(strtolower($member['name'] . ' ' . $member['email'])) ?>"
                                data-role="<?= htmlspecialchars($member['role']) ?>"
                                data-status="<?= htmlspecialchars($member['status']) ?>"
                                data-member-id="<?= (int) $member['user_id'] ?>"
                                data-member-name="<?= htmlspecialchars($member['name']) ?>"
                                data-member-role="<?= htmlspecialchars($member['role']) ?>">
                                <td>
                                    <div class="member-cell">
                                        <span class="avatar avatar--<?= avatarColorClass($member['name']) ?>"><?= htmlspecialchars($memberInitials) ?></span>
                                        <span class="member-cell__name">
                                            <?= htmlspecialchars($member['name']) ?>
                                            <?php if ($member['user_id'] === $currentUser['user_id']): ?>
                                                <span class="member-cell__you">(You)</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="team-table__muted"><?= htmlspecialchars($member['email']) ?></td>
                                <td>
                                    <span class="badge badge--<?= memberRoleTone($member['role']) ?>"><?= memberRoleLabel($member['role']) ?></span>
                                </td>
                                <td>
                                    <span class="badge badge--<?= $member['status'] === 'active' ? 'success' : 'neutral' ?>">
                                        <?= ucfirst($member['status']) ?>
                                    </span>
                                </td>
                                <td class="team-table__muted"><?= htmlspecialchars(date('M j, Y', strtotime($member['joinedAt']))) ?></td>
                                <td>
                                    <?php if ($member['user_id'] !== $currentUser['user_id']): ?>
                                    <button type="button" class="icon-btn js-manage-member" aria-label="Manage <?= htmlspecialchars($member['name']) ?>"
                                            data-member-id="<?= (int) $member['user_id'] ?>"
                                            data-member-name="<?= htmlspecialchars($member['name']) ?>"
                                            data-member-role="<?= htmlspecialchars($member['role']) ?>"
                                            data-member-status="<?= htmlspecialchars($member['status']) ?>">
                                        <?= renderIcon('ellipsis-vertical') ?>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="team-table__no-results" id="memberNoResults" hidden>No members match your search.</p>
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- Change Role Modal -->
<div class="modal-overlay" id="changeRoleModal" hidden>
    <div class="modal-backdrop" data-close-modal></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Change Member Role</h2>
            <button type="button" class="icon-btn" data-close-modal aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <form class="modal-box__body" id="changeRoleForm">
            <p class="modal-subtext">Update role for <strong id="changeRoleMemberName"></strong>. Role changes require approval from the qualifying approver.</p>
            <div class="form-group">
                <label for="newRole">New Role</label>
                <select id="newRole" name="role">
                    <option value="developer">Developer</option>
                    <option value="designer">Designer</option>
                    <option value="team_lead">Team Lead</option>
                </select>
            </div>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-close-modal>Cancel</button>
                <button type="submit" class="btn-sm btn-sm--primary">Submit for Approval</button>
            </div>
        </form>
    </div>
</div>

<!-- Deactivate / Remove Modal -->
<div class="modal-overlay" id="deactivateModal" hidden>
    <div class="modal-backdrop" data-close-modal></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Deactivate or Remove Member</h2>
            <button type="button" class="icon-btn" data-close-modal aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <form class="modal-box__body" id="deactivateForm">
            <p class="modal-subtext">Choose an action for <strong id="deactivateMemberName"></strong>.</p>
            <div class="form-group">
                <label class="radio-card">
                    <input type="radio" name="action" value="deactivate" checked>
                    <span class="radio-card__body">
                        <strong>Deactivate</strong>
                        <small>Member loses access but remains in project history.</small>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="action" value="remove">
                    <span class="radio-card__body">
                        <strong>Remove from project</strong>
                        <small>Permanently removes member. Requires approver confirmation.</small>
                    </span>
                </label>
            </div>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-close-modal>Cancel</button>
                <button type="submit" class="btn-sm btn-sm--danger-outline">Confirm</button>
            </div>
        </form>
    </div>
</div>

<!-- Manage Member Menu -->
<div class="dropdown-menu" id="memberManageMenu" hidden>
    <button type="button" class="dropdown-menu__item" data-action="change-role"><?= renderIcon('user') ?> Change Role</button>
    <button type="button" class="dropdown-menu__item dropdown-menu__item--danger" data-action="deactivate"><?= renderIcon('ban') ?> Deactivate / Remove</button>
</div>
