<?php
class ProjectController extends Controller {

    public function index(): void {
        $user          = require __DIR__ . '/../../config/mock/users.php';
        $roles         = require __DIR__ . '/../../config/mock/roles.php';
        $projects      = require __DIR__ . '/../../config/mock/projects.php';
        $notifications = require __DIR__ . '/../../config/mock/notifications.php';
        $projectsList  = require __DIR__ . '/../../config/mock/projects-list.php';

        usort($projectsList, fn($a, $b) => strtotime($a['deadline']) <=> strtotime($b['deadline']));

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

    // Projects Tab row's arrow link
    public function overview(): void {
        $id = (int) (Router::$params['id'] ?? 0);

        $projectsList = require __DIR__ . '/../../config/mock/projects-list.php';
        $project = null;
        foreach ($projectsList as $row) {
            if ($row['id'] === $id) {
                $project = $row;
                break;
            }
        }

        if ($project === null) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $detail = require __DIR__ . '/../../config/mock/project-detail.php';
        $stages = $detail[$id]['stages'] ?? [];

        $user          = require __DIR__ . '/../../config/mock/users.php';
        $roles         = require __DIR__ . '/../../config/mock/roles.php';
        $projects      = require __DIR__ . '/../../config/mock/projects.php';
        $notifications = require __DIR__ . '/../../config/mock/notifications.php';

        $context = array_merge(
            [
                'pageTitle'     => $project['name'],
                'currentUser'   => $user,
                'currentRoute'  => '/projects',
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
                'project'       => $project,
                'stages'        => $stages,
            ],
            $roles,
            $projects
        );

        $this->render('project/overview', $context);
    }
}