<?php
class ProfileController extends Controller {

    public function settings(): void {
        $user           = currentUserContext();
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';

        $context = array_merge(
            [
                'pageTitle'     => 'Settings',
                'currentUser'   => $user,
                'currentRoute'  => '/settings',
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
            ],
            $projectContext
        );

        $this->render('profile/settings', $context);
    }
}