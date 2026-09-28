<?php

class ProjectController extends Controller {

    public function index(): void {
        $user           = currentUserContext();
        $projectContext = currentProjectContext();
        $notifications  = currentUserNotifications();
        $projectsList   = projectsListForCurrentUser();

        foreach (ProjectMember::projectsForUser(Auth::id()) as $row) {
            $projectId = (int) $row['project_id'];
            $dbProject = Project::findById($projectId);
            if (!$dbProject) {
                continue;
            }

            $dbStages = Stage::listByProject($projectId);

            $percent    = 0;
            $stageLabel = 'No stages yet';
            if (!empty($dbStages)) {
                $completed = count(array_filter($dbStages, fn($s) => $s['status'] === 'completed'));
                $percent   = (int) round(($completed / count($dbStages)) * 100);

                $current = null;
                foreach ($dbStages as $s) {
                    if ($s['status'] !== 'completed') {
                        $current = $s;
                        break;
                    }
                }
                $stageLabel = $current ? $current['name'] : 'Completed';
            }

            $pendingCount = 0;
            $overdueCount = 0;
            $blockedCount = 0;
            foreach (Task::listByProject($projectId) as $task) {
                if ($task['status'] === 'pending_review') $pendingCount++;
                if ($task['status'] === 'overdue')        $overdueCount++;
                if ($task['status'] === 'blocked')        $blockedCount++;
            }

            $entry = [
                'id'              => $projectId,
                'name'            => $dbProject['name'],
                'description'     => $dbProject['description'] ?? '',
                'status'          => $dbProject['status'],
                'health'          => null,
                'role'            => $row['role'],
                'percent'         => $percent,
                'stages'          => array_column($dbStages, 'status'),
                'stageLabel'      => $stageLabel,
                'deadline'        => $dbProject['deadline'],
                'pendingCount'    => $pendingCount,
                'overdueCount'    => $overdueCount,
                'blockedCount'    => $blockedCount,
                'milestonesPaid'  => 0,
                'milestonesTotal' => 0,
            ];

            $replaced = false;
            foreach ($projectsList as $i => $mockRow) {
                if ((int) $mockRow['id'] === $projectId) {
                    $projectsList[$i] = $entry;
                    $replaced = true;
                    break;
                }
            }
            if (!$replaced) {
                $projectsList[] = $entry;
            }
        }

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
            if ((int) $row['id'] === $id) {
                $project = $row;
                break;
            }
        }

        $dbProject = Project::findById($id);
        $dbRoles   = $dbProject ? ProjectMember::rolesForUser($id, Auth::id()) : [];

        if ($project === null && empty($dbRoles)) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        if ($project === null) {
            // No mock stand-in for this project - build the row straight from the DB.
            $project = [
                'id'              => $id,
                'name'            => $dbProject['name'],
                'description'     => $dbProject['description'] ?? '',
                'status'          => $dbProject['status'],
                'health'          => null,
                'role'            => $dbRoles[0] ?? null,
                'percent'         => 0,
                'deadline'        => $dbProject['deadline'],
                'pendingCount'    => 0,
                'overdueCount'    => 0,
                'blockedCount'    => 0,
                'milestonesPaid'  => 0,
                'milestonesTotal' => 0,
            ];
        }

        if (!empty($dbRoles) ? in_array('client', $dbRoles, true) : $project['role'] === 'client') {
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

        $stagesEditable = false;
        if ($dbProject && !empty($dbRoles)) {
            $project['name']        = $dbProject['name'];
            $project['description'] = $dbProject['description'];
            $project['deadline']    = $dbProject['deadline'];
            $project['status']      = $dbProject['status'];

            $stagesEditable = ProjectMember::canManageStages($id, Auth::id());
            $stages = array_map(function ($s) {
                return [
                    'stage_id'      => (int) $s['stage_id'],
                    'name'          => $s['name'],
                    'status'        => $s['status'],
                    'tasksTotal'    => Stage::taskCount((int) $s['stage_id']),
                    'tasksApproved' => 0,
                    'tasks'         => [],
                ];
            }, Stage::listByProject($id));
        }

        $teamData = require __DIR__ . '/../../config/mock/team-members.php';
        $members  = array_values(array_filter(
            $teamData[$id]['members'] ?? [],
            fn($m) => $m['status'] === 'active'
        ));

        $user           = currentUserContext();
        $projectContext = currentProjectContext();
        $notifications  = currentUserNotifications();

        $context = array_merge(
            [
                'pageTitle'     => $project['name'],
                'currentUser'   => $user,
                'currentRoute'  => '/projects/' . $id,
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
                'project'       => $project,
                'stages'        => $stages,
                'stagesEditable' => $stagesEditable,
                'members'       => $members,
                'showPayment'   => (bool) array_intersect(!empty($dbRoles) ? $dbRoles : [$project['role'] ?? ''], ['manager', 'team_lead', 'client']),
            ],
            $projectContext
        );

        $this->render('project/overview', $context);
    }

    // POST /projects/:id/update - edit name / description / deadline
    public function update(): void {
        $id = (int) (Router::$params['id'] ?? 0);

        $dbProject = Project::findById($id);
        if (!$dbProject) {
            Session::flash('error', "This demo project's details aren't editable.");
            header('Location: ' . url('projects/' . $id));
            exit;
        }

        $roles = ProjectMember::rolesForUser($id, Auth::id());
        if (!in_array('manager', $roles, true) && !in_array('team_lead', $roles, true)) {
            Session::flash('error', 'Only a Manager or Team Lead can edit project details.');
            header('Location: ' . url('projects/' . $id));
            exit;
        }

        if (!verifyCsrf()) {
            Session::flash('error', 'Your session expired. Please try again.');
            header('Location: ' . url('projects/' . $id));
            exit;
        }

        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $deadline    = trim($_POST['deadline'] ?? '');

        if ($name === '') {
            Session::flash('error', 'Please enter a project name.');
            header('Location: ' . url('projects/' . $id));
            exit;
        }
        if (strlen($name) > 150) {
            Session::flash('error', 'Project name must be 150 characters or fewer.');
            header('Location: ' . url('projects/' . $id));
            exit;
        }

        Project::update($id, $name, $description, $deadline ?: $dbProject['deadline']);
        Session::flash('success', 'Project details updated.');
        header('Location: ' . url('projects/' . $id));
        exit;
    }
}