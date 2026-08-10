<div class="settings-page">
    <h1 class="settings-page__title">Settings</h1>

    <div class="settings-card">
        <h2 class="settings-card__title">Profile information</h2>

        <div class="settings-avatar-row">
            <span class="avatar avatar--lg avatar--<?= avatarColorClass($currentUser['name']) ?>" data-profile-avatar>
                <?php if (!empty($currentUser['profile_picture'])): ?>
                    <img src="<?= htmlspecialchars($currentUser['profile_picture']) ?>" alt="" data-profile-avatar-img>
                <?php else: ?>
                    <span data-profile-avatar-initials><?= htmlspecialchars(initials($currentUser['name'])) ?></span>
                <?php endif; ?>
            </span>
            <button type="button" class="settings-change-photo" data-change-photo>Change photo</button>
            <input type="file" accept="image/*" hidden data-photo-input>
        </div>

        <div class="form-group">
            <label for="full-name">Full name</label>
            <input type="text" id="full-name" value="<?= htmlspecialchars($currentUser['name']) ?>">
        </div>

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" value="<?= htmlspecialchars($currentUser['email']) ?>" disabled>
        </div>
        <p class="settings-hint">Email can't be changed since it's tied to your account and notifications.</p>

        <div class="settings-card__footer">
            <button type="button" class="btn-primary" data-save-profile>Save changes</button>
        </div>
    </div>

    <div class="settings-card">
        <h2 class="settings-card__title">Change password</h2>

        <div class="form-group">
            <label for="current-password">Current password</label>
            <div class="password-field">
                <input type="password" id="current-password">
                <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                    <?= renderIcon('eye') ?>
                    <?= renderIcon('eye-off') ?>
                </button>
            </div>
            <span class="field-error" data-error-for="current-password"></span>
        </div>

        <div class="form-group">
            <label for="new-password">New password</label>
            <div class="password-field">
                <input type="password" id="new-password" placeholder="Enter new password">
                <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                    <?= renderIcon('eye') ?>
                    <?= renderIcon('eye-off') ?>
                </button>
            </div>
            <span class="field-error" data-error-for="new-password"></span>
        </div>

        <div class="form-group">
            <label for="confirm-password">Confirm new password</label>
            <div class="password-field">
                <input type="password" id="confirm-password" placeholder="Confirm new password">
                <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                    <?= renderIcon('eye') ?>
                    <?= renderIcon('eye-off') ?>
                </button>
            </div>
            <span class="field-error" data-error-for="confirm-password"></span>
        </div>

        <div class="settings-card__footer">
            <button type="button" class="btn-primary" data-update-password>Update password</button>
        </div>
    </div>

    <div class="settings-card settings-card--danger">
        <h2 class="settings-card__title settings-card__title--danger">Delete account</h2>
        <p class="settings-card__desc">This permanently deletes your account. Any projects you own or contribute to will be affected.</p>
        <button type="button" class="btn-outline-danger" data-modal-trigger="delete-account">Delete my account</button>
    </div>
</div>

<div class="modal-overlay" data-modal="delete-account" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box settings-delete-modal">
        <div class="settings-delete-modal__body">
            <h3>Delete your account?</h3>
            <p>This action is permanent and cannot be undone. Your account and profile will be removed; any projects you own or contribute to will be affected.</p>
        </div>
        <div class="settings-delete-modal__footer">
            <button type="button" class="btn-cancel" data-modal-close>Cancel</button>
            <button type="button" class="btn-outline-danger" data-confirm-delete>Yes, delete my account</button>
        </div>
    </div>
</div>