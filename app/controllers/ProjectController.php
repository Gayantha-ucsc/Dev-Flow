<?php

class ProjectController extends Controller {

    public function index(): void {
        $user           = currentUserContext();
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';
        $projectsList   = projectsListForCurrentUser();

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
            $projectContext
        );

        $this->render('project/index', $context);
    }

    public function overview(): void {
        $id = (int) (Router::$params['id'] ?? 0);

        $projectsList = projectsListForCurrentUser();
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

        if ($project['role'] === 'client') {
            header('Location: ' . url('client-portal/overview?id=' . $id));
            exit;
        }

        setCurrentProjectId($id);

        $detail = require __DIR__ . '/../../config/mock/project-detail.php';
        $stages = $detail[$id]['stages'] ?? [];

        $taskData = require __DIR__ . '/../../config/mock/project-tasks.php';
        foreach ($stages as $i => &$stage) {
            $stage['tasks'] = $taskData[$id][$i] ?? [];
        }
        unset($stage);

        $teamData = require __DIR__ . '/../../config/mock/team-members.php';
        $members  = array_values(array_filter(
            $teamData[$id]['members'] ?? [],
            fn($m) => $m['status'] === 'active'
        ));

        $user           = currentUserContext();
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';

        $context = array_merge(
            [
                'pageTitle'     => $project['name'],
                'currentUser'   => $user,
                'currentRoute'  => '/projects/' . $id,
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
                'project'       => $project,
                'stages'        => $stages,
                'members'       => $members,
            ],
            $projectContext
        );

        $this->render('project/overview', $context);
    }
}