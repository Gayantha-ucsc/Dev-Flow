<?php
// Temporary - every  link routes here with mock data until each section gets a real controller + database-backed data.

class PageController extends Controller {

    private function mockContext(string $currentRoute, string $pageTitle): array {
        return [
            'pageTitle'          => $pageTitle,
            'currentProjectId'   => null,
            'currentProjectName' => 'Project Alpha',
            'userProjects' => [
                ['project_id' => 1, 'name' => 'Project Alpha'],
                ['project_id' => 2, 'name' => 'Project Beta'],
            ],
            'activeRole'  => 'team_lead',
            'userRoles'   => ['team_lead', 'developer'],
            'currentUser' => ['name' => 'User One', 'profile_picture' => null],
            'unreadCount' => 4,
            'currentRoute' => $currentRoute,
            'notifications' => [
                ['message' => 'Your task "Login Page UI" was approved', 'icon' => 'review', 'is_read' => false, 'created_at' => '10m ago', 'href' => '/tasks'],
                ['message' => 'New comment on "Database Schema" task', 'icon' => 'chat', 'is_read' => false, 'created_at' => '1h ago', 'href' => '/chat'],
                ['message' => 'Milestone payment requested', 'icon' => 'payment', 'is_read' => false, 'created_at' => '3h ago', 'href' => '/payment'],
                ['message' => 'Stage "Testing" is ready for review', 'icon' => 'tasks', 'is_read' => true, 'created_at' => 'Yesterday', 'href' => '/review'],
            ],
        ];

    }

    public function dashboard(): void {
        $hasProject = true;
        $context = $hasProject
            ? $this->mockContext('/dashboard', 'Dashboard')
            : array_merge($this->mockContext('/dashboard', 'Dashboard'), [
                'currentProjectId'   => null,
                'currentProjectName' => null,
                'userProjects'       => [],
                'userRoles'          => [],
            ]);

        $context = array_merge($context, [
            'icon'     => 'rocket',
            'heading'  => 'Ready to launch your first project?',
            'subtext'  => 'Create a project to start managing tasks, tracking progress, and collaborating with your team.',
            'ctaText'  => 'Create a Project',
            'ctaHref'  => '/projects/create',
        ]);

        $this->render('pages/dashboard', $context);
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