<?php
// Temporary - every  link routes here with mock data until each section gets a real controller + database-backed data.

class PageController extends Controller {

    private function mockContext(string $currentRoute, string $pageTitle): array {
        $user          = require __DIR__ . '/../../config/mock/users.php';
        $roles         = require __DIR__ . '/../../config/mock/roles.php';
        $projects      = require __DIR__ . '/../../config/mock/projects.php';
        $notifications = require __DIR__ . '/../../config/mock/notifications.php';

        return array_merge(
            [
                'pageTitle'    => $pageTitle,
                'currentUser'  => $user,
                'currentRoute' => $currentRoute,
                'unreadCount'  => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
            ],
            $roles,
            $projects
        );
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
            'ctaTrigger' => 'project-wizard',
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