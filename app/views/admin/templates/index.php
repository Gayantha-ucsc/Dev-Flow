<?php
// Expects: $templates, $allCount, $filters (['q','default_only'])
$q           = $filters['q'] ?? '';
$defaultOnly = $filters['default_only'] ?? false;
?>
<div class="admin-templates-page">

    <div class="admin-page-header">
        <div>
            <h1>Workflow templates</h1>
            <p>Reusable stage sequences a Team Lead can start a project from instead of building one from scratch.</p>
        </div>
        <button type="button" class="btn-primary" id="createTemplateBtn">
            <?= renderIcon('plus') ?>
            Create template
        </button>
    </div>

    <form class="card admin-templates-toolbar" method="get" action="<?= url('admin/templates') ?>" id="adminTemplatesFilterForm">
        <div class="projects-search">
            <?= renderIcon('search') ?>
            <input type="text" name="q" id="templateSearch" placeholder="Search templates or stages" value="<?= htmlspecialchars($q) ?>">
        </div>

        <label class="filter-pill admin-templates-toolbar__pill <?= $defaultOnly ? 'is-active' : '' ?>">
            <input type="checkbox" name="default_only" value="1" id="templateDefaultOnly" <?= $defaultOnly ? 'checked' : '' ?> hidden>
            Default only
        </label>

        <?php if ($q !== '' || $defaultOnly): ?>
            <a href="<?= url('admin/templates') ?>" class="icon-btn-sm" aria-label="Reset filters" title="Reset filters">
                <?= renderIcon('loader-circle') ?>
            </a>
        <?php endif; ?>
    </form>

    <?php if (empty($templates)): ?>
        <div class="card">
            <?php
                $variant = 'card';
                $icon    = 'layout-template';
                $heading = 'No matching templates';
                $subtext = 'Try a different search term, or clear the default-only filter.';
            ?>
            <?php include __DIR__ . '/../../partials/empty-state.php'; ?>
        </div>
    <?php else: ?>
        <div class="template-grid">
            <?php foreach ($templates as $tpl): ?>
                <div class="card admin-template-card">
                    <div class="admin-template-card__head">
                        <span class="template-card__icon template-card__icon--<?= htmlspecialchars($tpl['icon_bg']) ?>">
                            <?= renderIcon($tpl['icon']) ?>
                        </span>
                        <div class="admin-template-card__title-block">
                            <div class="admin-template-card__title-row">
                                <h3><?= htmlspecialchars($tpl['name']) ?></h3>
                                <?php if ($tpl['is_system_default']): ?>
                                    <span class="badge badge--primary">Default</span>
                                <?php endif; ?>
                            </div>
                            <span class="admin-template-card__stage-count"><?= count($tpl['stages']) ?> stages</span>
                        </div>
                    </div>

                    <p class="admin-template-card__desc"><?= htmlspecialchars($tpl['description']) ?></p>

                    <div class="admin-template-card__stages">
                        <span class="admin-template-card__stages-label">Stages</span>
                        <div class="admin-template-card__chips">
                            <?php foreach (array_slice($tpl['stages'], 0, 4) as $i => $stageName): ?>
                                <?php if ($i > 0): ?><?= renderIcon('arrow-right') ?><?php endif; ?>
                                <span class="badge badge--neutral"><?= htmlspecialchars($stageName) ?></span>
                            <?php endforeach; ?>
                            <?php if (count($tpl['stages']) > 4): ?>
                                <span class="badge badge--outline">+<?= count($tpl['stages']) - 4 ?> more</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="admin-template-card__footer">
                        <span class="admin-template-card__meta">
                            <?php if ($tpl['is_system_default']): ?>
                                <?= renderIcon('shield-check') ?>
                                Seeded system default
                            <?php else: ?>
                                <?= renderIcon('user') ?>
                                Created by <?= htmlspecialchars($tpl['created_by']) ?> &bull; <?= htmlspecialchars(date('M j, Y', strtotime($tpl['created_at']))) ?>
                            <?php endif; ?>
                        </span>
                        <div class="admin-template-card__actions">
                            <button type="button" class="icon-btn-sm js-edit-template" data-template-name="<?= htmlspecialchars($tpl['name']) ?>" title="Edit template" aria-label="Edit <?= htmlspecialchars($tpl['name']) ?>">
                                <?= renderIcon('pencil') ?>
                            </button>
                            <button type="button" class="icon-btn-sm js-duplicate-template" data-template-name="<?= htmlspecialchars($tpl['name']) ?>" title="Duplicate template" aria-label="Duplicate <?= htmlspecialchars($tpl['name']) ?>">
                                <?= renderIcon('copy') ?>
                            </button>
                            <button type="button"
                                    class="icon-btn-sm icon-btn-sm--danger js-delete-template"
                                    data-template-name="<?= htmlspecialchars($tpl['name']) ?>"
                                    title="<?= $tpl['is_system_default'] ? 'System defaults can\'t be deleted - duplicate it instead' : 'Delete template' ?>"
                                    aria-label="Delete <?= htmlspecialchars($tpl['name']) ?>"
                                    <?= $tpl['is_system_default'] ? 'disabled' : '' ?>>
                                <?= renderIcon('trash-2') ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="team-banner team-banner--info admin-templates-banner">
        <?= renderIcon('info') ?>
        <div>
            System default templates can be duplicated into an editable copy without changing the original -
            and once a project copies a template's stages onto itself, that project's stages are
            independent of the template from then on.
        </div>
        <a href="<?= url('admin/audit') ?>" class="admin-templates-banner__link">
            View audit log <?= renderIcon('arrow-right') ?>
        </a>
    </div>
