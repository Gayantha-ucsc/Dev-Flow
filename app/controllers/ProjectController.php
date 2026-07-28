<?php
class ProjectController extends Controller {

    public function index(): void {
        $user          = require __DIR__ . '/../../config/mock/users.php';
        $roles         = require __DIR__ . '/../../config/mock/roles.php';
        $projects      = require __DIR__ . '/../../config/mock/projects.php';
        $notifications = require __DIR__ . '/../../config/mock/notifications.php';
        $projectsList  = require __DIR__ . '/../../config/mock/projects-list.php';

        $context = array_merge(
            [
                'pageTitle'     => 'Projects',
                'currentUser'   => $user,
                'currentRoute'  => '/projects',
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
                'projectsList'  => $projectsList,
            ],
            $roles,
            $projects
        );

        $this->render('project/index', $context);
    }
}