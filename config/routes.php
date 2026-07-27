<?php
return [
    // Routes that skip Middleware::requireAuth() in Router::dispatch().
    'public' => ['/login'],

    'GET' => [
        '/'               => ['PageController', 'dashboard'],
        '/login'          => ['AuthController', 'showLogin'],
        '/dashboard'      => ['PageController', 'dashboard'],
        '/projects'       => ['PageController', 'projects'],
        '/team'           => ['PageController', 'team'],
        '/tasks'          => ['PageController', 'tasks'],
        '/review'         => ['PageController', 'review'],
        '/chat'           => ['PageController', 'chat'],
        '/reports'        => ['PageController', 'reports'],
        '/payment'        => ['PageController', 'payment'],
        '/settings'       => ['PageController', 'settings'],
        '/notifications'  => ['PageController', 'notifications'],
        '/profile'        => ['PageController', 'profile'],
        '/logout'         => ['AuthController', 'logout'],
    ],

    'POST' => [
        '/login'          => ['AuthController', 'login'],
    ],
];