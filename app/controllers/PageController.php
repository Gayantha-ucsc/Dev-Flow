<?php
// Temporary - every  link routes here with mock data until each section gets a real controller + database-backed data.

class PageController extends Controller {

    private function mockContext(string $currentRoute, string $pageTitle): array {
        return [
            'pageTitle'          => $pageTitle,
            'currentProjectId'   => 1,
            'currentProjectName' => 'Project Alpha',
            'userProjects' => [
                ['project_id' => 1, 'name' => 'Project Alpha'],
                ['project_id' => 2, 'name' => 'Project Beta'],
            ],
            'activeRole'  => 'team_lead',
            'userRoles'   => ['team_lead', 'developer'],
            'currentUser' => ['name' => 'User One', 'profile_picture' => null],
            'unreadCount' => 3,
            'currentRoute' => $currentRoute,
        ];
    }

    public function dashboard(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/dashboard', 'Dashboard'), ['heading' => 'Dashboard']
        ));
    }
    public function projects(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/projects', 'Projects'), ['heading' => 'Projects']
        ));
    }
    public function team(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/team', 'Team & Roles'), ['heading' => 'Team & Roles']
        ));
    }
    public function tasks(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/tasks', 'Tasks'), ['heading' => 'Tasks']
        ));
    }
    public function review(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/review', 'Review & Approval'), ['heading' => 'Review & Approval']
        ));
    }
    public function chat(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/chat', 'Chat'), ['heading' => 'Chat']
        ));
    }
    public function reports(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/reports', 'Reports & Monitoring'), ['heading' => 'Reports & Monitoring']
        ));
    }
    public function payment(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/payment', 'Payment'), ['heading' => 'Payment']
        ));
    }
    public function settings(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/settings', 'Settings'), ['heading' => 'Settings']
        ));
    }
    public function notifications(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/notifications', 'Notifications'), ['heading' => 'Notifications']
        ));
    }
    public function profile(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/profile', 'Profile'), ['heading' => 'Profile']
        ));
    }
    public function logout(): void {
        $this->render('pages/placeholder', array_merge(
            $this->mockContext('/logout', 'Logged Out'), ['heading' => 'Logged Out (placeholder)']
        ));
    }
}