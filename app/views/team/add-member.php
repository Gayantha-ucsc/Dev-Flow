<?php
// Expects: $hasTeamLead, $currentProjectName
// Users are looked up on demand by name/email, not browsed.
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
            <strong>First Team Lead - unilateral add</strong>
            This project has no Team Lead yet. You can appoint the first Team Lead directly without cross-approval.
            Other roles still follow the standard approval flow.
        </div>
    </div>
    <?php else: ?>
    <div class="team-banner team-banner--info">
        <?= renderIcon('info') ?>
        <div>
            <strong>Cross-approval required</strong>
            Members you add as Developer or Designer require approval from the qualifying approver.
        </div>
    </div>
    <?php endif; ?>

    <div class="team-form-grid team-form-grid--single">
        <div class="card team-form-card">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('user-plus') ?> Add Member</h2>
            </div>
            <form class="team-form-card__body" id="addMemberForm">
                <div class="form-row">
                    <div class="form-group" style="grid-column: 1 / span 2;">
                        <label for="userIdentifier">Username or email</label>
                        <input type="text" id="userIdentifier" placeholder="Enter exact username or email..." autocomplete="off">
                        <span class="field-error" id="identifierError"></span>
                    </div>
                    <div class="form-group">
                        <label for="memberRole">Assign Role</label>
                        <select id="memberRole" name="role">
                            <?php if (!$hasTeamLead): ?>
                                <option value="team_lead">Team Lead (direct add)</option>
                            <?php endif; ?>
                            <option value="developer" <?= !$hasTeamLead ? '' : 'selected' ?>>Developer</option>
                            <option value="designer">Designer</option>
                            <?php if ($hasTeamLead): ?>
                                <option value="team_lead">Team Lead</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group form-group--compact">
                    <button type="button" class="btn-add-member btn-add-member--inline" id="addIdentifierBtn">
                        <?= renderIcon('plus') ?> Add to List
                    </button>
                    <p class="form-hint">Enter the person's exact username or email.</p>
                </div>

                <div class="team-members" id="addedMemberSection" hidden>
                    <div class="team-members__label">MEMBER TO ADD</div>
                    <div class="team-members__list" id="addedMemberList"></div>
                </div>

                <div class="form-group">
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