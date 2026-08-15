<?php

class AdminTemplateController extends Controller {
    public function index(): void {
        $this->render('admin/templates/index', mockPageContext('/admin/templates', 'Workflow Templates', [
            'heading'     => 'Workflow Templates',
            'isAdminMode' => true,
        ]));
    }
}