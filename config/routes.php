<?php
return [
    // Routes that skip Middleware::requireAuth() in Router::dispatch().
    'public' => ['/login'],

    'GET' => [
        '/'                    => ['DashboardController', 'index'],
        '/login'               => ['AuthController', 'showLogin'],
        '/dashboard'           => ['DashboardController', 'index'],
        '/projects'            => ['ProjectController', 'index'],
        '/projects/:id'        => ['ProjectController', 'overview'],
        '/team'                => ['TeamController', 'index'],
        '/team/add-member'     => ['TeamController', 'addMember'],
        '/team/approvals'      => ['TeamController', 'approvals'],
        '/team/invite-client'  => ['TeamController', 'inviteClient'],
        '/tasks'               => ['TaskController', 'index'],
        '/tasks/create'        => ['TaskController', 'create'],
        '/tasks/:id'           => ['TaskController', 'detail'],
        '/tasks/:id/edit'      => ['TaskController', 'edit'],
        '/review'              => ['PageController', 'review'],
        '/client-portal/reviews' => ['PageController', 'clientPortalReviews'],
        '/chat'                => ['ChatController', 'index'],
        '/reports'             => ['PageController', 'reports'],
        '/payment'             => ['PageController', 'payment'],
        '/settings'            => ['ProfileController', 'settings'],
        '/notifications'       => ['PageController', 'notifications'],
        '/logout'              => ['AuthController', 'logout'],

        // System Administration (Admin only, project-agnostic)
        '/admin/users'       => ['AdminUserController', 'index'],
        '/admin/permissions' => ['AdminPermissionController', 'index'],
        '/admin/templates'   => ['AdminTemplateController', 'index'],
        '/admin/settings'    => ['AdminSettingsController', 'index'],
        '/admin/audit'       => ['AdminAuditController', 'index'],
    ],

    'POST' => [
        '/login'          => ['AuthController', 'login'],
    ],
];
