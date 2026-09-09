<?php

class TaskController extends Controller {

    private function baseContext(string $currentRoute, string $pageTitle): array {
        $user           = require __DIR__ . '/../../config/mock/users.php';
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';

        return array_merge(
            [
                'pageTitle'     => $pageTitle,
                'currentUser'   => $user,
                'currentRoute'  => $currentRoute,
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
            ],
            $projectContext
        );
    }

    private function projectTasks(int $projectId): array {
        $all = require __DIR__ . '/../../config/mock/tasks.php';
        return $all[$projectId] ?? ['stages' => [], 'tasks' => []];
    }

    private function taskPerms(?array $task = null): array {
        $ctx = currentProjectContext();
        $user = require __DIR__ . '/../../config/mock/users.php';
        return taskPermissionsUiDemo(taskPermissions($ctx['activeRole'], $user, $task));
    }

    private function boardStats(array $tasks): array {
        $counts = array_count_values(array_column($tasks, 'status'));
        return [
            ['label' => 'In Progress', 'value' => (int) ($counts['in_progress'] ?? 0), 'tone' => 'primary', 'icon' => 'loader-circle', 'meta' => 'Active work'],
            ['label' => 'Blocked', 'value' => (int) ($counts['blocked'] ?? 0), 'tone' => 'danger', 'icon' => 'ban', 'meta' => 'Needs attention'],
            ['label' => 'Pending Review', 'value' => (int) ($counts['pending_review'] ?? 0), 'tone' => 'pink', 'icon' => 'review', 'meta' => 'Awaiting approval'],
            ['label' => 'Completed', 'value' => (int) ($counts['completed'] ?? 0), 'tone' => '', 'icon' => 'circle-check', 'meta' => 'Done this project'],
        ];
    }

    public function index(): void {
        $projectId   = currentProjectContext()['currentProjectId'];
        $projectData = $this->projectTasks($projectId);

        $context = array_merge($this->baseContext('/tasks', 'Tasks'), [
            'stages'     => $projectData['stages'],
            'tasks'      => $projectData['tasks'],
            'taskStats'  => $this->boardStats($projectData['tasks']),
            'taskPerms'  => $this->taskPerms(),
        ]);

        $this->render('task/board', $context);
    }

    public function create(): void {
        $projectId   = currentProjectContext()['currentProjectId'];
        $projectData = $this->projectTasks($projectId);
        $teamData    = require __DIR__ . '/../../config/mock/team-members.php';

        $context = array_merge($this->baseContext('/tasks', 'Create Task'), [
            'stages'      => $projectData['stages'],
            'tasks'       => $projectData['tasks'],
            'teamMembers' => $teamData[$projectId]['members'] ?? [],
            'taskPerms'   => $this->taskPerms(),
            'mode'        => 'create',
        ]);

        $this->render('task/form', $context);
    }

    public function detail(): void {
        $taskId      = (int) (Router::$params['id'] ?? 0);
        $projectId   = currentProjectContext()['currentProjectId'];
        $projectData = $this->projectTasks($projectId);
        $task        = null;

        foreach ($projectData['tasks'] as $row) {
            if ($row['id'] === $taskId) {
                $task = $row;
                break;
            }
        }

        if (!$task) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $teamData = require __DIR__ . '/../../config/mock/team-members.php';

        $context = array_merge($this->baseContext('/tasks', $task['title']), [
            'task'        => $task,
            'allTasks'    => $projectData['tasks'],
            'stages'      => $projectData['stages'],
            'teamMembers' => $teamData[$projectId]['members'] ?? [],
            'taskPerms'   => $this->taskPerms($task),
        ]);

        $this->render('task/detail', $context);
    }

    public function edit(): void {
        $taskId      = (int) (Router::$params['id'] ?? 0);
        $projectId   = currentProjectContext()['currentProjectId'];
        $projectData = $this->projectTasks($projectId);
        $task        = null;

        foreach ($projectData['tasks'] as $row) {
            if ($row['id'] === $taskId) {
                $task = $row;
                break;
            }
        }

        if (!$task) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $context = array_merge($this->baseContext('/tasks', 'Edit Task'), [
            'stages'      => $projectData['stages'],
            'tasks'       => $projectData['tasks'],
            'task'        => $task,
            'teamMembers' => (require __DIR__ . '/../../config/mock/team-members.php')[$projectId]['members'] ?? [],
            'taskPerms'   => $this->taskPerms($task),
            'mode'        => 'edit',
        ]);

        $this->render('task/form', $context);
    }
}
