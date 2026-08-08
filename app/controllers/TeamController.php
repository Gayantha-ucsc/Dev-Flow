<?php
class TeamController extends Controller {

    public function index(): void {
        $user           = require __DIR__ . '/../../config/mock/users.php';
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';
        $teamData       = require __DIR__ . '/../../config/mock/team-members.php';

        $projectId = $projectContext['currentProjectId'];
        $members          = $teamData[$projectId]['members'] ?? [];
        $pendingApprovals = $teamData[$projectId]['pendingApprovals'] ?? [];

        $context = array_merge(
            [
                'pageTitle'        => 'Team & Roles',
                'currentUser'      => $user,
                'currentRoute'     => '/team',
                'unreadCount'      => $notifications['unreadCount'],
                'notifications'    => $notifications['items'],
                'members'          => $members,
                'pendingApprovals' => $pendingApprovals,
            ],
            $projectContext
        );

        $this->render('team/index', $context);
    }
}