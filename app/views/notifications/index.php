<?php
// Expects: $groupedNotifications (day => ['label','unread','items']), $categoryCounts

$groupedNotifications = $groupedNotifications ?? [];
$categoryCounts       = $categoryCounts ?? ['all' => 0, 'tasks' => 0, 'approvals' => 0, 'chat' => 0, 'payments' => 0];
$totalUnread          = array_sum(array_map(fn($g) => $g['unread'], $groupedNotifications));

$tabs = [
    'all'       => 'All',
    'tasks'     => 'Tasks',
    'approvals' => 'Approvals',
    'chat'      => 'Chat',
    'payments'  => 'Payments',
];
?>
<div class="notif-page">

    <div class="notif-page__header">
        <div>
            <p class="notif-page__eyebrow"><span class="notif-page__eyebrow-dot"></span>Activity Stream</p>
            <h1>Notifications</h1>
            <p class="notif-page__subtitle">Stay on top of what's happening across your projects and deliverables.</p>
        </div>

        <div class="notif-page__header-actions">
            <span class="notif-page__updated"><?= renderIcon('clock') ?> Updated just now</span>
            <?php if ($totalUnread > 0): ?>
                <button type="button" class="btn-sm notif-page__mark-all" id="notifMarkAllRead">
                    <?= renderIcon('check') ?> Mark all as read
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="notif-tabs" role="tablist">
        <?php foreach ($tabs as $key => $label): ?>
            <button type="button"
                    class="notif-tab <?= $key === 'all' ? 'is-active' : '' ?>"
                    data-notif-tab="<?= $key ?>"
                    role="tab"
                    aria-selected="<?= $key === 'all' ? 'true' : 'false' ?>">
                <?= htmlspecialchars($label) ?>
                <span class="notif-tab__count" data-count-for="<?= $key ?>"><?= (int) ($categoryCounts[$key] ?? 0) ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <?php if (empty($groupedNotifications)): ?>
        <?php
            $variant = 'card';
            $icon    = 'bell';
            $heading = "You're all caught up";
            $subtext = 'New activity across your projects will show up here.';
        ?>
        <?php include __DIR__ . '/../partials/empty-state.php'; ?>
    <?php else: ?>
        <div class="notif-feed card" id="notifFeed">
            <?php foreach ($groupedNotifications as $group): ?>
                <div class="notif-day-group" data-day-group>
                    <div class="notif-day-group__header">
                        <span class="notif-day-group__label"><?= htmlspecialchars(strtoupper($group['label'])) ?></span>
                        <span class="notif-day-group__unread" data-unread-label>
                            <?= $group['unread'] > 0 ? (int) $group['unread'] . ' unread' : 'All caught up' ?>
                        </span>
                    </div>

                    <?php foreach ($group['items'] as $i => $item): ?>
                        <?php $timeLabel = notificationTimeLabel($item['created_at'], $group['label']); ?>
                        <div class="notif-row <?= empty($item['is_read']) ? 'is-unread' : '' ?>"
                             data-notif-row
                             data-category="<?= htmlspecialchars($item['category']) ?>"
                             data-read="<?= empty($item['is_read']) ? '0' : '1' ?>">

                            <span class="notif-row__icon notif-row__icon--<?= htmlspecialchars($item['tone'] ?? 'neutral') ?>">
                                <?= renderIcon($item['icon'] ?? 'bell') ?>
                            </span>

                            <a href="<?= url($item['href'] ?? '/notifications') ?>" class="notif-row__body">
                                <span class="notif-row__message"><?= renderNotificationMessage($item['message']) ?></span>

                                <?php if (!empty($item['quote'])): ?>
                                    <span class="notif-row__quote">&ldquo;<?= htmlspecialchars($item['quote']) ?>&rdquo;</span>
                                <?php endif; ?>

                                <span class="notif-row__meta">
                                    <span class="notif-row__time"><?= htmlspecialchars($timeLabel) ?></span>
                                    <?php if (!empty($item['context'])): ?>
                                        <span class="notif-row__dot-sep">&bull;</span>
                                        <span class="notif-row__context notif-row__context--<?= htmlspecialchars($item['contextTone'] ?? 'link') ?>">
                                            <?= htmlspecialchars($item['context']) ?>
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </a>

                            <?php if (empty($item['is_read'])): ?>
                                <span class="notif-row__unread-dot" data-unread-dot aria-hidden="true"></span>
                            <?php endif; ?>

                            <div class="dropdown notif-row__menu" data-dropdown>
                                <button type="button" class="icon-btn icon-btn--sm" data-dropdown-trigger aria-label="Notification options">
                                    <?= renderIcon('ellipsis-vertical') ?>
                                </button>
                                <div class="dropdown__menu dropdown__menu--right" data-dropdown-menu>
                                    <button type="button" class="dropdown__item" data-notif-action="read">
                                        <?= renderIcon('check') ?> Mark as read
                                    </button>
                                    <button type="button" class="dropdown__item" data-notif-action="mute">
                                        <?= renderIcon('eye-off') ?> Mute this thread
                                    </button>
                                    <button type="button" class="dropdown__item dropdown__item--danger" data-notif-action="delete">
                                        <?= renderIcon('trash-2') ?> Delete notification
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="notif-page__load-more">
            <button type="button" class="notif-page__load-more-btn" disabled title="No more notifications">
                Load more notifications <?= renderIcon('chevron-down') ?>
            </button>
        </div>
    <?php endif; ?>

</div>