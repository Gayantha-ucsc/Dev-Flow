<?php
return [
    // Routes that skip Middleware::requireAuth() in Router::dispatch().
    'public' => ['/login'],

    'GET' => [
        '/'               => ['DashboardController', 'index'],
        '/login'          => ['AuthController', 'showLogin'],
        '/dashboard'      => ['DashboardController', 'index'],
        '/projects'       => ['ProjectController', 'index'],
        '/projects/:id'   => ['ProjectController', 'overview'],
        '/team'           => ['TeamController', 'index'],
        '/tasks'          => ['PageController', 'tasks'],
        '/review'         => ['PageController', 'review'],
        '/chat'           => ['PageController', 'chat'],
        '/reports'        => ['PageController', 'reports'],
        '/payment'        => ['PageController', 'payment'],
        '/settings'       => ['ProfileController', 'settings'],
        '/notifications'  => ['PageController', 'notifications'],
        '/logout'         => ['AuthController', 'logout'],
    ],

    'POST' => [
        '/login'          => ['AuthController', 'login'],
    ],
];