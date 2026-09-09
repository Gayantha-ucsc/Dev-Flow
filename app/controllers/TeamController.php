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
        $directory = require __DIR__ . '/../../config/mock/user-directory.php';

        $existingIds = array_column($team['members'], 'user_id');
        foreach ($team['pendingApprovals'] as $pending) {
            $existingIds[] = $pending['user_id'];
        }

        $searchable = array_values(array_filter(
            $directory,
            fn($u) => !in_array($u['user_id'], $existingIds, true)
        ));

        $context = array_merge($this->baseContext('/team', 'Add Member'), [
            'members'          => $team['members'],
            'hasTeamLead'      => projectHasTeamLead($team['members']),
            'searchableUsers'  => $searchable,
        ]);

        $this->render('team/add-member', $context);
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