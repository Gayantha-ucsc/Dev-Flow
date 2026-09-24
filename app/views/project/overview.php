<?php
// Expects: 
    // $project (one row from config/mock/projects-list.php),
    // $stages (config/mock/project-detail.php)
    // $members (config/mock/team-members.php)

$allProjectTasks = [];
$allTaskTypes = [];
foreach ($stages as $stage) {
    foreach ($stage['tasks'] as $task) {
        $allProjectTasks[] = [
            'name'      => $task['name'],
            'stageName' => $stage['name'],
            'status'    => $task['status'],
        ];
        if (!empty($task['type'])) {
            $allTaskTypes[$task['type']] = true;
        }
    }
}
$allTaskTypes = array_keys($allTaskTypes);
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

    <div class="project-overview__workflow" data-workflow-section>
        <div class="project-overview__workflow-header">
            <h2>Project Workflow</h2>
            <?php if (!empty($stagesEditable)): ?>
                <form method="post" action="<?= url('projects/' . $project['id'] . '/stages') ?>" data-stage-form>
                    <?= csrfField() ?>
                    <input type="hidden" name="name" value="New Stage">
                    <button type="submit" class="btn-add-stage" data-add-stage><?= renderIcon('plus') ?> Add Stage</button>
                </form>
            <?php else: ?>
                <button type="button" class="btn-add-stage" data-add-stage><?= renderIcon('plus') ?> Add Stage</button>
            <?php endif; ?>
        </div>

        <?php if (empty($stages)): ?>
            <?php
                $icon     = 'layout-template';
                $heading  = 'No stages yet';
                $subtext  = 'Add a stage above, or pick a workflow template to get started.';
                $variant  = 'card';
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
                        $isCurrent = in_array($stage['status'], ['in_progress', 'pending_completion'], true);
                    ?>
                    <div class="card stage-item <?= $isCurrent ? 'stage-item--current stage-item--expanded' : '' ?>" data-stage-name="<?= htmlspecialchars($stage['name']) ?>" <?php if (!empty($stage['stage_id'])): ?>data-stage-id="<?= (int) $stage['stage_id'] ?>"<?php endif; ?> draggable="false">
                        <div class="stage-row">
                            <button type="button" class="stage-row__reorder" aria-label="Reorder <?= htmlspecialchars($stage['name']) ?>">
                                <?= renderIcon('chevrons-up-down') ?>
                            </button>

                            <span class="stage-row__number <?= $isCurrent ? 'stage-row__number--current' : '' ?>">
                                <?= $i + 1 ?>
                            </span>

                            <div class="stage-row__body">
                                <h3 class="stage-row__name" data-name-display><?= htmlspecialchars($stage['name']) ?></h3>
                                <?php if (!empty($stage['stage_id'])): ?>
                                    <form method="post" action="<?= url('stages/' . $stage['stage_id'] . '/update') ?>" data-rename-form>
                                        <?= csrfField() ?>
                                        <input type="text" name="name" class="stage-row__name-input" data-name-input value="<?= htmlspecialchars($stage['name']) ?>" draggable="false" hidden>
                                    </form>
                                <?php else: ?>
                                    <input type="text" class="stage-row__name-input" data-name-input value="<?= htmlspecialchars($stage['name']) ?>" draggable="false" hidden>
                                <?php endif; ?>
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
                                <button type="button" class="icon-btn-sm js-edit-stage" draggable="false" aria-label="Edit <?= htmlspecialchars($stage['name']) ?>"><?= renderIcon('pencil') ?></button>
                                <button type="button" class="icon-btn-sm icon-btn-sm--danger js-delete-stage" draggable="false" data-stage-name="<?= htmlspecialchars($stage['name']) ?>" data-task-count="<?= (int) $stage['tasksTotal'] ?>" aria-label="Delete <?= htmlspecialchars($stage['name']) ?>"><?= renderIcon('trash-2') ?></button>
                                <button type="button" class="icon-btn-sm stage-row__expand-toggle" draggable="false" aria-expanded="<?= $isCurrent ? 'true' : 'false' ?>" aria-label="<?= $isCurrent ? 'Collapse' : 'Expand' ?> <?= htmlspecialchars($stage['name']) ?> tasks">
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

                                <div class="task-card task-card--add js-add-task" data-stage-name="<?= htmlspecialchars($stage['name']) ?>">
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

<script>
    window.STAGE_BASE_URL = <?= json_encode(url('stages')) ?>;
    window.STAGE_CSRF     = <?= json_encode(csrfToken()) ?>;

    window.STAGE_ICONS = {
        grip:        <?= iconJson('grip-vertical') ?>,
        pencil:      <?= iconJson('pencil') ?>,
        trash:       <?= iconJson('trash-2') ?>,
        chevronDown: <?= iconJson('chevron-down') ?>,
        plus:        <?= iconJson('plus') ?>,
        chevrons:    <?= iconJson('chevrons-up-down') ?>
    };

    window.TASK_STATUS_META = {
        locked:      { tone: 'slate',   icon: <?= iconJson('lock') ?>,          label: 'Locked' },
        not_started: { tone: 'neutral', icon: <?= iconJson('circle-dashed') ?>, label: 'Not Started' }
    };
    window.TASK_ICONS = {
        lock: <?= iconJson('lock') ?>
    };
</script>

<!-- Delete Stage confirmation -->
<div class="modal-overlay" data-modal="delete-stage-modal" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Delete stage</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">
                Delete <strong data-delete-stage-name></strong>? This removes the stage and cannot be undone.
                <span data-delete-stage-task-warning hidden> It still has tasks in it.</span>
            </p>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                <form method="post" id="deleteStageForm" data-delete-form>
                    <?= csrfField() ?>
                    <button type="submit" class="btn-sm btn-sm--danger-outline" id="confirmDeleteStage">Delete stage</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Create Task -->
