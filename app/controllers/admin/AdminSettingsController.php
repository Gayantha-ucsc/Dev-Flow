<?php

class AdminSettingsController extends Controller {
    public function index(): void {
        $groups          = require __DIR__ . '/../../../config/mock/system-settings.php';
        $workflowTemplates = require __DIR__ . '/../../../config/mock/workflow-templates.php';

        $this->render('admin/settings/index', mockPageContext('/admin/settings', 'System Settings', [
            'heading'           => 'System Settings',
            'isAdminMode'       => true,
            'groups'            => $groups,
            'templateOptions'   => array_column($workflowTemplates, 'name'),
        ]));
    }
}