</div>

<!-- Create template -->
<div class="modal-overlay" data-modal="create-template-modal" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form modal-box--template">
        <div class="modal-box__header">
            <h2><?= renderIcon('workflow') ?> Create template</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">Define a reusable stage sequence for future engineering pipelines.</p>

            <div class="form-group">
                <label for="newTemplateName">Template name <span class="required">*</span></label>
                <input type="text" id="newTemplateName" placeholder="e.g. Mobile App Development">
                <span class="field-error" data-error-for="newTemplateName"></span>
            </div>

            <div class="form-group">
                <label for="newTemplateDesc">Description</label>
                <textarea id="newTemplateDesc" rows="3" placeholder="Briefly describe when this template should be used..."></textarea>
            </div>

            <div class="template-default-toggle">
                <div>
                    <span class="template-default-toggle__label">Set as default template</span>
                    <span class="template-default-toggle__desc">Automatically suggested to Managers when creating a project</span>
                </div>
                <button type="button" class="status-switch" id="newTemplateDefaultSwitch" role="switch" aria-checked="false" aria-label="Set as default template"><span class="status-switch__knob"></span></button>
            </div>

            <div class="template-stage-builder">
                <div class="template-stage-builder__label">
                    <span>Stages sequence</span>
                    <span class="badge badge--neutral" id="newTemplateStageCount">0 stages</span>
                </div>
                <div class="wizard-stage-list" id="newTemplateStageList"></div>
                <button type="button" class="stage-add-btn" id="newTemplateAddStage">
                    <?= renderIcon('plus') ?> Add Stage
                </button>
            </div>

            <div class="modal-box__footer modal-box__footer--split">
                <span class="modal-box__draft-note" id="newTemplateDraftNote">Draft saved</span>
                <div class="modal-box__footer-actions">
                    <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                    <button type="button" class="btn-primary" id="submitCreateTemplate"><?= renderIcon('check') ?> Create template</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete template confirmation -->
<div class="modal-overlay" data-modal="delete-template-modal" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Delete template</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">
                Permanently delete <strong data-delete-template-name></strong>? Projects already using this
                template's stages are not affected, since their stages are independent copies.
            </p>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                <button type="button" class="btn-sm btn-sm--danger-outline" id="confirmDeleteTemplate">Delete template</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.TEMPLATE_ICONS = {
        grip:   <?= iconJson('grip-vertical') ?>,
        pencil: <?= iconJson('pencil') ?>,
        trash:  <?= iconJson('trash-2') ?>,
        x:      <?= iconJson('x') ?>,
        layout: <?= iconJson('layout-template') ?>
    };
</script>