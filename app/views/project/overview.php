<?php
// Expects: 
    // $project (one row from config/mock/projects-list.php)
    // $stages (config/mock/project-detail.php)
?>
<div class="project-overview">

    <div class="project-overview__header">
        <a class="project-overview__edit" href="<?= url('projects/' . $project['id'] . '/edit') ?>" aria-label="Edit project details">
            <?= renderIcon('pencil') ?>
        </a>

        <div class="project-overview__title-row">
            <h1><?= htmlspecialchars($project['name']) ?></h1>
            <span class="badge badge--<?= projectStatusTone($project['status']) ?> badge--outline">
                <?= htmlspecialchars(ucfirst($project['status'])) ?>
            </span>
        </div>

        <p class="project-overview__description"><?= htmlspecialchars($project['description']) ?></p>

        <div class="project-overview__meta">
            <span><?= renderIcon('calendar') ?> Deadline: <?= htmlspecialchars(date('M j, Y', strtotime($project['deadline']))) ?></span>
            <span><?= renderIcon('loader-circle') ?> <?= (int) $project['percent'] ?>% Complete</span>
        </div>
    </div>

    <div class="project-overview__workflow">
        <div class="project-overview__workflow-header">
            <h2>Project Workflow</h2>
            <!-- Stage CRUD (FR-2.3.1) isn't wired up yet - visual only for now. -->
            <button type="button" class="btn-add-stage"><?= renderIcon('plus') ?> Add Stage</button>
        </div>

        <?php if (empty($stages)): ?>
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
                        $isCurrent = in_array($stage['status'], ['in_progress', 'pending_completion'], true);
                    ?>
                    <div class="card stage-row <?= $isCurrent ? 'stage-row--current' : '' ?>">
                        <button type="button" class="stage-row__reorder" aria-label="Reorder <?= htmlspecialchars($stage['name']) ?>">
                            <?= renderIcon('chevrons-up-down') ?>
                        </button>

                        <span class="stage-row__number <?= $isCurrent ? 'stage-row__number--current' : '' ?>">
                            <?= $i + 1 ?>
                        </span>

                        <div class="stage-row__body">
                            <h3 class="stage-row__name"><?= htmlspecialchars($stage['name']) ?></h3>
                            <div class="stage-row__meta">
                                <span class="stage-row__status stage-row__status--<?= $statusMeta['tone'] ?>">
                                    <?php if ($statusMeta['icon']): ?><?= renderIcon($statusMeta['icon']) ?><?php endif; ?>
                                    <?= $statusMeta['label'] ?>
                                </span>
                                <?php if ($stage['tasksTotal'] > 0): ?>
                                    <span class="stage-row__sep">&bull;</span>
                                    <span class="stage-row__tasks"><?= (int) $stage['tasksApproved'] ?>/<?= (int) $stage['tasksTotal'] ?> tasks approved</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="stage-row__actions">
                            <button type="button" class="icon-btn" aria-label="Edit <?= htmlspecialchars($stage['name']) ?>"><?= renderIcon('pencil') ?></button>
                            <button type="button" class="icon-btn icon-btn--danger" aria-label="Delete <?= htmlspecialchars($stage['name']) ?>"><?= renderIcon('trash-2') ?></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>