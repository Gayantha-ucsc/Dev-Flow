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