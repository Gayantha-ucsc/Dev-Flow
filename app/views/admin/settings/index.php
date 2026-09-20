<?php
// Expects: $groups (config/mock/system-settings.php), $templateOptions
?>
<div class="admin-settings-page">

    <div class="admin-page-header">
        <div>
            <h1>System settings</h1>
            <p>Configure system-wide behavior and thresholds shared across every project.</p>
        </div>
    </div>

    <?php foreach ($groups as $group): ?>
        <div class="card settings-group">
            <div class="settings-group__head">
                <span class="settings-group__icon"><?= renderIcon($group['icon']) ?></span>
                <div>
                    <h2><?= htmlspecialchars($group['label']) ?></h2>
                    <p><?= htmlspecialchars($group['description']) ?></p>
                </div>
            </div>

            <?php foreach ($group['settings'] as $setting): ?>
                <div class="settings-row">
                    <div class="settings-row__body">
                        <span class="settings-row__label"><?= htmlspecialchars($setting['label']) ?></span>
                        <span class="settings-row__desc"><?= htmlspecialchars($setting['description']) ?></span>
                    </div>
                    <div class="settings-row__control">
                        <?php if ($setting['type'] === 'select'): ?>
                            <span class="badge badge--primary settings-value-pill">
                                <?= renderIcon('workflow') ?>
                                <span class="js-setting-display" data-key="<?= htmlspecialchars($setting['key']) ?>"><?= htmlspecialchars($setting['value']) ?></span>
                            </span>
                        <?php else: ?>
                            <span class="badge badge--neutral settings-value-pill">
                                <span class="js-setting-display" data-key="<?= htmlspecialchars($setting['key']) ?>"><?= htmlspecialchars($setting['value']) ?></span>
                                <?= htmlspecialchars($setting['unit']) ?>
                            </span>
                        <?php endif; ?>
                        <button type="button"
                                class="icon-btn-sm js-edit-setting"
                                title="Edit"
                                aria-label="Edit <?= htmlspecialchars($setting['label']) ?>"
                                data-key="<?= htmlspecialchars($setting['key']) ?>"
                                data-label="<?= htmlspecialchars($setting['label']) ?>"
                                data-description="<?= htmlspecialchars($setting['description']) ?>"
                                data-type="<?= htmlspecialchars($setting['type']) ?>"
                                data-value="<?= htmlspecialchars($setting['value']) ?>"
                                data-unit="<?= htmlspecialchars($setting['unit'] ?? '') ?>"
                                data-updated-by="<?= htmlspecialchars($setting['updated_by']) ?>"
                                data-updated-at="<?= htmlspecialchars(date('M j, Y', strtotime($setting['updated_at']))) ?>">
                            <?= renderIcon('pencil') ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="team-banner team-banner--info">
        <?= renderIcon('shield-check') ?>
        <div>
            All changes here are recorded in the <a href="<?= url('admin/audit') ?>">Audit Log</a> with the acting
            administrator and a timestamp.
        </div>
    </div>
</div>

<div class="modal-overlay" id="editSettingModal" hidden>
    <div class="modal-backdrop" data-close-modal></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2 id="editSettingTitle">Edit setting</h2>
            <button type="button" class="icon-btn" data-close-modal aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext" id="editSettingDesc"></p>

            <div class="form-group" id="editSettingNumberField">
                <label for="editSettingNumberInput">Value</label>
                <div class="settings-modal-input-row">
                    <input type="text" inputmode="numeric" id="editSettingNumberInput">
                    <span id="editSettingUnitLabel" class="settings-modal-unit"></span>
                </div>
            </div>

            <div class="form-group" id="editSettingSelectField" hidden>
                <label for="editSettingSelectInput">Value</label>
                <select id="editSettingSelectInput">
                    <?php foreach ($templateOptions as $name): ?>
                        <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <p class="settings-modal-meta">Last changed by <strong id="editSettingUpdatedBy"></strong>, <span id="editSettingUpdatedAt"></span>.</p>

            <div class="form-actions">
                <button type="button" class="btn-sm" data-close-modal>Cancel</button>
                <button type="button" class="btn-primary" id="saveSettingBtn">Save</button>
            </div>
        </div>
    </div>
</div>