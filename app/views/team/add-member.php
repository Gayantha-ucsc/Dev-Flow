<?php
// Expects: $searchableUsers, $hasTeamLead, $currentProjectName
?>
<div class="team-page team-form-page">

    <div class="team-header">
        <div>
            <a href="<?= url('team') ?>" class="back-link"><?= renderIcon('arrow-left') ?> Back to Team</a>
            <h1>Add Member</h1>
            <p>Search and add a collaborator to <?= htmlspecialchars($currentProjectName ?? 'this project') ?>.</p>
        </div>
    </div>

    <?php if (!$hasTeamLead): ?>
    <div class="team-banner team-banner--highlight">
        <?= renderIcon('flag') ?>
        <div>
            <strong>First Team Lead — unilateral add</strong>
            This project has no Team Lead yet. You can appoint the first Team Lead directly without cross-approval.
            Other roles still follow the standard approval flow.
        </div>
    </div>
    <?php else: ?>
    <div class="team-banner team-banner--info">
        <?= renderIcon('info') ?>
        <div>
            <strong>Cross-approval required</strong>
            Members you add as Developer or Designer require approval from the qualifying approver
            (Manager additions need Team Lead approval; Team Lead additions need Manager approval).
        </div>
    </div>
    <?php endif; ?>

    <div class="team-form-grid">
        <div class="card team-form-card">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('search') ?> Search Users</h2>
            </div>
            <div class="team-form-card__body">
                <div class="projects-search team-form-search">
                    <?= renderIcon('search') ?>
                    <input type="text" id="userSearch" placeholder="Search by name, email, or skill..." autocomplete="off">
                </div>

                <div class="user-search-results" id="userSearchResults">
                    <?php foreach ($searchableUsers as $user): ?>
                        <button type="button" class="user-result"
                                data-user-id="<?= (int) $user['user_id'] ?>"
                                data-user-name="<?= htmlspecialchars($user['name']) ?>"
                                data-user-email="<?= htmlspecialchars($user['email']) ?>"
                                data-user-skills="<?= htmlspecialchars(strtolower(implode(' ', $user['skills']))) ?>">
                            <span class="avatar avatar--<?= avatarColorClass($user['name']) ?>">
                                <?= htmlspecialchars(strtoupper(substr($user['name'], 0, 1))) ?>
                            </span>
                            <span class="user-result__body">
                                <span class="user-result__name"><?= htmlspecialchars($user['name']) ?></span>
                                <span class="user-result__email"><?= htmlspecialchars($user['email']) ?></span>
                                <span class="user-result__skills"><?= htmlspecialchars(implode(' · ', $user['skills'])) ?></span>
                            </span>
                        </button>
                    <?php endforeach; ?>
                    <p class="user-search-empty" id="userSearchEmpty" hidden>No users match your search.</p>
                </div>
            </div>
        </div>

        <div class="card team-form-card">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('user-plus') ?> Member Details</h2>
            </div>
            <form class="team-form-card__body" id="addMemberForm">
                <div class="form-group form-group--compact">
                    <label>Selected Member</label>
                    <div class="selected-user-placeholder" id="selectedUserPlaceholder">
                        <?= renderIcon('user') ?>
                        <span>Select a user from the search list on the left</span>
                    </div>
                    <div class="selected-user" id="selectedUser" hidden>
                        <span class="avatar avatar--primary" id="selectedUserAvatar"></span>
                        <div class="selected-user__info">
                            <div class="selected-user__name" id="selectedUserName"></div>
                            <div class="selected-user__email" id="selectedUserEmail"></div>
                        </div>
                        <button type="button" class="btn-sm selected-user__change" id="clearSelectedUser">Change</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="memberRole">Assign Role</label>
                    <select id="memberRole" name="role" required>
                        <?php if (!$hasTeamLead): ?>
                            <option value="team_lead">Team Lead (direct add)</option>
                        <?php endif; ?>
                        <option value="developer" <?= !$hasTeamLead ? '' : 'selected' ?>>Developer</option>
                        <option value="designer">Designer</option>
                        <?php if ($hasTeamLead): ?>
                            <option value="team_lead">Team Lead</option>
                        <?php endif; ?>
                    </select>
                    <p class="form-hint" id="roleHint">
                        <?php if (!$hasTeamLead): ?>
                            First Team Lead is added immediately without approval.
                        <?php else: ?>
                            This role requires approval from the qualifying approver.
                        <?php endif; ?>
                    </p>
                </div>

                <div class="form-group">
                    <label for="memberNote">Note (optional)</label>
                    <textarea id="memberNote" name="note" rows="3" placeholder="Reason for adding this member..."></textarea>
                </div>

                <div class="form-actions">
                    <a href="<?= url('team') ?>" class="btn-sm">Cancel</a>
                    <button type="submit" class="btn-add-member" id="submitAddMember" disabled>Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
