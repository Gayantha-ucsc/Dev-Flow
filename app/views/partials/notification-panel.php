<?php
// Expects $notifications = [
//   ['message' => ..., 'icon' => ..., 'is_read' => bool, 'created_at' => 'x ago', 'href' => '/...'],
// ]
$notifications = $notifications ?? [];
$hasUnread = !empty(array_filter($notifications, fn($n) => !$n['is_read']));
?>
<div class="notif-panel">
    <div class="notif-panel__header">
        <span class="notif-panel__title">Notifications</span>
        <?php if ($hasUnread): ?>
            <a href="?mark_all_read=1" class="notif-panel__mark-read">Mark all as read</a>
        <?php endif; ?>
    </div>

    <div class="notif-panel__list">
        <?php if (empty($notifications)): ?>
            <div class="notif-panel__empty">
                <?= renderIcon('bell') ?>
                <p>You're all caught up</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <a href="<?= url($n['href'] ?? '/notifications') ?>"
                   class="notif-item <?= $n['is_read'] ? '' : 'is-unread' ?>">
                    <span class="notif-item__icon"><?= renderIcon($n['icon'] ?? 'bell') ?></span>
                    <span class="notif-item__body">
                        <span class="notif-item__message"><?= htmlspecialchars($n['message']) ?></span>
                        <span class="notif-item__time"><?= htmlspecialchars($n['created_at']) ?></span>
                    </span>
                    <?php if (!$n['is_read']): ?><span class="notif-item__dot"></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="notif-panel__footer">
        <a href="<?= url('/notifications') ?>">View all notifications</a>
    </div>
</div>