<?php

class AdminSettingsController extends Controller {
    public function index(): void {
        $this->render('admin/settings/index', mockPageContext('/admin/settings', 'System Settings', [
            'heading'     => 'System Settings',
            'isAdminMode' => true,
        ]));
    }
}