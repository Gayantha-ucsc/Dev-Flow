<?php

class AdminTemplateController extends Controller {
    public function index(): void {
        $allTemplates = require __DIR__ . '/../../../config/mock/workflow-templates.php';

        $search      = trim((string) ($_GET['q'] ?? ''));
        $defaultOnly = isset($_GET['default_only']) && $_GET['default_only'] === '1';

        $templates = array_values(array_filter($allTemplates, function ($tpl) use ($search, $defaultOnly) {
            if ($defaultOnly && empty($tpl['is_system_default'])) {
                return false;
            }
            if ($search === '') {
                return true;
            }
            $needle   = strtolower($search);
            $haystack = strtolower($tpl['name'] . ' ' . $tpl['description'] . ' ' . implode(' ', $tpl['stages']));
            return str_contains($haystack, $needle);
        }));

        $this->render('admin/templates/index', mockPageContext('/admin/templates', 'Workflow Templates', [
            'heading'      => 'Workflow Templates',
            'isAdminMode'  => true,
            'templates'    => $templates,
            'allCount'     => count($allTemplates),
            'filters'      => ['q' => $search, 'default_only' => $defaultOnly],
        ]));
    }
}