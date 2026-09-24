<?php

class ProjectController extends Controller {

    public function index(): void {
        $user           = currentUserContext();
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';
        $projectsList   = projectsListForCurrentUser();

        foreach (ProjectMember::projectsForUser(Auth::id()) as $row) {
            $projectId = (int) $row['project_id'];
            $dbProject = Project::findById($projectId);
            if (!$dbProject) {
                continue;
            }

            $entry = [
                'id'              => $projectId,
                'name'            => $dbProject['name'],
                'description'     => $dbProject['description'] ?? '',
                'status'          => $dbProject['status'],
                'health'          => null,
                'role'            => $row['role'],
                'percent'         => 0,
                'stages'          => [],
                'stageLabel'      => 'No stages yet',
                'deadline'        => $dbProject['deadline'],
                'pendingCount'    => 0,
                'overdueCount'    => 0,
                'blockedCount'    => 0,
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

        $stagesEditable = false;
        $dbProject = Project::findById($id);
        if ($dbProject) {
            $dbRoles = ProjectMember::rolesForUser($id, Auth::id());
            if (!empty($dbRoles)) {
                if (in_array('client', $dbRoles, true)) {
                    header('Location: ' . url('client-portal/overview?id=' . $id));
                    exit;
                }

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
        }

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
                'stagesEditable' => $stagesEditable,
                'members'       => $members,
            ],
            $projectContext
        );

        $this->render('project/overview', $context);
    }
}