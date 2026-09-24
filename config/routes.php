<?php
return [
    // Routes that skip Middleware::requireAuth() in Router::dispatch().
    'public' => ['/login', '/register'],

    'GET' => [
        '/'                    => ['DashboardController', 'index'],
        '/login'               => ['AuthController', 'showLogin'],
        '/register'            => ['AuthController', 'showRegister'],
        '/dashboard'           => ['DashboardController', 'index'],
        '/projects'            => ['ProjectController', 'index'],
        '/projects/:id'        => ['ProjectController', 'overview'],
        '/team'                => ['TeamController', 'index'],
        '/team/add-member'     => ['TeamController', 'addMember'],
        '/team/search-users'   => ['TeamController', 'searchUsers'],
        '/team/approvals'      => ['TeamController', 'approvals'],
        '/team/invite-client'  => ['TeamController', 'inviteClient'],
        '/tasks'               => ['TaskController', 'index'],
        '/tasks/create'        => ['TaskController', 'create'],
        '/tasks/:id'           => ['TaskController', 'detail'],
        '/tasks/:id/edit'      => ['TaskController', 'edit'],
        '/review'              => ['ReviewController', 'queue'],
        '/client-portal/reviews'  => ['ClientPortalController', 'reviews'],
        '/client-portal/overview' => ['ClientPortalController', 'overview'],
        '/client-portal/history'  => ['ClientPortalController', 'history'],
        '/chat'                => ['ChatController', 'index'],
        '/reports'             => ['ReportController', 'index'],
        '/payment'             => ['PaymentController', 'index'],
        '/payment/history'     => ['PaymentController', 'history'],
        '/settings'            => ['ProfileController', 'settings'],
        '/notifications'       => ['NotificationController', 'index'],
        '/logout'              => ['AuthController', 'logout'],

        // System Administration (Admin only, project-agnostic)
        '/admin/users'       => ['AdminUserController', 'index'],
        '/admin/users/create'   => ['AdminUserController', 'create'],
        '/admin/users/:id/edit' => ['AdminUserController', 'edit'],
        '/admin/permissions' => ['AdminPermissionController', 'index'],
        '/admin/templates'   => ['AdminTemplateController', 'index'],
        '/admin/settings'    => ['AdminSettingsController', 'index'],
        '/admin/audit'       => ['AdminAuditController', 'index'],
    ],

    'POST' => [
        '/login'          => ['AuthController', 'login'],
        '/register'       => ['AuthController', 'register'],

        '/admin/users'            => ['AdminUserController', 'store'],
        '/admin/users/:id/update' => ['AdminUserController', 'update'],
        '/admin/users/:id/delete' => ['AdminUserController', 'destroy'],

        '/payment/milestones'                => ['PaymentController', 'store'],
        '/payment/milestones/:id/update'     => ['PaymentController', 'update'],
        '/payment/milestones/:id/delete'     => ['PaymentController', 'destroy'],
        '/payment/milestones/:id/request'    => ['PaymentController', 'request'],

        '/projects/:id/stages' => ['StageController', 'store'],
        '/stages/:id/update'   => ['StageController', 'update'],
        '/stages/:id/move'     => ['StageController', 'move'],
        '/stages/:id/delete'   => ['StageController', 'destroy'],
    ],
];