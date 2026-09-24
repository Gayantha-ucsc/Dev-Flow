<?php

class AdminUserController extends Controller {
    private const PER_PAGE = 6;

    // Every action in this controller is admin-only
    private function requireAdmin(): void {
        $user = Auth::user();
        if (!$user || !$user['is_admin']) {
            http_response_code(403);
            require __DIR__ . '/../../views/errors/403.php';
            exit;
        }
    }

    private function back(string $path = 'admin/users'): void {
        header('Location: ' . url($path));
        exit;
    }

    private function checkCsrf(string $redirect): void {
        if (!verifyCsrf()) {
            Session::flash('error', 'Your session expired. Please try again.');
            $this->back($redirect);
        }
    }

    // Random temporary password that satisfies Validator::password rules
    private function generateTempPassword(): string {
        $pick = fn(string $set) => $set[random_int(0, strlen($set) - 1)];
        $chars = [$pick('ABCDEFGHJKLMNPQRSTUVWXYZ'), $pick('abcdefghijkmnpqrstuvwxyz'), $pick('23456789'), $pick('!@#$%*?')];
        $all = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        for ($i = 0; $i < 8; $i++) $chars[] = $pick($all);
        shuffle($chars);
        return implode('', $chars);
    }

    // Shared validation for create + update. Returns [cleanData, errors]
    private function validateInput(int $exceptId = 0): array {
        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'email'    => strtolower(trim($_POST['email'] ?? '')),
            'is_admin' => !empty($_POST['is_admin']),
        ];
        $errors = [];

        if ($e = Validator::name($data['name']))         $errors['name'] = $e;
        if ($e = Validator::username($data['username'])) $errors['username'] = $e;
        elseif (User::takenByOther('username', $data['username'], $exceptId)) $errors['username'] = 'That username is already taken.';
        if ($e = Validator::email($data['email']))       $errors['email'] = $e;
        elseif (User::takenByOther('email', $data['email'], $exceptId)) $errors['email'] = 'That email is already registered.';

        return [$data, $errors];
    }

    // READ: list with search / filters / pagination
    public function index(): void {
        $this->requireAdmin();

        $filters = [
            'q'      => trim((string) ($_GET['q'] ?? '')),
            'status' => $_GET['status'] ?? 'all',
            'type'   => $_GET['type'] ?? 'all',
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $totalCount = User::adminCount($filters);
        $totalPages = max(1, (int) ceil($totalCount / self::PER_PAGE));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * self::PER_PAGE;

        $newTemp = Session::get('new_temp_password');
        Session::remove('new_temp_password');

        $this->render('admin/users/index', mockPageContext('/admin/users', 'User Management', [
            'heading'         => 'User Management',
            'isAdminMode'     => true,
            'users'           => User::adminList($filters, self::PER_PAGE, $offset),
            'totalCount'      => $totalCount,
            'allCount'        => User::adminCount([]),
            'page'            => $page,
            'totalPages'      => $totalPages,
            'perPage'         => self::PER_PAGE,
            'rangeStart'      => $totalCount === 0 ? 0 : $offset + 1,
            'rangeEnd'        => min($offset + self::PER_PAGE, $totalCount),
            'filters'         => $filters,
            'newTempPassword' => $newTemp,
        ]));
    }

    // CREATE: form
    public function create(): void {
        $this->requireAdmin();
        $old    = Session::get('user_form_old', []);
        $errors = Session::get('user_form_errors', []);
        Session::remove('user_form_old');
        Session::remove('user_form_errors');

        $this->render('admin/users/create', mockPageContext('/admin/users', 'Create User', [
            'isAdminMode' => true, 'mode' => 'create', 'old' => $old, 'errors' => $errors, 'editId' => null,
        ]));
    }

    // CREATE: save
    public function store(): void {
        $this->requireAdmin();
        $this->checkCsrf('admin/users/create');

        [$data, $errors] = $this->validateInput();
        if ($errors) {
            Session::set('user_form_old', $data);
            Session::set('user_form_errors', $errors);
            $this->back('admin/users/create');
        }

        $tempPassword = $this->generateTempPassword();
        $data['password_hash'] = password_hash($tempPassword, PASSWORD_DEFAULT);
        User::createTemp($data);

        Session::set('new_temp_password', [
            'name' => $data['name'], 'username' => $data['username'], 'password' => $tempPassword,
        ]);
        Session::flash('success', 'User "' . $data['name'] . '" created.');
        $this->back();
    }

    // UPDATE: form
    public function edit(): void {
        $this->requireAdmin();
        $id   = (int) (Router::$params['id'] ?? 0);
        $user = User::findById($id);
        if (!$user) {
            Session::flash('error', 'User not found.');
            $this->back();
        }

        $old    = Session::get('user_form_old', null) ?? [
            'name' => $user['name'], 'username' => $user['username'],
            'email' => $user['email'], 'is_admin' => (bool) $user['is_admin'],
        ];
        $errors = Session::get('user_form_errors', []);
        Session::remove('user_form_old');
        Session::remove('user_form_errors');

        $this->render('admin/users/edit', mockPageContext('/admin/users', 'Edit User', [
            'isAdminMode' => true, 'mode' => 'edit', 'old' => $old, 'errors' => $errors, 'editId' => $id,
        ]));
    }

    // UPDATE: save
    public function update(): void {
        $this->requireAdmin();
        $id = (int) (Router::$params['id'] ?? 0);
        $this->checkCsrf("admin/users/$id/edit");

        if (!User::findById($id)) {
            Session::flash('error', 'User not found.');
            $this->back();
        }

        [$data, $errors] = $this->validateInput($id);
        if ($errors) {
            Session::set('user_form_old', $data);
            Session::set('user_form_errors', $errors);
            $this->back("admin/users/$id/edit");
        }

        $fields = ['name' => $data['name'], 'username' => $data['username'], 'email' => $data['email']];
        // Admins can't change their own admin flag (prevents locking everyone out)
        if ($id !== Auth::id()) {
            $fields['is_admin'] = $data['is_admin'] ? 1 : 0;
        }
        User::updateById($id, $fields);

        Session::flash('success', 'User "' . $data['name'] . '" updated.');
        $this->back();
    }

    // DELETE
    public function destroy(): void {
        $this->requireAdmin();
        $this->checkCsrf('admin/users');
        $id = (int) (Router::$params['id'] ?? 0);

        $user = User::findById($id);
        if (!$user) {
            Session::flash('error', 'User not found.');
            $this->back();
        }
        if ($id === Auth::id()) {
            Session::flash('error', 'You cannot delete your own account here.');
            $this->back();
        }
        if (User::projectLinkCount($id) > 0) {
            Session::flash('error', $user['name'] . ' still belongs to (or owns) projects. Deactivate the account instead, or remove them from their projects first.');
            $this->back();
        }

        try {
            User::deleteById($id);
            Session::flash('success', 'User "' . $user['name'] . '" deleted.');
        } catch (Throwable $e) {
            Session::flash('error', 'This user has related records and cannot be deleted. Deactivate the account instead.');
        }
        $this->back();
    }
}