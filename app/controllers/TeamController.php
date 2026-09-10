<?php
class TeamController extends Controller {

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

    private function teamForProject(int $projectId): array {
        $teamData = require __DIR__ . '/../../config/mock/team-members.php';
        return $teamData[$projectId] ?? ['members' => [], 'pendingApprovals' => []];
    }

    public function index(): void {
        $projectId = currentProjectContext()['currentProjectId'];
        $team      = $this->teamForProject($projectId);

        $context = array_merge($this->baseContext('/team', 'Team & Roles'), [
            'members'          => $team['members'],
            'pendingApprovals' => $team['pendingApprovals'],
            'hasTeamLead'      => projectHasTeamLead($team['members']),
        ]);

        $this->render('team/index', $context);
    }

    public function addMember(): void {
        $projectId = currentProjectContext()['currentProjectId'];
        $team      = $this->teamForProject($projectId);

        $context = array_merge($this->baseContext('/team', 'Add Member'), [
            'members'     => $team['members'],
            'hasTeamLead' => projectHasTeamLead($team['members']),
        ]);

        $this->render('team/add-member', $context);
    }

    public function searchUsers(): void {
        $query = trim($_GET['q'] ?? '');

        header('Content-Type: application/json');

        if (mb_strlen($query) < 3) {
            echo json_encode(['results' => []]);
            return;
        }

        $projectId = currentProjectContext()['currentProjectId'];
        $team      = $this->teamForProject($projectId);
        $directory = require __DIR__ . '/../../config/mock/user-directory.php';

        $existingIds = array_column($team['members'], 'user_id');
        foreach ($team['pendingApprovals'] as $pending) {
            $existingIds[] = $pending['user_id'];
        }

        $needle = mb_strtolower($query);
        $matches = array_values(array_filter(
            $directory,
            function ($u) use ($needle, $existingIds) {
                if (in_array($u['user_id'], $existingIds, true)) {
                    return false;
                }
                return str_contains(mb_strtolower($u['name']), $needle)
                    || str_contains(mb_strtolower($u['email']), $needle);
            }
        ));

        // Capped, not the full result set — a targeted lookup, not a browse.
        $matches = array_slice($matches, 0, 8);

        echo json_encode(['results' => $matches]);
    }

    public function approvals(): void {
        $projectId = currentProjectContext()['currentProjectId'];
        $team      = $this->teamForProject($projectId);

        $context = array_merge($this->baseContext('/team', 'Member Approval Queue'), [
            'pendingApprovals' => $team['pendingApprovals'],
            'hasTeamLead'      => projectHasTeamLead($team['members']),
            'activeRole'       => currentProjectContext()['activeRole'],
        ]);

        $this->render('team/approvals', $context);
    }

    public function inviteClient(): void {
        $projectId = currentProjectContext()['currentProjectId'];
        $team      = $this->teamForProject($projectId);

        $context = array_merge($this->baseContext('/team', 'Invite Client'), [
            'members' => $team['members'],
        ]);

        $this->render('team/invite-client', $context);
    }
}