<?php

class StageController extends Controller {

    // POST /projects/:id/stages  -  add a stage
    public function store(): void {
        [$project, $roles] = $this->authorize();
        $projectId = (int) $project['project_id'];
        $this->requireManage($roles, $projectId);
        $this->requireCsrf($projectId);

        $name  = trim($_POST['name'] ?? '');
        $error = $this->validateName($name);

        if ($error !== null) {
            Session::flash('error', $error);
        } else {
            Stage::create($projectId, $name);
            Session::flash('success', '"' . $name . '" was added to the workflow.');
        }

        $this->back($projectId);
    }

    // POST /stages/:id/update  -  rename a stage
    public function update(): void {
        $stage     = $this->findStageOr404();
        $projectId = (int) $stage['project_id'];

        [$project, $roles] = $this->authorize($projectId);
        $this->requireManage($roles, $projectId);
        $this->requireCsrf($projectId);

        $name  = trim($_POST['name'] ?? '');
        $error = $this->validateName($name);

        if ($error !== null) {
            Session::flash('error', $error);
            $this->back($projectId);
        }

        Stage::rename((int) $stage['stage_id'], $name);
        Session::flash('success', 'Stage renamed to "' . $name . '".');
        $this->back($projectId);
    }

    // POST /stages/:id/move  -  reorder a stage up or down one position
    public function move(): void {
        $stage     = $this->findStageOr404();
        $projectId = (int) $stage['project_id'];

        [$project, $roles] = $this->authorize($projectId);
        $this->requireManage($roles, $projectId);
        $this->requireCsrf($projectId);

        $direction = $_POST['direction'] ?? '';
        if (!Stage::move((int) $stage['stage_id'], $direction)) {
            Session::flash('error', 'Could not reorder that stage.');
        }

        $this->back($projectId);
    }

    // POST /stages/:id/delete  -  delete a stage
    public function destroy(): void {
        $stage     = $this->findStageOr404();
        $stageId   = (int) $stage['stage_id'];
        $projectId = (int) $stage['project_id'];

        [$project, $roles] = $this->authorize($projectId);
        $this->requireManage($roles, $projectId);
        $this->requireCsrf($projectId);

        if (Stage::taskCount($stageId) > 0) {
            Session::flash('error', '"' . $stage['name'] . '" still has tasks in it. Move or delete them first.');
            $this->back($projectId);
        }

        Stage::delete($stageId);
        Session::flash('success', '"' . $stage['name'] . '" was deleted.');
        $this->back($projectId);
    }

    // Helpers
    private function validateName(string $name): ?string {
        if ($name === '') {
            return 'Please enter a stage name.';
        }
        if (strlen($name) > 150) {
            return 'Stage name must be 150 characters or fewer.';
        }
        return null;
    }

    private function findStageOr404(): array {
        $stageId = (int) (Router::$params['id'] ?? 0);
        $stage   = Stage::findById($stageId);

        if (!$stage) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            exit;
        }

        return $stage;
    }

    private function authorize(?int $projectIdOverride = null): array {
        $projectId = $projectIdOverride ?? (int) (Router::$params['id'] ?? 0);

        $project = Project::findById($projectId);
        if (!$project) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            exit;
        }

        $roles = ProjectMember::rolesForUser($projectId, Auth::id());
        if (empty($roles)) {
            Session::flash('error', "You don't have access to that project.");
            header('Location: ' . url('projects'));
            exit;
        }

        return [$project, $roles];
    }

    private function canManage(array $roles): bool {
        return in_array('manager', $roles, true) || in_array('team_lead', $roles, true);
    }

    // only Manager/Team Lead may write.
    private function requireManage(array $roles, int $projectId): void {
        if (!$this->canManage($roles)) {
            Session::flash('error', 'Only a Manager or Team Lead can manage workflow stages.');
            $this->back($projectId);
        }
    }

    private function requireCsrf(int $projectId): void {
        if (!verifyCsrf()) {
            Session::flash('error', 'Your session expired. Please try again.');
            $this->back($projectId);
        }
    }

    // every write action lands back on the project page
    private function back(int $projectId): void {
        header('Location: ' . url('projects/' . $projectId));
        exit;
    }
}
