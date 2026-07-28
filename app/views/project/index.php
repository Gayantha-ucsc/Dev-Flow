<?php
// Expects: $projectsList (see config/mock/projects-list.php)
$statusToneMap = [
    'active'   => 'primary',
    'archived' => 'neutral',
    'closed'   => 'pink',
];
?>
<div class="projects-page">

    <div class="projects-header">
        <h1>Projects</h1>
        <p>Manage and track every project you're a part of.</p>
    </div>

    <?php if (empty($projectsList)): ?>
        <?php include __DIR__ . '/../partials/empty-state.php'; ?>
    <?php else: ?>

        <?php if (count($projectsList) > 1): ?>
        <div class="projects-toolbar">
            <div class="projects-search">
                <?= renderIcon('search') ?>
                <input type="text" id="projectSearch" placeholder="Search projects...">
            </div>
            <div class="projects-filters" id="projectFilters">
                <button type="button" class="filter-pill is-active" data-filter="all">All</button>
                <button type="button" class="filter-pill" data-filter="active">Active</button>
                <button type="button" class="filter-pill" data-filter="archived">Archived</button>
                <button type="button" class="filter-pill" data-filter="closed">Closed</button>
            </div>
            <div class="projects-sort">
                <label for="projectSort">Sort by:</label>
                <select id="projectSort">
                    <option value="updated">Recently Updated</option>
                    <option value="name">Name (A-Z)</option>
                    <option value="deadline">Deadline (Soonest)</option>
                </select>
            </div>
        </div>
        <?php endif; ?>

        <div class="project-list" id="projectList">
            <?php foreach ($projectsList as $project): ?>
                <?php
                    $isApprover = (bool) array_intersect($project['roles'], ['manager', 'team_lead']);
                    $stages     = $project['stages'];
                    $roleLabel  = implode(' + ', array_map(
                        fn($r) => ucwords(str_replace('_', ' ', $r)),
                        $project['roles']
                    ));
                ?>
                <a href="<?= url('projects/' . $project['id']) ?>"
                   class="card project-row"
                   data-name="<?= htmlspecialchars(strtolower($project['name'])) ?>"
                   data-status="<?= htmlspecialchars($project['status']) ?>"
                   data-deadline="<?= htmlspecialchars($project['deadline']) ?>">

                    <div class="project-row__info">
                        <div class="project-row__title-line">
                            <h2 class="project-row__name"><?= htmlspecialchars($project['name']) ?></h2>
                            <span class="badge badge--<?= $statusToneMap[$project['status']] ?> badge--outline">
                                <?= strtoupper($project['status']) ?>
                            </span>
                        </div>
                        <p class="project-row__description"><?= htmlspecialchars($project['description']) ?></p>
                        <span class="role-chip"><?= renderIcon('user') ?> <?= htmlspecialchars($roleLabel) ?></span>
                    </div>

                    <div class="project-row__progress">
                        <div class="project-row__stage-line">
                            <span><?= htmlspecialchars($project['stageLabel']) ?></span>
                            <strong><?= (int) $project['percent'] ?>%</strong>
                        </div>
                        <div class="project-row__pipeline">
                            <?php include __DIR__ . '/../partials/stage-pipeline.php'; ?>
                        </div>

                        <?php if ($isApprover && ($project['pendingCount'] || $project['overdueCount'] || $project['blockedCount'])): ?>
                            <div class="project-row__badges">
                                <?php if ($project['pendingCount'] > 0): ?>
                                    <span class="badge badge--primary badge--outline"><?= renderIcon('clock') ?> <?= (int) $project['pendingCount'] ?> Pending</span>
                                <?php endif; ?>
                                <?php if ($project['overdueCount'] > 0): ?>
                                    <span class="badge badge--danger badge--outline"><?= renderIcon('circle-alert') ?> <?= (int) $project['overdueCount'] ?> Overdue</span>
                                <?php endif; ?>
                                <?php if ($project['blockedCount'] > 0): ?>
                                    <span class="badge badge--pink badge--outline"><?= renderIcon('ban') ?> <?= (int) $project['blockedCount'] ?> Blocked</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="project-row__meta">
                        <div class="project-row__meta-label">Deadline</div>
                        <div class="project-row__meta-value<?= $project['overdueCount'] > 0 ? ' project-row__meta-value--warning' : '' ?>">
                            <?= htmlspecialchars(date('M j, Y', strtotime($project['deadline']))) ?>
                        </div>
                        <span class="project-row__arrow"><?= renderIcon('arrow-up-right') ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (count($projectsList) > 1): ?>
        <div class="projects-pagination">
            <a href="#">Previous</a>
            <span class="projects-pagination__page is-active">1</span>
            <a href="#"><span class="projects-pagination__page">2</span></a>
            <a href="#">Next</a>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>