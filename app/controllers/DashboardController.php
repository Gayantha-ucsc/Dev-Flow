<?php

class DashboardController extends Controller {

    public function index(): void {
        $user           = require __DIR__ . '/../../config/mock/users.php';
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';

        $context = array_merge(
            [
                'pageTitle'     => 'Dashboard',
                'currentUser'   => $user,
                'currentRoute'  => '/dashboard',
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
            ],
            $projectContext
        );

        if (empty($context['userProjects'])) {
            $this->render('pages/dashboard', array_merge($context, [
                'currentProjectId' => null,
                'icon'             => 'rocket',
                'heading'          => 'Ready to launch your first project?',
                'subtext'          => 'Create a project to start managing tasks, tracking progress, and collaborating with your team.',
                'ctaText'          => 'Create a Project',
                'ctaTrigger'       => 'project-wizard',
            ]));
            return;
        }

        if (userHasRoleAnywhere('manager')) {
            $managerData = require __DIR__ . '/../../config/mock/manager-dashboard.php';
            $this->render('dashboard/manager', array_merge($context, $managerData));
            return;
        }

        $this->render('pages/placeholder', array_merge($context, [
            'heading' => 'Dashboard',
        ]));
    }
}