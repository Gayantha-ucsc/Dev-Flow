<?php

class AdminUserController extends Controller {
    public function index(): void {
        $this->render('admin/users/index', mockPageContext('/admin/users', 'User Management', [
            'heading'     => 'User Management',
            'isAdminMode' => true,
        ]));
    }
}