<?php
// Expects: $tasks, $stages, $currentProjectName, $taskStats, $taskPerms, $activeRole
?>
<div class="tasks-page">

    <div class="tasks-sticky">
        <div class="tasks-header">
            <div>
                <h1>Tasks</h1>
                <p>Track and manage work across stages for <?= htmlspecialchars($currentProjectName ?? 'this project') ?>.</p>
            </div>
            <div class="tasks-header__right">
                <span class="tasks-header__role role-chip"><?= renderIcon('user') ?> <?= htmlspecialchars($taskPerms['roleLabel']) ?> view</span>
                <?php if ($taskPerms['canCreateTask']): ?>
                    <a href="<?= url('tasks/create') ?>" class="btn-add-member"><?= renderIcon('plus') ?> Create Task</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($tasks)): ?>
        <div class="tasks-toolbar">
            <div class="projects-search">
                <?= renderIcon('search') ?>
                <input type="text" id="taskSearch" placeholder="Search tasks...">
            </div>
            <div class="projects-filters" id="taskStatusFilters">
                <button type="button" class="filter-pill is-active" data-filter="all">All</button>
                <button type="button" class="filter-pill" data-filter="not_started">Not Started</button>
                <button type="button" class="filter-pill" data-filter="in_progress">In Progress</button>
                <button type="button" class="filter-pill" data-filter="blocked">Blocked</button>
                <button type="button" class="filter-pill" data-filter="pending_review">Review</button>
                <button type="button" class="filter-pill" data-filter="completed">Completed</button>
            </div>
            <div class="projects-sort">
                <label for="stageFilter">Stage:</label>
                <select id="stageFilter">
                    <option value="all">All Stages</option>
                    <?php foreach ($stages as $stage): ?>
                        <option value="<?= htmlspecialchars($stage['name']) ?>"><?= htmlspecialchars($stage['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if (empty($tasks)): ?>
        <?php
            $variant  = 'card';
            $icon     = 'tasks';
            $heading  = 'No tasks yet';
            $subtext  = 'Create your first task to start tracking work across project stages.';
            $ctaText  = 'Create Task';
            $ctaHref  = 'tasks/create';
        ?>
        <?php include __DIR__ . '/../partials/empty-state.php'; ?>
    <?php else: ?>

        <div class="dash-stats tasks-stats">
            <?php foreach ($taskStats as $stat): ?>
                <div class="card stat-card<?= !empty($stat['tone']) ? ' stat-card--' . htmlspecialchars($stat['tone']) : '' ?>">
                    <div class="stat-card__top">
                        <span class="stat-card__label"><?= htmlspecialchars($stat['label']) ?></span>
                        <span class="stat-card__icon"><?= renderIcon($stat['icon']) ?></span>
                    </div>
                    <div class="stat-card__value"><?= (int) $stat['value'] ?></div>
                    <div class="stat-card__meta"><?= htmlspecialchars($stat['meta']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="task-list" id="taskList">
            <?php foreach ($tasks as $task): ?>
                <a href="<?= url('tasks/' . $task['id']) ?>"
                   class="card task-row"
                   data-title="<?= htmlspecialchars(strtolower($task['title'])) ?>"
                   data-status="<?= htmlspecialchars($task['status']) ?>"
                   data-stage="<?= htmlspecialchars($task['stage']) ?>">
                    <div class="task-row__info">
                        <div class="task-row__title-line">
                            <h2 class="task-row__name"><?= htmlspecialchars($task['title']) ?></h2>
                            <span class="badge badge--<?= taskStatusTone($task['status']) ?> badge--outline">
                                <?= strtoupper(str_replace('_', ' ', $task['status'])) ?>
                            </span>
                        </div>
                        <p class="task-row__description"><?= htmlspecialchars(strlen($task['description']) > 90 ? substr($task['description'], 0, 90) . '…' : $task['description']) ?></p>
                        <span class="role-chip"><?= renderIcon('flag') ?> <?= htmlspecialchars($task['stage']) ?></span>
                    </div>

                    <div class="task-row__center">
                        <div class="task-row__assignee-line">
                            <span>Assignees</span>
                            <div class="task-row__avatars">
                                <?php foreach ($task['assignees'] as $a): ?>
                                    <span class="avatar avatar--<?= avatarColorClass($a['name']) ?>" title="<?= htmlspecialchars($a['name']) ?>">
                                        <?= htmlspecialchars(strtoupper(substr($a['name'], 0, 1))) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="task-row__badges">
                            <span class="badge badge--<?= taskPriorityTone($task['priority']) ?> badge--outline">
                                <?= ucfirst($task['priority']) ?> Priority
                            </span>
                            <?php if ($task['status'] === 'blocked'): ?>
                                <span class="badge badge--danger badge--outline"><?= renderIcon('ban') ?> Blocked</span>
                            <?php endif; ?>
                            <?php if (!empty($task['dependencies'])): ?>
                                <span class="badge badge--neutral badge--outline"><?= count($task['dependencies']) ?> deps</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="task-row__meta">
                        <div class="task-row__meta-label">Due Date</div>
                        <div class="task-row__meta-value"><?= htmlspecialchars(date('M j, Y', strtotime($task['dueDate']))) ?></div>
                        <span class="task-row__arrow"><?= renderIcon('arrow-up-right') ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <p class="task-list__empty" id="taskListEmpty" hidden>No tasks match your filters.</p>

    <?php endif; ?>
</div>