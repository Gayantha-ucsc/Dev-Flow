<?php

class ChatController extends Controller {

    public function index(): void {
        $user           = require __DIR__ . '/../../config/mock/users.php';
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';
        $chatData       = require __DIR__ . '/../../config/mock/chat.php';
        $taskData       = require __DIR__ . '/../../config/mock/tasks.php';

        $projectId = $projectContext['currentProjectId'];
        $chat      = $chatData[$projectId] ?? [
            'projectMessages' => [],
            'stageMessages'   => [],
            'taskMessages'    => [],
            'clientMessages'  => [],
            'clientAccess'    => [],
            'accessRequests'  => [],
        ];

        $tasks  = $taskData[$projectId]['tasks'] ?? [];
        $stages = $taskData[$projectId]['stages'] ?? [];

        $hasApprovedClientAccess = in_array($projectContext['activeRole'], ['manager', 'team_lead', 'client'], true)
            || !empty(array_filter($chat['clientAccess'] ?? [], fn($a) => ($a['access'] ?? '') === 'approved'));

        $chatPerms = chatPermissionsUiDemo(
            chatPermissions($projectContext['activeRole'], $hasApprovedClientAccess)
        );

        $context = array_merge(
            [
                'pageTitle'     => 'Chat',
                'currentUser'   => $user,
                'currentRoute'  => '/chat',
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
                'chat'          => $chat,
                'tasks'         => $tasks,
                'stages'        => $stages,
                'chatPerms'     => $chatPerms,
            ],
            $projectContext
        );

        $this->render('chat/index', $context);
    }
}