<div class="side-panel-overlay" data-panel="create-task-panel" hidden>
    <div class="side-panel-backdrop" data-panel-close></div>
    <div class="side-panel">
        <div class="side-panel__header">
            <div>
                <h2>Create task</h2>
                <div class="side-panel__stage-line">
                    STAGE: <span class="badge badge--primary" data-task-stage-badge></span>
                </div>
            </div>
            <button type="button" class="icon-btn" data-panel-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>

        <div class="side-panel__body">
            <div class="form-group">
                <label for="taskName">Task name <span class="required">*</span></label>
                <input type="text" id="taskName" placeholder="e.g. Implement User Authentication Middleware">
                <span class="field-error" data-error-for="taskName"></span>
            </div>

            <div class="form-group">
                <div class="form-group__label-row">
                    <label for="taskDescription">Description</label>
                    <span class="form-group__hint">Markdown supported</span>
                </div>
                <textarea id="taskDescription" rows="4" placeholder="Describe what this task involves..."></textarea>
            </div>

            <div class="form-group">
                <label for="taskType">Task type</label>
                <input type="text" id="taskType" list="taskTypeList" placeholder="e.g. backend">
                <datalist id="taskTypeList">
                    <?php foreach ($allTaskTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>">
                    <?php endforeach; ?>
                </datalist>

                <div class="tag-suggestions">
                    <span class="tag-suggestions__label">Suggestions</span>
                    <span class="tag-suggestions__hint">Type to add a new tag</span>
                </div>
                <div class="tag-suggestions__chips" data-tag-suggestions>
                    <?php foreach (array_slice($allTaskTypes, 0, 6) as $type): ?>
                        <button type="button" class="tag-chip" data-tag-chip="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></button>
                    <?php endforeach; ?>
                    <?php if (empty($allTaskTypes)): ?>
                        <button type="button" class="tag-chip" data-tag-chip="frontend">frontend</button>
                        <button type="button" class="tag-chip" data-tag-chip="backend">backend</button>
                        <button type="button" class="tag-chip" data-tag-chip="design">design</button>
                        <button type="button" class="tag-chip" data-tag-chip="testing">testing</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Deadline <span class="required">*</span></label>
                <div class="task-deadline-row">
                    <div class="dropdown date-field" data-dropdown data-datepicker>
                        <button type="button" class="date-field__input" data-dropdown-trigger data-date-trigger>
                            <?= renderIcon('calendar') ?>
                            <span data-date-display>mm/dd/yyyy</span>
                        </button>
                        <input type="hidden" id="taskDeadline" data-date-value>
                        <div class="dropdown__menu dropdown__menu--calendar" data-dropdown-menu data-date-calendar></div>
                    </div>
                    <input type="time" id="taskDeadlineTime" class="task-deadline-row__time" value="14:00">
                </div>
                <span class="field-error" data-error-for="taskDeadline"></span>
            </div>

            <div class="form-group">
                <div class="form-group__label-row">
                    <label>Assignees</label>
                    <span class="form-group__hint" data-assignee-count>0 assigned</span>
                </div>
                <div class="assignee-chips" data-assignee-chips></div>
                <div class="dropdown" data-dropdown>
                    <button type="button" class="assignee-add-btn" data-dropdown-trigger>
                        <?= renderIcon('user-plus') ?> Add assignee
                    </button>
                    <div class="dropdown__menu assignee-add-menu" data-dropdown-menu data-assignee-menu>
                        <?php foreach ($members ?? [] as $member): ?>
                            <button type="button" class="assignee-option" data-assignee-option data-assignee-name="<?= htmlspecialchars($member['name']) ?>">
                                <span class="avatar avatar--sm avatar--<?= avatarColorClass($member['name']) ?>"><?= htmlspecialchars(initials($member['name'])) ?></span>
                                <?= htmlspecialchars($member['name']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-group__label-row">
                    <label>Depends on (optional)</label>
                </div>
                <div class="dependency-chips" data-dependency-chips></div>
                <div class="dropdown" data-dropdown>
                    <button type="button" class="dependency-add-btn" data-dropdown-trigger>
                        <?= renderIcon('user-plus') ?> Add dependency
                    </button>
                    <div class="dropdown__menu dependency-add-menu" data-dropdown-menu data-dependency-menu>
                        <?php foreach ($allProjectTasks as $t): ?>
                            <?php $tMeta = taskStatusMeta($t['status']); ?>
                            <button type="button" class="dependency-option" data-dependency-option
                                    data-dependency-name="<?= htmlspecialchars($t['name']) ?>"
                                    data-dependency-stage="<?= htmlspecialchars($t['stageName']) ?>">
                                <span class="dependency-option__name"><?= htmlspecialchars($t['name']) ?></span>
                                <span class="badge badge--<?= $tMeta['tone'] ?>"><?= htmlspecialchars($tMeta['label']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <p class="form-group__hint">This task will stay locked until all dependencies are approved.</p>
            </div>

            <div class="review-banner" data-circular-warning hidden>
                <?= renderIcon('info') ?>
                <div>
                    <strong>Circular dependency prevented</strong>
                    <p data-circular-warning-text></p>
                </div>
            </div>
        </div>

        <div class="side-panel__footer">
            <button type="button" class="btn-sm" data-panel-close>Cancel</button>
            <button type="button" class="btn-primary" id="submitCreateTask"><?= renderIcon('plus') ?> Create task</button>
        </div>
    </div>
</div>