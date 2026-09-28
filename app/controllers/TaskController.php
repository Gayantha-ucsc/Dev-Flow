<?php

class TaskController extends Controller {

    // GET /tasks  -  tasks in the current project, grouped by stage
    public function index(): void {
        [$project, $roles] = $this->projectAndRoles();
        $projectId = (int) $project['project_id'];
        $tasks  = Task::listByProject($projectId);
        $stages = Stage::listByProject($projectId);

        $mock = empty($tasks) ? ((require __DIR__ . '/../../config/mock/project-tasks.php')[$projectId] ?? []) : [];
        $realNames = array_column($tasks, 'name');
        foreach (array_values($stages) as $i => $stage) {
            foreach ($mock[$i] ?? [] as $mi => $m) {
                if (in_array($m['name'], $realNames, true)) { continue; }
                $tasks[] = [
                    'task_id'     => null,
                    'is_mock'     => true,
                    'mock_key'    => $projectId . '-' . $i . '-' . $mi,
                    'stage_id'    => $stage['stage_id'],
                    'name'        => $m['name'],
                    'description' => null,
                    'task_type'   => $m['type'] ?? null,
                    'status'      => $m['status'],
                    'deadline'    => $m['deadline'] ?? null,
                ];
            }
        }

        $this->render('task/board', array_merge($this->context($project, $roles, 'Tasks'), [
            'stages'  => $stages,
            'tasks'   => $tasks,
            'canEdit' => $this->canManage($roles),
        ]));
    }

