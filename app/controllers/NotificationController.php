<?php

class NotificationController extends Controller {

    public function index(): void {
        $context = mockPageContext('/notifications', 'Notifications', ['heading' => 'Notifications']);

        $mock = require __DIR__ . '/../../config/mock/notifications.php';
        $feed = $mock['feed'] ?? [];

        // Newest first
        usort($feed, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        $context = array_merge($context, [
            'groupedNotifications' => groupNotificationFeed($feed),
            'categoryCounts'       => notificationCategoryCounts($feed),
        ]);

        $this->render('notifications/index', $context);
    }
}