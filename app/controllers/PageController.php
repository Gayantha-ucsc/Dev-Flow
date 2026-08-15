<?php
// Temporary - every  link routes here with mock data until each section gets a real controller + database-backed data.

class PageController extends Controller {

    public function tasks(): void {
        $this->render('pages/placeholder', mockPageContext('/tasks', 'Tasks', ['heading' => 'Tasks']));
    }
    public function review(): void {
        $this->render('pages/placeholder', mockPageContext('/review', 'Review & Approval', ['heading' => 'Review & Approval']));
    }
    public function chat(): void {
        $this->render('pages/placeholder', mockPageContext('/chat', 'Chat', ['heading' => 'Chat']));
    }
    public function reports(): void {
        $this->render('pages/placeholder', mockPageContext('/reports', 'Reports & Monitoring', ['heading' => 'Reports & Monitoring']));
    }
    public function payment(): void {
        $this->render('pages/placeholder', mockPageContext('/payment', 'Payment', ['heading' => 'Payment']));
    }
    public function notifications(): void {
        $this->render('pages/placeholder', mockPageContext('/notifications', 'Notifications', ['heading' => 'Notifications']));
    }
    public function clientPortalReviews(): void {
        $this->render('pages/placeholder', mockPageContext('/client-portal/reviews', 'Approvals', ['heading' => 'Approvals']));
    }
}