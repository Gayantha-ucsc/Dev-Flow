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

    private function statusLabel(string $status): string {
        return match ($status) {
            'pending_review' => 'Pending Review',
            'blocked'        => 'Blocked',
            'overdue'        => 'Overdue',
            default          => ucfirst($status),
        };
    }

    private function statusTone(string $status): string {
        return match ($status) {
            'blocked'        => 'danger',
            'overdue'        => 'warning',
            'pending_review' => 'primary',
            default          => 'neutral',
        };
    }

    public function index(): void {
        $projectContext = currentProjectContext();
        $projectId      = $projectContext['currentProjectId'];
        $isTeamLead     = $projectContext['activeRole'] === 'team_lead';

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
            'percent' => $projectMeta['percent']     ?? 0,
            'blocked' => $projectMeta['blockedCount'] ?? 0,
            'overdue' => $projectMeta['overdueCount'] ?? 0,
            'pending' => $projectMeta['pendingCount'] ?? 0,
        ];

        // Stage progress panel: percent = tasksApproved / tasksTotal,
        $stageProgress = [];
        foreach ($stageDetail as $index => $stage) {
            $total   = $stage['tasksTotal'] ?? 0;
            $percent = $total > 0 ? (int) round(($stage['tasksApproved'] / $total) * 100) : 0;

            $blockedInStage = 0;
            $overdueInStage = 0;
            foreach (($taskData[$index] ?? []) as $task) {
                if ($task['status'] === 'blocked') $blockedInStage++;
                if ($task['status'] === 'overdue') $overdueInStage++;
            }

            $stageProgress[] = [
                'name'       => $stage['name'],
                'percent'    => $percent,
                'status'     => $stage['status'],
                'approved'   => $stage['tasksApproved'] ?? 0,
                'total'      => $total,
                'blockedTag' => $blockedInStage,
                'overdueTag' => $overdueInStage,
            ];
        }

        // Team workload + contribution summary
        $agg = [];
        $bottlenecks = [];
        foreach ($taskData as $stageIndex => $stageTasks) {
            $stageName = $stageDetail[$stageIndex]['name'] ?? '';
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

                // Bottleneck drill-down (Team Lead only)
                if (in_array($task['status'], ['blocked', 'overdue', 'pending_review'], true)) {
                    $bottlenecks[] = [
                        'name'       => $task['name'],
                        'assignee'   => $task['assignees'][0] ?? 'Unassigned',
                        'stage'      => $stageName,
                        'status'     => $task['status'],
                        'statusLabel' => $this->statusLabel($task['status']),
                        'statusTone'  => $this->statusTone($task['status']),
                        'days'       => $reportsMock['bottleneckDays'][$projectId][$task['name']] ?? 1,
                    ];
                }
            }
        }
        usort($bottlenecks, fn($a, $b) => $b['days'] <=> $a['days']);

        $maxTotal = 1;
        foreach ($agg as $row) {
            $maxTotal = max($maxTotal, $row['total']);
        }

        $teamWorkload = [];
        $contribution = [];
        foreach ($agg as $name => $row) {
            arsort($row['types']);
            $topType   = array_key_first($row['types']) ?? 'other';
            $roleLabel = $this->roleLabelFromType($topType);

            $teamWorkload[] = [
                'name'       => $name,
                'role'       => $roleLabel,
                'total'      => $row['total'],
                'open'       => $row['open'],
                'barPercent' => (int) round(($row['total'] / $maxTotal) * 100),
            ];

            $decided = $row['completed']; // approved + rejected, rejected = 0
            $contribution[] = [
                'name'         => $name,
                'role'         => $roleLabel,
                'completed'    => $row['completed'],
                'inProgress'   => $row['open'],
                'avgRevision'  => 1.0,
                'approvalRate' => $decided > 0 ? 100 : null,
            ];
        }

        // Sort workload by open items first
        usort($teamWorkload, fn($a, $b) => $b['open'] <=> $a['open'] ?: $b['total'] <=> $a['total']);
        usort($contribution, fn($a, $b) => $b['completed'] <=> $a['completed']);

        $approvalHistory = $reportsMock[$projectId]['approvalHistory'] ?? [];

        $context = array_merge($this->baseContext('/reports', 'Reports & Monitoring'), [
            'projectName'     => $projectMeta['name'] ?? $projectContext['currentProjectName'],
            'isTeamLead'      => $isTeamLead,
            'stats'           => $stats,
            'stageProgress'   => $stageProgress,
            'teamWorkload'    => $teamWorkload,
            'contribution'    => $contribution,
            'approvalHistory' => $approvalHistory,
            'bottlenecks'     => $bottlenecks,
        ]);

        $this->render('reports/dashboard', $context);
    }
}