<?php
// Expects: $mode ('create'|'edit'), $old (array), $errors (array), $editId (int|null)
$action = $mode === 'edit' ? url('admin/users/' . (int) $editId . '/update') : url('admin/users');
$isSelfEdit = $mode === 'edit' && (int) $editId === (int) ($currentUser['user_id'] ?? 0);
?>
<div class="admin-users-page">
    <div class="admin-page-header">
        <div>
            <h1><?= $mode === 'edit' ? 'Edit user' : 'Create user' ?></h1>
            <p><?= $mode === 'edit' ? 'Update this account\'s details.' : 'A temporary password will be generated. The user must change it on first login.' ?></p>
        </div>
        <a class="btn-sm" href="<?= url('admin/users') ?>">Back to users</a>
    </div>

    <form method="post" action="<?= $action ?>" class="card admin-user-form" novalidate>
        <?= csrfField() ?>

        <?php foreach (['name' => 'Full name', 'username' => 'Username', 'email' => 'Email'] as $field => $label): ?>
            <div class="af-group">
                <label for="f_<?= $field ?>"><?= $label ?></label>
                <input type="<?= $field === 'email' ? 'email' : 'text' ?>" id="f_<?= $field ?>" name="<?= $field ?>"
                       value="<?= htmlspecialchars($old[$field] ?? '') ?>" required>
                <?php if (!empty($errors[$field])): ?><span class="af-error"><?= htmlspecialchars($errors[$field]) ?></span><?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="af-group af-check">
            <input type="checkbox" id="f_is_admin" name="is_admin" value="1"
                   <?= !empty($old['is_admin']) ? 'checked' : '' ?> <?= $isSelfEdit ? 'disabled' : '' ?>>
            <label for="f_is_admin">System administrator</label>
            <?php if ($isSelfEdit): ?><span class="af-hint">(you can't change your own admin status)</span><?php endif; ?>
        </div>

        <div class="af-actions">
            <button type="submit" class="btn-primary"><?= $mode === 'edit' ? 'Save changes' : 'Create user' ?></button>
            <a class="btn-sm" href="<?= url('admin/users') ?>">Cancel</a>
        </div>
    </form>
</div>