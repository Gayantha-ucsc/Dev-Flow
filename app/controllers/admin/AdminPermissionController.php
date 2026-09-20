<?php

class AdminPermissionController extends Controller {
    public function index(): void {
        $data = require __DIR__ . '/../../../config/mock/permissions.php';

        $roles = ['manager', 'team_lead', 'designer', 'developer', 'client'];

        $allowedCount    = 0;
        $restrictedCount = 0;
        foreach ($data['groups'] as $group) {
            foreach ($group['permissions'] as $permission) {
                foreach ($roles as $role) {
                    if (!empty($permission['roles'][$role])) {
                        $allowedCount++;
                    } else {
                        $restrictedCount++;
                    }
                }
            }
        }

        $this->render('admin/permissions/index', mockPageContext('/admin/permissions', 'Roles & Permissions', [
            'heading'         => 'Roles & Permissions',
            'isAdminMode'     => true,
            'roles'           => $roles,
            'groups'          => $data['groups'],
            'lastUpdatedBy'   => $data['lastUpdatedBy'],
            'lastUpdatedAt'   => $data['lastUpdatedAt'],
            'allowedCount'    => $allowedCount,
            'restrictedCount' => $restrictedCount,
        ]));
    }
}