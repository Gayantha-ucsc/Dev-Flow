<?php
// Expects: $chat, $tasks, $stages, $chatPerms, $activeRole, $currentProjectName

$pendingRequests = array_filter($chat['accessRequests'] ?? [], fn($r) => ($r['status'] ?? '') === 'pending');
?>
<div class="chat-page">

    <div class="chat-header">
        <div>
            <h1>Project Chat</h1>
            <p>Communicate at project, stage, task, and client levels for <?= htmlspecialchars($currentProjectName ?? 'this project') ?>.</p>
        </div>
        <span class="role-chip"><?= renderIcon('user') ?> <?= htmlspecialchars($chatPerms['roleLabel']) ?> view</span>
    </div>

    <div class="chat-layout">

        <!-- Left: vertical room selector -->
        <aside class="chat-sidebar card" data-chat-perm="canViewProjectChat">
            <div class="chat-sidebar__section">
                <h3 class="chat-sidebar__label">Channels</h3>
                <button type="button" class="chat-room-btn is-active" data-room="project">
                    <?= renderIcon('chat') ?> Project Chat
                </button>
                <button type="button"
                        class="chat-room-btn"
                        data-room="client"
                        data-chat-perm="canViewClientChat"
                        <?= !$chatPerms['canViewClientChat'] ? 'disabled aria-disabled="true"' : '' ?>>
                    <?= renderIcon('user') ?> Client Chat Room
                    <?php if (!$chatPerms['canViewClientChat']): ?>
                        <span class="chat-room-btn__lock"><?= renderIcon('ban') ?></span>
                    <?php endif; ?>
                </button>
            </div>

            <div class="chat-sidebar__section" data-chat-perm="canViewStageChat">
                <h3 class="chat-sidebar__label">Stage Chat</h3>
                <?php if (empty($stages)): ?>
                    <p class="chat-sidebar__empty">No stages defined.</p>
                <?php else: ?>
                    <select id="stageChatSelect" class="chat-select" aria-label="Select stage">
                        <?php foreach ($stages as $stage): ?>
                            <option value="<?= htmlspecialchars($stage['name']) ?>"><?= htmlspecialchars($stage['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="chat-room-btn" data-room="stage" id="openStageChat">
                        Open Stage Chat
                    </button>
                <?php endif; ?>
            </div>

            <div class="chat-sidebar__section" data-chat-perm="canViewTaskChat">
                <h3 class="chat-sidebar__label">Task Chat</h3>
                <?php if (empty($tasks)): ?>
                    <p class="chat-sidebar__empty">No tasks yet.</p>
                <?php else: ?>
                    <select id="taskChatSelect" class="chat-select" aria-label="Select task">
                        <?php foreach ($tasks as $t): ?>
                            <option value="<?= (int) $t['id'] ?>"><?= htmlspecialchars($t['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="chat-room-btn" data-room="task" id="openTaskChat">
                        Open Task Chat
                    </button>
                <?php endif; ?>
            </div>

            <?php if (!empty($chat['accessRequests'])): ?>
            <div class="chat-sidebar__section">
                <h3 class="chat-sidebar__label">Access Requests</h3>
                <?php foreach ($chat['accessRequests'] as $req): ?>
                    <div class="chat-access-item chat-access-item--<?= htmlspecialchars($req['status']) ?>">
                        <div class="chat-access-item__name"><?= htmlspecialchars($req['requester']) ?></div>
                        <div class="chat-access-item__meta"><?= htmlspecialchars($req['taskTitle']) ?></div>
                        <span class="badge badge--<?= ($req['status'] ?? '') === 'approved' ? 'success' : 'warning' ?>">
                            <?= ucfirst($req['status'] ?? 'pending') ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </aside>

        <!-- Center: active conversation -->
        <div class="chat-main card">
            <div class="chat-main__header">
                <div>
                    <h2 class="chat-main__title" id="chatRoomTitle">Project Chat</h2>
                    <span class="chat-main__subtitle" id="chatRoomSubtitle">All team members</span>
                </div>
                <span class="chat-perm-badge" id="chatRoomBadge">Team Lead led</span>
            </div>

            <div class="chat-locked-banner" id="clientLockedBanner" hidden>
                <span class="chat-locked-banner__icon"><?= renderIcon('ban') ?></span>
                <div class="chat-locked-banner__body">
                    <strong class="chat-locked-banner__title">Client Chat access required</strong>
                    <p class="chat-locked-banner__text">Contributors need joint approval from Manager and Team Lead (and Client when applicable) to join client conversations.</p>
                </div>
                <div class="chat-locked-banner__action">
                    <button type="button" class="btn-sm btn-sm--primary" id="requestClientAccess">Request Access</button>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages" aria-live="polite"></div>

            <form class="chat-composer" id="chatComposer">
                <textarea name="message" rows="2" placeholder="Type a message..." required></textarea>
                <button type="submit" class="btn-sm btn-sm--primary">Send</button>
            </form>
        </div>

        <!-- Right: client access management -->
        <aside class="card chat-access-panel" id="chat-access" data-chat-perm="canApproveClientAccess">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('user-check') ?> Client Chat Access</h2>
                <span class="chat-perm-badge chat-perm-badge--joint">Manager + TL</span>
            </div>
            <div class="chat-access-panel__body">
                <p class="chat-access-panel__intro">Contributors request access; Manager and Team Lead approve jointly.</p>

                <div class="chat-access-block">
                    <h3 class="chat-access-block__title">Current Access</h3>
                    <?php if (empty($chat['clientAccess'])): ?>
                        <div class="chat-access-empty">
                            <p>No participants listed yet.</p>
                        </div>
                    <?php else: ?>
                        <ul class="chat-access-roster">
                            <?php foreach ($chat['clientAccess'] as $entry): ?>
                                <li class="chat-access-roster__item">
                                    <span class="avatar avatar--<?= avatarColorClass($entry['name']) ?>">
                                        <?= htmlspecialchars(strtoupper(substr($entry['name'], 0, 1))) ?>
                                    </span>
                                    <div class="chat-access-roster__body">
                                        <div class="chat-access-roster__name"><?= htmlspecialchars($entry['name']) ?></div>
                                        <div class="chat-access-roster__role"><?= memberRoleLabel($entry['role']) ?></div>
                                    </div>
                                    <span class="badge badge--<?= ($entry['access'] ?? '') === 'always' ? 'primary' : 'success' ?>">
                                        <?= ($entry['access'] ?? '') === 'always' ? 'Always' : 'Approved' ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <?php if (!empty($pendingRequests)): ?>
                <div class="chat-access-block">
                    <h3 class="chat-access-block__title">Pending Requests</h3>
                    <?php foreach ($pendingRequests as $req): ?>
                        <div class="chat-access-request" data-request-id="<?= (int) $req['id'] ?>">
                            <div class="chat-access-request__header">
                                <strong><?= htmlspecialchars($req['requester']) ?></strong>
                                <span class="badge badge--<?= memberRoleTone($req['requesterRole']) ?>"><?= memberRoleLabel($req['requesterRole']) ?></span>
                            </div>
                            <p class="chat-access-request__task">Task: <?= htmlspecialchars($req['taskTitle']) ?></p>
                            <p class="chat-access-request__approvers">Requires: <?= htmlspecialchars(implode(' + ', $req['approvers'])) ?></p>
                            <?php if ($chatPerms['canApproveClientAccess']): ?>
                                <div class="chat-access-request__actions">
                                    <button type="button" class="btn-sm btn-sm--text-danger js-deny-access">Deny</button>
                                    <button type="button" class="btn-sm btn-sm--primary js-grant-access">Grant</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($chatPerms['canRequestClientAccess']): ?>
                <div class="chat-access-block" data-chat-perm="canRequestClientAccess">
                    <h3 class="chat-access-block__title">Request Access</h3>
                    <form class="chat-access-form" id="accessRequestForm">
                        <div class="form-group">
                            <label for="accessTask">Related Task</label>
                            <select id="accessTask" name="task_id">
                                <?php foreach ($tasks as $t): ?>
                                    <option value="<?= (int) $t['id'] ?>"><?= htmlspecialchars($t['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="accessReason">Reason</label>
                            <textarea id="accessReason" name="reason" rows="3" placeholder="Why do you need client chat access?" required></textarea>
                        </div>
                        <button type="submit" class="btn-sm btn-sm--primary">Submit Request</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<script type="application/json" id="chatData"><?= json_encode($chat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?></script>
