<?php
// Expects: 
    // $project (one row from config/mock/projects-list.php),
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
            <span><?= renderIcon('calendar') ?> <?= htmlspecialchars(date('M j, Y', strtotime($project['deadline']))) ?></span>
            <span><?= renderIcon('trending-up') ?> <?= htmlspecialchars(projectStatusLabel($project['status'])) ?> &mdash; <?= (int) $project['percent'] ?>% complete</span>
        </div>

        <?php if (!empty($members)): ?>
            <div class="project-overview__team">
                <span class="project-overview__team-label">Team</span>
                <div class="avatar-stack">
                    <?php foreach (array_slice($members, 0, 4) as $member): ?>
                        <span class="avatar avatar--stacked avatar--<?= avatarColorClass($member['name']) ?>" title="<?= htmlspecialchars($member['name']) ?>">
                            <?= htmlspecialchars(initials($member['name'])) ?>
                        </span>
                    <?php endforeach; ?>
                    <?php $extraMembers = count($members) - 4; ?>
                    <?php if ($extraMembers > 0): ?>
                        <span class="avatar avatar--stacked avatar--overflow">+<?= $extraMembers ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="project-pipeline card">
        <div class="project-pipeline__track">
            <?php foreach ($stages as $stage):
                $fraction = $stage['tasksTotal'] > 0 ? $stage['tasksApproved'] / $stage['tasksTotal'] : 0;
                $fillPct  = match (true) {
                    $stage['status'] === 'completed' => 100,
                    in_array($stage['status'], ['in_progress', 'pending_completion'], true) => round($fraction * 100),
                    default => 0,
                };
            ?>
                <div class="project-pipeline__segment">
                    <span class="project-pipeline__label"><?= htmlspecialchars($stage['name']) ?></span>
                    <div class="project-pipeline__track-bg">
                        <div class="project-pipeline__fill" style="width: <?= $fillPct ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="project-pipeline__pills">
            <span class="badge badge--outline badge--neutral">
                <span class="pill-dot"></span> <?= (int) $project['blockedCount'] ?> Blocked
            </span>
            <span class="badge badge--outline <?= $project['overdueCount'] > 0 ? 'badge--danger' : 'badge--neutral' ?>">
                <span class="pill-dot"></span> <?= (int) $project['overdueCount'] ?> Overdue
            </span>
            <span class="badge badge--outline <?= $project['pendingCount'] > 0 ? 'badge--pink' : 'badge--neutral' ?>">
                <span class="pill-dot"></span> <?= (int) $project['pendingCount'] ?> Pending Approvals
            </span>
        </div>
    </div>

    <?php if (($project['milestonesTotal'] ?? 0) > 0): ?>
        <a href="<?= url('/payment') ?>" class="project-payment-card card">
            <span class="project-payment-card__icon"><?= renderIcon('payment') ?></span>
            <div class="project-payment-card__body">
                <span class="project-payment-card__label"><?= (int) $project['milestonesPaid'] ?>/<?= (int) $project['milestonesTotal'] ?> milestones paid</span>
                <div class="project-payment-card__bar">
                    <div class="project-payment-card__bar-fill" style="width: <?= round(($project['milestonesPaid'] / $project['milestonesTotal']) * 100) ?>%"></div>
                </div>
            </div>
            <span class="project-payment-card__chevron"><?= renderIcon('chevron-right') ?></span>
        </a>
    <?php endif; ?>

    <div class="project-overview__workflow">
        <div class="project-overview__workflow-header">
            <h2>Project Workflow</h2>
            <!-- Stage CRUD isn't wired up yet -->
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
                    <div class="card stage-item <?= $isCurrent ? 'stage-item--current stage-item--expanded' : '' ?>">
                        <div class="stage-row">
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
                                <button type="button" class="icon-btn-sm" aria-label="Edit <?= htmlspecialchars($stage['name']) ?>"><?= renderIcon('pencil') ?></button>
                                <button type="button" class="icon-btn-sm icon-btn-sm--danger" aria-label="Delete <?= htmlspecialchars($stage['name']) ?>"><?= renderIcon('trash-2') ?></button>
                                <button type="button" class="icon-btn-sm stage-row__expand-toggle" aria-expanded="<?= $isCurrent ? 'true' : 'false' ?>" aria-label="<?= $isCurrent ? 'Collapse' : 'Expand' ?> <?= htmlspecialchars($stage['name']) ?> tasks">
                                    <?= renderIcon('chevron-down') ?>
                                </button>
                            </div>
                        </div>

                        <?php
                            $taskColumns = computeTaskColumns($stage['tasks']);
                            $taskDomId = [];
                            foreach ($stage['tasks'] as $ti => $t) {
                                $taskDomId[$t['name']] = "task-{$i}-{$ti}";
                            }
                        ?>
                        <div class="stage-expand" <?= $isCurrent ? '' : 'hidden' ?>>
                            <div class="task-strip" data-task-strip>
                                <svg class="connector-layer" data-connector-layer></svg>

                                <?php foreach ($taskColumns as $column): ?>
                                    <div class="task-column">
                                        <?php foreach ($column as $task): ?>
                                            <?php
                                                $taskMeta = taskStatusMeta($task['status']);
                                                $isLocked = $task['status'] === 'locked';
                                                $shownAssignees = array_slice($task['assignees'], 0, 2);
                                                $extraCount = count($task['assignees']) - count($shownAssignees);

                                                $localDependIds = [];
                                                $crossStageDepends = [];
                                                foreach ($task['dependsOn'] as $depName) {
                                                    if (isset($taskDomId[$depName])) {
                                                        $localDependIds[] = $taskDomId[$depName];
                                                    } else {
                                                        $crossStageDepends[] = $depName;
                                                    }
                                                }
                                            ?>
                                            <div
                                                class="task-card <?= $isLocked ? 'task-card--locked' : '' ?>"
                                                id="<?= $taskDomId[$task['name']] ?>"
                                                <?php if ($localDependIds): ?>data-depends-ids="<?= htmlspecialchars(implode(',', $localDependIds)) ?>"<?php endif; ?>
                                            >
                                                <span class="badge badge--<?= $taskMeta['tone'] ?>">
                                                    <?php if ($taskMeta['icon']): ?><?= renderIcon($taskMeta['icon']) ?><?php endif; ?>
                                                    <?= htmlspecialchars($taskMeta['label']) ?>
                                                </span>

                                                <h4 class="task-card__name"><?= htmlspecialchars($task['name']) ?></h4>

                                                <?php if ($crossStageDepends): ?>
                                                    <div class="task-card__depends">
                                                        <?= renderIcon('lock') ?>
                                                        <span class="task-card__depends-text">Depends on: <?= htmlspecialchars(implode(', ', $crossStageDepends)) ?></span>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="task-card__footer">
                                                    <div class="task-card__assignees">
                                                        <?php if (empty($task['assignees'])): ?>
                                                            <span class="task-card__unassigned">Unassigned</span>
                                                        <?php else: ?>
                                                            <?php foreach ($shownAssignees as $person): ?>
                                                                <?php
                                                                    $personInitials = initials($person);
                                                                ?>
                                                                <span class="avatar avatar--sm avatar--<?= avatarColorClass($person) ?>" title="<?= htmlspecialchars($person) ?>"><?= htmlspecialchars($personInitials) ?></span>
                                                            <?php endforeach; ?>
                                                            <?php if ($extraCount > 0): ?>
                                                                <span class="task-card__more">+<?= $extraCount ?></span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="task-card__deadline"><?= htmlspecialchars(date('M j', strtotime($task['deadline']))) ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Task creation isn't wired up yet -->
                                <div class="task-card task-card--add">
                                    <?= renderIcon('plus') ?>
                                    <span>Add Task</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>