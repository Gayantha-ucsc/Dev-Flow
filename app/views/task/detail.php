<?php
// Expects: $task, $allTasks, $stages, $teamMembers, $taskPerms
$prerequisites = array_filter($allTasks, fn($t) => in_array($t['id'], $task['dependencies'] ?? [], true));
$dependents    = array_filter($allTasks, fn($t) => in_array($task['id'], $t['dependencies'] ?? [], true));

function renderCommentThread(array $comments, ?int $parentId = null): void {
    $children = array_filter($comments, fn($c) => ($c['parentId'] ?? null) === $parentId);
    if (empty($children)) return;
    echo '<div class="comment-thread' . ($parentId ? ' comment-thread--nested' : '') . '">';
    foreach ($children as $comment) {
        ?>
        <div class="comment-item">
            <span class="avatar avatar--<?= avatarColorClass($comment['author']) ?>">
                <?= htmlspecialchars(strtoupper(substr($comment['author'], 0, 1))) ?>
            </span>
            <div class="comment-item__body">
                <div class="comment-item__header">
                    <strong><?= htmlspecialchars($comment['author']) ?></strong>
                    <time><?= htmlspecialchars($comment['createdAt']) ?></time>
                </div>
                <p><?= htmlspecialchars($comment['body']) ?></p>
                <button type="button" class="btn-sm comment__reply" data-parent-id="<?= (int) $comment['id'] ?>">Reply</button>
            </div>
        </div>
        <?php
        renderCommentThread($comments, $comment['id']);
    }
    echo '</div>';
}
?>
<div class="tasks-page task-detail-page">

    <div class="tasks-sticky tasks-sticky--detail">
        <a href="<?= url('tasks') ?>" class="back-link"><?= renderIcon('arrow-left') ?> Back to Tasks</a>
        <div class="task-detail-hero">
            <div class="task-detail-hero__main">
                <div class="task-detail-hero__title-line">
                    <h1><?= htmlspecialchars($task['title']) ?></h1>
                    <span class="badge badge--<?= taskStatusTone($task['status']) ?> badge--outline">
                        <?= taskStatusLabel($task['status']) ?>
                    </span>
                    <span class="badge badge--<?= taskPriorityTone($task['priority']) ?> badge--outline">
                        <?= ucfirst($task['priority']) ?>
                    </span>
                </div>
                <p class="task-detail-hero__meta">
                    <?= htmlspecialchars($task['stage']) ?> &bull; Due <?= htmlspecialchars(date('M j, Y', strtotime($task['dueDate']))) ?>
                    &bull; <span class="role-chip"><?= renderIcon('user') ?> <?= htmlspecialchars($taskPerms['roleLabel']) ?> view</span>
                </p>
            </div>
            <div class="task-detail-hero__actions">
                <a href="<?= url('chat') ?>?room=task&task=<?= (int) $task['id'] ?>" class="btn-outline"><?= renderIcon('chat') ?> Task Chat</a>
                <?php if ($taskPerms['canEditTask']): ?>
                    <a href="<?= url('tasks/' . $task['id'] . '/edit') ?>" class="btn-outline"><?= renderIcon('pencil') ?> Edit</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="task-detail-layout">

        <!-- LEFT: task management (Team Lead permissions) -->
        <div class="task-detail-main">

            <div class="card">
                <div class="card__header">
                    <h2 class="card__title"><?= renderIcon('tasks') ?> Description</h2>
                </div>
                <div class="task-panel-body">
                    <p class="task-description"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                </div>
            </div>

            <!-- PERM: canUpdateStatus / canSetAnyStatus -->
            <div class="card task-section" data-task-perm="canUpdateStatus">
                <div class="card__header">
                    <h2 class="card__title"><?= renderIcon('loader-circle') ?> Status</h2>
                    <?php if ($taskPerms['canSetAnyStatus']): ?>
                        <span class="task-perm-badge">Team Lead</span>
                    <?php endif; ?>
                </div>
                <div class="task-panel-body">
                    <div class="status-pills" id="statusControls">
                        <?php foreach (['not_started', 'in_progress', 'blocked', 'pending_review', 'completed'] as $s): ?>
                            <button type="button"
                                    class="filter-pill status-pill <?= $task['status'] === $s ? 'is-active' : '' ?>"
                                    data-status="<?= $s ?>">
                                <?= taskStatusLabel($s) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- PERM: canAssign -->
            <div class="card task-section" data-task-perm="canAssign">
                <div class="card__header">
                    <h2 class="card__title"><?= renderIcon('user') ?> Assignees</h2>
                    <span class="task-perm-badge">Team Lead</span>
                    <?php if ($taskPerms['canAssign']): ?>
                        <button type="button" class="btn-sm" id="openAssignPanel">Reassign</button>
                    <?php endif; ?>
                </div>
                <div class="task-panel-body">
                    <div class="assignee-list" id="assigneeList">
                        <?php foreach ($task['assignees'] as $a): ?>
                            <div class="assignee-chip">
                                <span class="avatar avatar--<?= avatarColorClass($a['name']) ?>">
                                    <?= htmlspecialchars(strtoupper(substr($a['name'], 0, 1))) ?>
                                </span>
                                <?= htmlspecialchars($a['name']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="task-inline-panel" id="assignPanel" hidden>
                        <p class="form-hint">Select multiple assignees for this task.</p>
                        <div class="picker-list">
                            <?php foreach ($teamMembers as $member):
                                if ($member['role'] === 'client') continue;
                                $checked = in_array($member['user_id'], array_column($task['assignees'], 'user_id'), true);
                            ?>
                                <label class="picker-item">
                                    <input type="checkbox" name="detailAssignees[]" value="<?= (int) $member['user_id'] ?>" <?= $checked ? 'checked' : '' ?>>
                                    <span class="avatar avatar--<?= avatarColorClass($member['name']) ?>"><?= htmlspecialchars(strtoupper(substr($member['name'], 0, 1))) ?></span>
                                    <span class="picker-item__label"><?= htmlspecialchars($member['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="task-inline-panel__actions">
                            <button type="button" class="btn-sm" id="cancelAssign">Cancel</button>
                            <button type="button" class="btn-sm btn-sm--primary" id="saveAssign">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PERM: canManageDependencies -->
            <div class="card task-section" data-task-perm="canManageDependencies">
                <div class="card__header">
                    <h2 class="card__title"><?= renderIcon('grip-vertical') ?> Dependencies</h2>
                    <span class="task-perm-badge">Team Lead</span>
                    <?php if ($taskPerms['canManageDependencies']): ?>
                        <button type="button" class="btn-sm" id="openDepPanel">Manage</button>
                    <?php endif; ?>
                </div>
                <div class="task-panel-body">
                    <div class="dep-block">
                        <h3 class="dep-block__label">Prerequisites</h3>
                        <?php if (empty($prerequisites)): ?>
                            <p class="form-hint">No prerequisite tasks.</p>
                        <?php else: ?>
                            <ul class="dep-links">
                                <?php foreach ($prerequisites as $dep): ?>
                                    <li>
                                        <a href="<?= url('tasks/' . $dep['id']) ?>"><?= htmlspecialchars($dep['title']) ?></a>
                                        <span class="badge badge--<?= taskStatusTone($dep['status']) ?>"><?= taskStatusLabel($dep['status']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($dependents)): ?>
                    <div class="dep-block">
                        <h3 class="dep-block__label">Blocks</h3>
                        <ul class="dep-links">
                            <?php foreach ($dependents as $dep): ?>
                                <li><a href="<?= url('tasks/' . $dep['id']) ?>"><?= htmlspecialchars($dep['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <div class="task-inline-panel" id="depPanel" hidden>
                        <p class="form-hint">Select prerequisite tasks.</p>
                        <div class="picker-list">
                            <?php foreach ($allTasks as $opt):
                                if ($opt['id'] === $task['id']) continue;
                                $depChecked = in_array($opt['id'], $task['dependencies'] ?? [], true);
                            ?>
                                <label class="picker-item">
                                    <input type="checkbox" name="detailDependencies[]" value="<?= (int) $opt['id'] ?>" <?= $depChecked ? 'checked' : '' ?>>
                                    <span class="picker-item__label"><?= htmlspecialchars($opt['title']) ?></span>
                                    <span class="badge badge--<?= taskStatusTone($opt['status']) ?>"><?= taskStatusLabel($opt['status']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="task-inline-panel__actions">
                            <button type="button" class="btn-sm" id="cancelDep">Cancel</button>
                            <button type="button" class="btn-sm btn-sm--primary" id="saveDep">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: collaboration (Contributor + Team Lead) -->
        <div class="task-detail-side">

            <div class="task-side-tabs" id="taskTabs">
                <button type="button" class="task-side-tab is-active" data-tab="notes">Progress Notes</button>
                <button type="button" class="task-side-tab" data-tab="comments">Comments</button>
                <button type="button" class="task-side-tab" data-tab="revisions">Revisions</button>
            </div>

            <!-- PERM: canAddProgressNote -->
            <div class="card task-tab-panel is-active task-section" data-panel="notes" data-task-perm="canAddProgressNote">
                <div class="card__header">
                    <h2 class="card__title"><?= renderIcon('clock') ?> Progress Notes</h2>
                    <span class="task-perm-badge task-perm-badge--shared">Shared</span>
                </div>
                <div class="task-panel-body">
                    <form class="task-compose" id="noteForm">
                        <textarea name="note" rows="3" placeholder="Add a progress update..." required></textarea>
                        <button type="submit" class="btn-sm btn-sm--primary">Add Note</button>
                    </form>
                    <ul class="note-feed" id="noteList">
                        <?php foreach (array_reverse($task['progressNotes']) as $note): ?>
                            <li class="note-feed__item">
                                <span class="note-feed__dot"></span>
                                <div class="note-feed__time"><?= htmlspecialchars($note['createdAt']) ?></div>
                                <div class="note-feed__author"><?= htmlspecialchars($note['author']) ?></div>
                                <div class="note-feed__text"><?= htmlspecialchars($note['body']) ?></div>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($task['progressNotes'])): ?>
                            <li class="note-feed__empty form-hint">No progress notes yet.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- PERM: canComment -->
            <div class="card task-tab-panel task-section" data-panel="comments" data-task-perm="canComment" hidden>
                <div class="card__header">
                    <h2 class="card__title"><?= renderIcon('chat') ?> Comments</h2>
                    <span class="task-perm-badge task-perm-badge--shared">Shared</span>
                </div>
                <div class="task-panel-body">
                    <form class="task-compose" id="commentForm">
                        <input type="hidden" name="parentId" id="commentParentId" value="">
                        <textarea name="comment" rows="3" placeholder="Write a comment..." required></textarea>
                        <div class="task-compose__actions">
                            <button type="button" class="btn-sm" id="cancelReply" hidden>Cancel Reply</button>
                            <button type="submit" class="btn-sm btn-sm--primary">Post</button>
                        </div>
                    </form>
                    <div class="comment-feed" id="commentList">
                        <?php if (empty($task['comments'])): ?>
                            <p class="form-hint">No comments yet.</p>
                        <?php else: ?>
                            <?php renderCommentThread($task['comments']); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- PERM: canViewRevisions / canManageRevisions -->
            <div class="card task-tab-panel task-section" data-panel="revisions" data-task-perm="canViewRevisions" hidden>
                <div class="card__header">
                    <h2 class="card__title"><?= renderIcon('review') ?> Revision Rounds</h2>
                    <?php if ($taskPerms['canManageRevisions']): ?>
                        <span class="task-perm-badge">Team Lead</span>
                    <?php endif; ?>
                </div>
                <div class="task-panel-body">
                    <?php if (empty($task['revisionRounds'])): ?>
                        <p class="form-hint">No revision rounds recorded.</p>
                    <?php else: ?>
                        <ul class="revision-feed">
                            <?php foreach ($task['revisionRounds'] as $round): ?>
                                <?php
                                    $roundTone = match ($round['status']) {
                                        'approved'  => 'success',
                                        'in_review' => 'warning',
                                        'rejected'  => 'danger',
                                        default     => 'neutral',
                                    };
                                ?>
                                <li class="revision-feed__item">
                                    <div class="revision-feed__top">
                                        <strong>Round <?= (int) $round['round'] ?></strong>
                                        <span class="badge badge--<?= $roundTone ?>"><?= ucfirst(str_replace('_', ' ', $round['status'])) ?></span>
                                    </div>
                                    <div class="revision-feed__meta">
                                        Submitted <?= htmlspecialchars($round['submittedAt']) ?>
                                        <?php if ($round['reviewedAt']): ?>
                                            &bull; Reviewed by <?= htmlspecialchars($round['reviewer']) ?>
                                        <?php else: ?>
                                            &bull; Awaiting <?= htmlspecialchars($round['reviewer']) ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($round['notes'])): ?>
                                        <p class="revision-feed__notes"><?= htmlspecialchars($round['notes']) ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>