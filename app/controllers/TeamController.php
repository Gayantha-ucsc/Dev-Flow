<?php
class TeamController extends Controller {

    private function baseContext(string $currentRoute, string $pageTitle): array {
        $user           = currentUserContext();
        $projectContext = currentProjectContext();
        $notifications  = currentUserNotifications();

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

    private function teamForProject(?int $projectId): array {
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