<?php

class TaskController extends Controller {

    // GET /tasks  -  tasks in the current project, grouped by stage
    public function index(): void {
        [$project, $roles] = $this->projectAndRoles();
        $tasks = Task::listByProject((int) $project['project_id']);

        $this->render('task/board', array_merge($this->context($project, $roles, 'Tasks'), [
            'stages'  => Stage::listByProject((int) $project['project_id']),
            'tasks'   => $tasks,
            'canEdit' => $this->canManage($roles),
        ]));
    }

    // GET /tasks/create
    public function create(): void {
        [$project, $roles] = $this->projectAndRoles();
        $this->requireManage($roles);

        $this->render('task/form', array_merge($this->context($project, $roles, 'Create Task'), [
            'stages' => Stage::listByProject((int) $project['project_id']),
            'task'   => null,
            'mode'   => 'create',
        ]));
    }

    // POST /tasks
    public function store(): void {
        [$project, $roles] = $this->projectAndRoles();
        $this->requireManage($roles);
        $this->requireCsrf('tasks/create');
        $projectId = (int) $project['project_id'];

        [$data, $error] = $this->validated($projectId);
        if ($error !== null) {
            Session::flash('error', $error);
            $this->redirect('tasks/create');
        }

        $data['created_by'] = $this->creatorMemberId($projectId);
        Task::create($data);
        Session::flash('success', '"' . $data['name'] . '" was created.');
        $this->redirect('tasks');
    }

    // GET /tasks/:id
    public function detail(): void {
        $task = $this->findTaskOr404();
        [$project, $roles] = $this->projectAndRoles((int) $task['project_id']);

        $this->render('task/detail', array_merge($this->context($project, $roles, $task['name']), [
            'task'    => $task,
            'canEdit' => $this->canManage($roles),
        ]));
    }

    // GET /tasks/:id/edit
    public function edit(): void {
        $task = $this->findTaskOr404();
        [$project, $roles] = $this->projectAndRoles((int) $task['project_id']);
        $this->requireManage($roles);

        $this->render('task/form', array_merge($this->context($project, $roles, 'Edit Task'), [
            'stages' => Stage::listByProject((int) $project['project_id']),
            'task'   => $task,
            'mode'   => 'edit',
        ]));
    }

    // POST /tasks/:id/update
    public function update(): void {
        $task = $this->findTaskOr404();
        $projectId = (int) $task['project_id'];
        [$project, $roles] = $this->projectAndRoles($projectId);
        $this->requireManage($roles);
        $this->requireCsrf('tasks/' . $task['task_id']);

        [$data, $error] = $this->validated($projectId);
        if ($error !== null) {
            Session::flash('error', $error);
            $this->redirect('tasks/' . $task['task_id'] . '/edit');
        }

        Task::update((int) $task['task_id'], $data);
        Session::flash('success', 'Task updated.');
        $this->redirect('tasks/' . $task['task_id']);
    }

    // POST /tasks/:id/delete
    public function destroy(): void {
        $task = $this->findTaskOr404();
        [$project, $roles] = $this->projectAndRoles((int) $task['project_id']);
        $this->requireManage($roles);
        $this->requireCsrf('tasks');

        try {
            Task::delete((int) $task['task_id']);
            Session::flash('success', '"' . $task['name'] . '" was deleted.');
        } catch (Throwable $e) {
            Session::flash('error', 'That task has review history or comments and can\'t be deleted.');
        }
        $this->redirect('tasks');
    }

    // Helpers

    private function validated(int $projectId): array {
        $name     = trim($_POST['name'] ?? '');
        $desc     = trim($_POST['description'] ?? '');
        $type     = trim($_POST['task_type'] ?? '');
        $deadline = trim($_POST['deadline'] ?? '');
        $stageId  = (int) ($_POST['stage_id'] ?? 0);

        $error = null;
        if ($name === '') {
            $error = 'Please enter a task name.';
        } elseif (strlen($name) > 150) {
            $error = 'Task name must be 150 characters or fewer.';
        } elseif (strlen($type) > 50) {
            $error = 'Task type must be 50 characters or fewer.';
        } elseif (!Stage::belongsToProject($stageId, $projectId)) {
            $error = 'Please choose a valid stage.';
        } elseif ($deadline !== '' && (!($d = DateTime::createFromFormat('Y-m-d', $deadline)) || $d->format('Y-m-d') !== $deadline)) {
            $error = 'Please enter a valid deadline.';
        }

        return [[
            'stage_id'    => $stageId,
            'name'        => $name,
            'description' => $desc === '' ? null : $desc,
            'task_type'   => $type === '' ? null : $type,
            'deadline'    => $deadline === '' ? null : $deadline,
        ], $error];
    }

    // Current project = the one in the session if the user is on it, else their first non-client project.
    private function projectAndRoles(?int $projectId = null): array {
        $userId = Auth::id();

        if ($projectId === null) {
            $selected = Session::get('current_project_id');
            if ($selected && !empty(ProjectMember::rolesForUser((int) $selected, $userId))) {
                $projectId = (int) $selected;
            } else {
                foreach (ProjectMember::projectsForUser($userId) as $row) {
                    if ($row['role'] !== 'client') {
                        $projectId = (int) $row['project_id'];
                        break;
                    }
                }
            }
        }

        $project = $projectId ? Project::findById($projectId) : null;
        $roles   = $project ? ProjectMember::rolesForUser($projectId, $userId) : [];

        if (!$project || empty($roles) || $roles === ['client']) {
            Session::flash('error', 'Join or create a project to work with tasks.');
            $this->redirect('projects');
        }

        setCurrentProjectId($projectId);
        return [$project, $roles];
    }

    private function context(array $project, array $roles, string $title): array {
        $notifications = require __DIR__ . '/../../config/mock/notifications.php';
        $base = currentProjectContext();
        $role = in_array('manager', $roles, true) ? 'manager' : (in_array('team_lead', $roles, true) ? 'team_lead' : $roles[0]);

        return array_merge($base, [
            'pageTitle'          => $title,
            'currentUser'        => currentUserContext(),
            'currentRoute'       => '/tasks',
            'unreadCount'        => $notifications['unreadCount'],
            'notifications'      => $notifications['items'],
            'currentProjectId'   => (int) $project['project_id'],
            'currentProjectName' => $project['name'],
            'activeRole'         => $role,
        ]);
    }

    private function canManage(array $roles): bool {
        return in_array('manager', $roles, true) || in_array('team_lead', $roles, true);
    }

    private function requireManage(array $roles): void {
        if (!$this->canManage($roles)) {
            Session::flash('error', 'Only a Manager or Team Lead can change tasks.');
            $this->redirect('tasks');
        }
    }

    private function requireCsrf(string $backTo): void {
        if (!verifyCsrf()) {
            Session::flash('error', 'Your session expired. Please try again.');
            $this->redirect($backTo);
        }
    }

    // project_member_id of the acting manager / team lead row.
    private function creatorMemberId(int $projectId): int {
        $rows = DB::getInstance()->query(
            "SELECT project_member_id FROM ProjectMember
             WHERE project_id = ? AND user_id = ? AND is_active = 1 AND role IN ('manager','team_lead')
             ORDER BY role = 'team_lead' DESC LIMIT 1",
            [$projectId, Auth::id()]
        );
        return (int) $rows[0]['project_member_id'];
    }

    private function findTaskOr404(): array {
        $task = Task::findById((int) (Router::$params['id'] ?? 0));
        if (!$task) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            exit;
        }
        return $task;
    }

    private function redirect(string $path): void {
        header('Location: ' . url($path));
        exit;
    }
}