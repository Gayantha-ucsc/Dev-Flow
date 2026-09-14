<?php

class ClientPortalController extends Controller {

    private function resolveClientProject(): ?array {
        $projectsList = projectsListForCurrentUser();
        $roles        = splitProjectRolesByClient($projectsList);

        if (empty($roles['client'])) {
            return null;
        }

        $bundles     = buildClientProjectBundles($roles['client']);
        $requestedId = isset($_GET['id']) ? (int) $_GET['id'] : null;

        foreach ($bundles as $bundle) {
            if ($bundle['id'] === $requestedId) {
                return $bundle;
            }
        }

        return $bundles[0];
    }

    public function overview(): void {
        $project = $this->resolveClientProject();

        if ($project === null) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        setCurrentProjectId($project['id']);

        $user           = currentUserContext();
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';

        $context = array_merge(
            [
                'pageTitle'     => $project['name'],
                'currentUser'   => $user,
                'currentRoute'  => '/client-portal/overview',
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
                'project'       => $project,
            ],
            $projectContext
        );

        $this->render('client-portal/overview', $context);
    }

    public function reviews(): void {
        $this->render('pages/placeholder', mockPageContext('/client-portal/reviews', 'Approvals', ['heading' => 'Approvals']));
    }

    public function history(): void {
        $this->render('pages/placeholder', mockPageContext('/client-portal/history', 'Approval History', ['heading' => 'Approval History']));
    }
}