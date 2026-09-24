<?php
// Expects:
//   $project    - real Project row (project_id, name, description, deadline, status, ...)
//   $stages     - Stage rows for this project, ordered by sequence_order, each with 'task_count'
//   $canManage  - bool, true when the current user is an active Manager or Team Lead here
//   $editingId  - int|null, the stage_id currently shown in its inline rename form
?>
<div class="project-overview">

    <div class="project-overview__header">
        <div class="project-overview__title-row">
            <h1><?= htmlspecialchars($project['name']) ?></h1>
            <span class="badge badge--<?= projectStatusTone($project['status']) ?> badge--outline">
                <?= htmlspecialchars(projectStatusLabel($project['status'])) ?>
            </span>
        </div>

        <?php if (!empty($project['description'])): ?>
            <p class="project-overview__description"><?= htmlspecialchars($project['description']) ?></p>
        <?php endif; ?>

        <?php if (!empty($project['deadline'])): ?>
            <div class="project-overview__meta">
                <span><?= renderIcon('calendar') ?> <?= htmlspecialchars(date('M j, Y', strtotime($project['deadline']))) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="project-overview__workflow">
        <div class="project-overview__workflow-header">
            <h2>Workflow Stages</h2>
            <?php if ($canManage): ?>
                <button type="button" class="btn-add-stage" data-modal-trigger="add-stage-modal">
                    <?= renderIcon('plus') ?> Add Stage
                </button>
            <?php endif; ?>
        </div>

        <?php if (empty($stages)): ?>
            <?php
                $icon       = 'layout-template';
                $heading    = 'No stages yet';
                $subtext    = $canManage
                    ? "Add your first stage to start building this project's workflow."
                    : "The Manager or Team Lead hasn't added any stages yet.";
                $variant    = 'card';
                $ctaText    = $canManage ? 'Add Stage' : null;
                $ctaTrigger = $canManage ? 'add-stage-modal' : null;
            ?>
            <?php include __DIR__ . '/../partials/empty-state.php'; ?>
        <?php else: ?>
            <div class="stage-list">
                <?php foreach ($stages as $i => $stage): ?>
                    <?php
                        $statusMeta = match ($stage['status']) {
                            'completed'           => ['icon' => 'circle-check', 'label' => 'Completed',          'tone' => 'completed'],
                            'in_progress'         => ['icon' => 'circle-dot',   'label' => 'In Progress',        'tone' => 'in-progress'],
                            'pending_completion'  => ['icon' => 'clock',        'label' => 'Pending Completion', 'tone' => 'pending'],
                            default               => ['icon' => null,          'label' => 'Not Started',        'tone' => 'not-started'],
                        };
                        $isEditing = $canManage && $editingId === (int) $stage['stage_id'];
                    ?>
                    <div class="card stage-item stage-item--static">
                        <div class="stage-row">

                            <?php if ($canManage): ?>
                                <div class="stage-row__order-controls">
                                    <?php if ($i > 0): ?>
                                        <form method="POST" action="<?= url('stages/' . $stage['stage_id'] . '/move') ?>">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="direction" value="up">
                                            <button type="submit" class="icon-btn-sm" style="transform: rotate(180deg);" aria-label="Move <?= htmlspecialchars($stage['name']) ?> up">
                                                <?= renderIcon('chevron-down') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($i < count($stages) - 1): ?>
                                        <form method="POST" action="<?= url('stages/' . $stage['stage_id'] . '/move') ?>">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="direction" value="down">
                                            <button type="submit" class="icon-btn-sm" aria-label="Move <?= htmlspecialchars($stage['name']) ?> down">
                                                <?= renderIcon('chevron-down') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <span class="stage-row__number"><?= $i + 1 ?></span>

                            <div class="stage-row__body">
                                <?php if ($isEditing): ?>
                                    <form method="POST" action="<?= url('stages/' . $stage['stage_id'] . '/update') ?>" class="stage-rename-form">
                                        <?= csrfField() ?>
                                        <input type="text" name="name" value="<?= htmlspecialchars($stage['name']) ?>" maxlength="150" required autofocus>
                                        <button type="submit" class="btn-sm btn-sm--primary">Save</button>
                                        <a href="<?= url('projects/' . $project['project_id'] . '/stages') ?>" class="btn-sm">Cancel</a>
                                    </form>
                                <?php else: ?>
                                    <h3 class="stage-row__name"><?= htmlspecialchars($stage['name']) ?></h3>
                                    <div class="stage-row__meta">
                                        <span class="stage-row__status stage-row__status--<?= $statusMeta['tone'] ?>">
                                            <?php if ($statusMeta['icon']): ?><?= renderIcon($statusMeta['icon']) ?><?php endif; ?>
                                            <?= $statusMeta['label'] ?>
                                        </span>
                                        <span class="stage-row__sep">&bull;</span>
                                        <span class="stage-row__tasks"><?= (int) $stage['task_count'] ?> task<?= ((int) $stage['task_count']) === 1 ? '' : 's' ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($canManage && !$isEditing): ?>
                                <div class="stage-row__actions">
                                    <a href="<?= url('projects/' . $project['project_id'] . '/stages?edit=' . $stage['stage_id']) ?>" class="icon-btn-sm" aria-label="Edit <?= htmlspecialchars($stage['name']) ?>">
                                        <?= renderIcon('pencil') ?>
                                    </a>
                                    <form method="POST" action="<?= url('stages/' . $stage['stage_id'] . '/delete') ?>" onsubmit="return confirm('Delete this stage? This cannot be undone.');">
                                        <?= csrfField() ?>
                                        <button type="submit" class="icon-btn-sm icon-btn-sm--danger" aria-label="Delete <?= htmlspecialchars($stage['name']) ?>">
                                            <?= renderIcon('trash-2') ?>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php if ($canManage): ?>
<div class="modal-overlay" data-modal="add-stage-modal" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Add stage</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <form method="POST" action="<?= url('projects/' . $project['project_id'] . '/stages') ?>" class="modal-box__body">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="stageName">Stage name <span class="required">*</span></label>
                <input type="text" id="stageName" name="name" maxlength="150" placeholder="e.g. Client Design Review" required autofocus>
            </div>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                <button type="submit" class="btn-sm btn-sm--primary"><?= renderIcon('plus') ?> Add stage</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
