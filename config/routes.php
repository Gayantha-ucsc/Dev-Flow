<?php
// config/routes.php — URL path => [ControllerClass, method]
// Frontend-only phase: every route points to PageController, which renders
// placeholder content with mock data. Swap individual entries to real
// controllers as each section gets built for real.

return [
    'GET' => [
        '/'               => ['PageController', 'dashboard'],
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
        '/logout'         => ['PageController', 'logout'],
    ],
];