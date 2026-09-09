<?php

class AdminAuditController extends Controller {
    public function index(): void {
        $this->render('admin/audit/index', mockPageContext('/admin/audit', 'Audit Log', [
            'heading'     => 'Audit Log',
            'isAdminMode' => true,
        ]));
    }
}