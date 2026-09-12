<?php
class ReportController extends Controller {

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

    private function roleLabelFromType(string $type): string {
        return match ($type) {
            'backend'  => 'Backend',
            'frontend' => 'Frontend',
            'design'   => 'Design',
            'qa'       => 'QA',
            'research' => 'Research',
            'devops'   => 'DevOps',
            'delivery' => 'Delivery',
            'review'   => 'Review',
            default    => ucfirst($type),
        };
    }

    public function index(): void {
        $projectId = currentProjectContext()['currentProjectId'];

        $projectsList = require __DIR__ . '/../../config/mock/projects-list.php';
        $projectMeta  = null;
        foreach ($projectsList as $row) {
            if ($row['id'] === $projectId) {
                $projectMeta = $row;
                break;
            }
        }

        $stageDetail = (require __DIR__ . '/../../config/mock/project-detail.php')[$projectId]['stages'] ?? [];
        $taskData    = (require __DIR__ . '/../../config/mock/project-tasks.php')[$projectId] ?? [];
        $reportsMock = require __DIR__ . '/../../config/mock/reports.php';
        $currentUser = require __DIR__ . '/../../config/mock/users.php';

        // Top stat cards
        $stats = [
            'percent'  => $projectMeta['percent']      ?? 0,
            'blocked'  => $projectMeta['blockedCount']  ?? 0,
            'overdue'  => $projectMeta['overdueCount']  ?? 0,
            'pending'  => $projectMeta['pendingCount']  ?? 0,
        ];

        // ---- Stage progress panel: percent = tasksApproved / tasksTotal.
        $stageProgress = array_map(function ($stage) {
            $total   = $stage['tasksTotal'] ?? 0;
            $percent = $total > 0 ? (int) round(($stage['tasksApproved'] / $total) * 100) : 0;
            return [
                'name'    => $stage['name'],
                'percent' => $percent,
                'status'  => $stage['status'],
            ];
        }, $stageDetail);

        // Team workload + contribution summary
        $agg = [];
        foreach ($taskData as $stageTasks) {
            foreach ($stageTasks as $task) {
                foreach ($task['assignees'] as $name) {
                    if ($name === $currentUser['name']) {
                        continue;
                    }
                    if (!isset($agg[$name])) {
                        $agg[$name] = ['total' => 0, 'completed' => 0, 'open' => 0, 'types' => []];
                    }
                    $agg[$name]['total']++;
                    if ($task['status'] === 'approved') {
                        $agg[$name]['completed']++;
                    } else {
                        $agg[$name]['open']++;
                    }
                    $agg[$name]['types'][$task['type']] = ($agg[$name]['types'][$task['type']] ?? 0) + 1;
                }
            }
        }

        $maxTotal = 1;
        foreach ($agg as $row) {
            $maxTotal = max($maxTotal, $row['total']);
        }

        $teamWorkload = [];
        $contribution = [];
        foreach ($agg as $name => $row) {
            arsort($row['types']);
            $topType = array_key_first($row['types']) ?? 'other';

            $teamWorkload[] = [
                'name'        => $name,
                'total'       => $row['total'],
                'open'        => $row['open'],
                'barPercent'  => (int) round(($row['total'] / $maxTotal) * 100),
            ];

            $decided = $row['completed']; // approved + rejected, rejected = 0
            $contribution[] = [
                'name'           => $name,
                'role'           => $this->roleLabelFromType($topType),
                'completed'      => $row['completed'],
                'inProgress'     => $row['open'],
                'avgRevision'    => 1.0,
                'approvalRate'   => $decided > 0 ? 100 : null,
            ];
        }

        // Sort workload by open items first
        usort($teamWorkload, fn($a, $b) => $b['open'] <=> $a['open'] ?: $b['total'] <=> $a['total']);
        usort($contribution, fn($a, $b) => $b['completed'] <=> $a['completed']);

        $approvalHistory = $reportsMock[$projectId]['approvalHistory'] ?? [];

        $context = array_merge($this->baseContext('/reports', 'Reports & Monitoring'), [
            'projectName'     => $projectMeta['name'] ?? currentProjectContext()['currentProjectName'],
            'stats'           => $stats,
            'stageProgress'   => $stageProgress,
            'teamWorkload'    => $teamWorkload,
            'contribution'    => $contribution,
            'approvalHistory' => $approvalHistory,
        ]);

        $this->render('reports/dashboard', $context);
    }
}