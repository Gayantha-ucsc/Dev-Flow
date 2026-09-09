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
        '/chat'                => ['ChatController', 'index'],
        '/reports'             => ['PageController', 'reports'],
        '/payment'             => ['PageController', 'payment'],
        '/settings'            => ['ProfileController', 'settings'],
        '/notifications'       => ['PageController', 'notifications'],
        '/logout'              => ['AuthController', 'logout'],
    ],

    'POST' => [
        '/login'          => ['AuthController', 'login'],
    ],
];