    // GET /tasks/mock/:key  -  demo task detail, read straight from config/mock (no database)
    public function mockDetail(): void {
        [$project, $roles] = $this->projectAndRoles();
        $projectId = (int) $project['project_id'];
        [$pid, $si, $ti] = array_map('intval', explode('-', (string) (Router::$params['key'] ?? '0-0-0')) + [0, 0, 0]);
        $all    = (require __DIR__ . '/../../config/mock/project-tasks.php')[$projectId] ?? [];
        $stages = array_values(Stage::listByProject($projectId));
        $m = ($pid === $projectId) ? ($all[$si][$ti] ?? null) : null;
        if (!$m || !isset($stages[$si])) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            exit;
        }
        $d = (require __DIR__ . '/../../config/mock/task-details.php')[$m['name']] ?? [];
        $link = fn($n) => array_map(fn($x) => ['task_id' => 'mock/' . $projectId . '-' . $si . '-' . $x[0], 'name' => $x[1], 'status' => $x[2]], $n);
        $deps = []; $blocks = [];
        foreach ($all[$si] as $j => $o) {
            if (in_array($o['name'], $m['dependsOn'] ?? [], true)) { $deps[] = [$j, $o['name'], $o['status']]; }
            if (in_array($m['name'], $o['dependsOn'] ?? [], true)) { $blocks[] = [$j, $o['name'], $o['status']]; }
        }
        $role = $m['type'] ?? 'developer';
        $task = [
            'task_id' => 'mock/' . Router::$params['key'], 'is_mock' => true, 'mock_key' => Router::$params['key'], 'name' => $m['name'], 'stage_name' => $stages[$si]['name'],
            'description' => $d['description'] ?? ('Demo task in the ' . $stages[$si]['name'] . ' stage.'),
            'task_type' => $m['type'] ?? null, 'status' => $m['status'], 'deadline' => $m['deadline'] ?? null,
            'blocked_reason' => $d['blocked'] ?? null, 'acknowledged' => !in_array($m['status'], ['locked', 'not_started'], true),
            'created_at' => null, 'updated_at' => null,
        ];
        $this->render('task/detail', array_merge($this->context($project, $roles, $m['name']), [
            'task' => $task, 'canEdit' => $this->canManage($roles),
            'createdBy' => $d['createdBy'] ?? '-',
            'assignees' => array_map(fn($n) => ['name' => $n, 'role' => 'contributor'], $m['assignees'] ?? []),
            'dependsOn' => $link($deps), 'blocks' => $link($blocks),
            'notes' => array_map(fn($n) => ['name' => $n['name'], 'created_at' => $n['at'], 'content' => $n['text']], $d['notes'] ?? []),
            'rounds' => array_map(fn($r) => ['round_number' => $r['n'], 'started_at' => $r['started'], 'closed_at' => $r['closed']], $d['rounds'] ?? []),
            'approvals' => array_map(fn($r) => ['decision' => $r['decision'], 'name' => $r['by'], 'decided_at' => $r['at'], 'feedback' => $r['feedback']], $d['reviews'] ?? []),
        ]));
    }

    // GET /tasks/mock/:key/edit  -  edit form for a demo task; saving is handled in the browser (temporary)
    public function mockEdit(): void {
        [$project, $roles] = $this->projectAndRoles();
        $this->requireManage($roles);
        $projectId = (int) $project['project_id'];
        [$pid, $si, $ti] = array_map('intval', explode('-', (string) (Router::$params['key'] ?? '0-0-0')) + [0, 0, 0]);
        $all    = (require __DIR__ . '/../../config/mock/project-tasks.php')[$projectId] ?? [];
        $stages = array_values(Stage::listByProject($projectId));
        $m = ($pid === $projectId) ? ($all[$si][$ti] ?? null) : null;
        if (!$m || !isset($stages[$si])) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            exit;
        }
        $d = (require __DIR__ . '/../../config/mock/task-details.php')[$m['name']] ?? [];
        $task = [
            'task_id' => 'mock/' . Router::$params['key'], 'is_mock' => true, 'mock_key' => Router::$params['key'],
            'name' => $m['name'], 'description' => $d['description'] ?? '', 'task_type' => $m['type'] ?? '',
            'deadline' => $m['deadline'] ?? '', 'stage_id' => $stages[$si]['stage_id'],
        ];
        $this->render('task/form', array_merge($this->context($project, $roles, 'Edit Task'), [
            'stages' => $stages, 'task' => $task, 'mode' => 'edit',
            'members' => ProjectMember::assignableForProject($projectId), 'allTasks' => [], 'types' => [],
        ]));
    }

    // GET /tasks/create
    public function create(): void {
        [$project, $roles] = $this->projectAndRoles();
        $this->requireManage($roles);

        $this->render('task/form', array_merge($this->context($project, $roles, 'Create Task'), [
            'stages'   => Stage::listByProject((int) $project['project_id']),
            'task'     => null,
            'mode'     => 'create',
            'members'  => ProjectMember::assignableForProject((int) $project['project_id']),
            'allTasks' => Task::listByProject((int) $project['project_id']),
            'types'    => Task::typesForProject((int) $project['project_id']),
        ]));
    }

    // POST /tasks  (normal form post, or AJAX from the stage panel with ajax=1)
    public function store(): void {
        $ajax = ($_POST['ajax'] ?? '') === '1';
        [$project, $roles] = $this->projectAndRoles();
        $this->requireManage($roles);
        $projectId = (int) $project['project_id'];

        if (!verifyCsrf()) {
            $this->fail($ajax, 'Your session expired. Please try again.', 'tasks/create');
        }

        [$data, $error] = $this->validated($projectId);
        $assignees = [];
        $deps = [];
        if ($error === null) {
            $db = DB::getInstance();
            foreach ((array) ($_POST['assignees'] ?? []) as $id) {
                $id = (int) $id;
                $ok = $db->query('SELECT 1 FROM ProjectMember WHERE project_member_id = ? AND project_id = ? AND is_active = 1', [$id, $projectId]);
                if ($ok) { $assignees[$id] = $id; }
            }
            foreach ((array) ($_POST['dependencies'] ?? []) as $id) {
                $id = (int) $id;
                $ok = $db->query('SELECT 1 FROM Task WHERE task_id = ? AND stage_id = ?', [$id, $data['stage_id']]);
                if ($ok) { $deps[$id] = $id; }
            }
        }
        if ($error !== null) {
            $this->fail($ajax, $error, 'tasks/create');
        }

        $data['created_by'] = $this->creatorMemberId($projectId);
        try {
            Task::createFull($data, array_values($assignees), array_values($deps));
        } catch (Throwable $e) {
            $this->fail($ajax, 'Could not create the task. Please try again.', 'tasks/create');
        }

        Session::flash('success', '"' . $data['name'] . '" was created.');
        if ($ajax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            exit;
        }
        $this->redirect('tasks');
    }

    private function fail(bool $ajax, string $message, string $backTo): void {
        if ($ajax) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => $message]);
            exit;
        }
        Session::flash('error', $message);
        $this->redirect($backTo);
    }

    // GET /tasks/:id
    public function detail(): void {
        $task = $this->findTaskOr404();
        [$project, $roles] = $this->projectAndRoles((int) $task['project_id']);

        $db = DB::getInstance();
        $tid = (int) $task['task_id'];
        $extra = [
            'createdBy' => ($db->query('SELECT u.name FROM ProjectMember pm JOIN User u ON u.user_id = pm.user_id WHERE pm.project_member_id = ?', [(int) $task['created_by']])[0]['name'] ?? '-'),
            'assignees' => $db->query('SELECT u.name, pm.role FROM TaskAssignment ta JOIN ProjectMember pm ON pm.project_member_id = ta.user_id JOIN User u ON u.user_id = pm.user_id WHERE ta.task_id = ? ORDER BY u.name', [$tid]),
            'dependsOn' => $db->query('SELECT t.task_id, t.name, t.status FROM TaskDependency d JOIN Task t ON t.task_id = d.depends_on_task_id WHERE d.task_id = ?', [$tid]),
            'blocks'    => $db->query('SELECT t.task_id, t.name, t.status FROM TaskDependency d JOIN Task t ON t.task_id = d.task_id WHERE d.depends_on_task_id = ?', [$tid]),
            'notes'     => $db->query('SELECT n.content, n.created_at, u.name FROM ProgressNote n JOIN ProjectMember pm ON pm.project_member_id = n.user_id JOIN User u ON u.user_id = pm.user_id WHERE n.task_id = ? ORDER BY n.created_at', [$tid]),
            'rounds'    => $db->query('SELECT round_number, started_at, closed_at FROM RevisionRound WHERE task_id = ? ORDER BY round_number', [$tid]),
            'approvals' => $db->query('SELECT a.decision, a.feedback, a.decided_at, u.name FROM ApprovalRecord a JOIN ProjectMember pm ON pm.project_member_id = a.decided_by JOIN User u ON u.user_id = pm.user_id WHERE a.task_id = ? ORDER BY a.decided_at', [$tid]),
        ];

        $this->render('task/detail', array_merge($this->context($project, $roles, $task['name']), $extra, [
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
            'stages'   => Stage::listByProject((int) $project['project_id']),
            'task'     => $task,
            'mode'     => 'edit',
            'members'  => ProjectMember::assignableForProject((int) $project['project_id']),
            'allTasks' => array_values(array_filter(Task::listByProject((int) $project['project_id']), fn($t) => (int) $t['task_id'] !== (int) $task['task_id'])),
            'types'    => Task::typesForProject((int) $project['project_id']),
            'curAssignees' => Task::assigneeIds((int) $task['task_id']),
            'curDeps'      => Task::dependencyIds((int) $task['task_id']),
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

        $db = DB::getInstance();
        $assignees = [];
        foreach ((array) ($_POST['assignees'] ?? []) as $id) {
            $id = (int) $id;
            if ($db->query('SELECT 1 FROM ProjectMember WHERE project_member_id = ? AND project_id = ? AND is_active = 1', [$id, $projectId])) { $assignees[$id] = $id; }
        }
        $deps = [];
        foreach ((array) ($_POST['dependencies'] ?? []) as $id) {
            $id = (int) $id;
            if ($id !== (int) $task['task_id'] && $db->query('SELECT 1 FROM Task WHERE task_id = ? AND stage_id = ?', [$id, $data['stage_id']])) { $deps[$id] = $id; }
        }

        $err = Task::updateFull((int) $task['task_id'], $data, array_values($assignees), array_values($deps), $this->creatorMemberId($projectId));
        if ($err !== null) {
            Session::flash('error', $err);
            $this->redirect('tasks/' . $task['task_id'] . '/edit');
        }
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
        $notifications  = currentUserNotifications();
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