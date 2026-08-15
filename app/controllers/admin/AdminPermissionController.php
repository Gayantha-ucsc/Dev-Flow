<?php

class AdminPermissionController extends Controller {
    public function index(): void {
        $this->render('admin/permissions/index', mockPageContext('/admin/permissions', 'Roles & Permissions', [
            'heading'     => 'Roles & Permissions',
            'isAdminMode' => true,
        ]));
    }
}