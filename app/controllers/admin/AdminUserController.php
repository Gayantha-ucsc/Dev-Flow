<?php

class AdminUserController extends Controller {
    private const PER_PAGE = 6;

    public function index(): void {
        $allUsers = require __DIR__ . '/../../../config/mock/admin-users.php';

        $search = trim((string) ($_GET['q'] ?? ''));
        $status = $_GET['status'] ?? 'all';   // all | active | inactive
        $type   = $_GET['type'] ?? 'all';     // all | standard | temporary | admin
        $page   = max(1, (int) ($_GET['page'] ?? 1));

        $filtered = array_values(array_filter($allUsers, function ($user) use ($search, $status, $type) {
            if ($search !== '') {
                $needle = strtolower($search);
                $haystack = strtolower($user['name'] . ' ' . $user['email']);
                if (!str_contains($haystack, $needle)) {
                    return false;
                }
            }

            if ($status === 'active' && !$user['is_active']) {
                return false;
            }
            if ($status === 'inactive' && $user['is_active']) {
                return false;
            }

            $accountType = adminAccountType($user);
            if ($type !== 'all' && $type !== $accountType) {
                return false;
            }

            return true;
        }));

        $totalCount = count($filtered);
        $totalPages = max(1, (int) ceil($totalCount / self::PER_PAGE));
        $page       = min($page, $totalPages);

        $offset = ($page - 1) * self::PER_PAGE;
        $pageUsers = array_slice($filtered, $offset, self::PER_PAGE);

        $this->render('admin/users/index', mockPageContext('/admin/users', 'User Management', [
            'heading'      => 'User Management',
            'isAdminMode'  => true,
            'users'        => $pageUsers,
            'totalCount'   => $totalCount,
            'allCount'     => count($allUsers),
            'page'         => $page,
            'totalPages'   => $totalPages,
            'perPage'      => self::PER_PAGE,
            'rangeStart'   => $totalCount === 0 ? 0 : $offset + 1,
            'rangeEnd'     => min($offset + self::PER_PAGE, $totalCount),
            'filters'      => ['q' => $search, 'status' => $status, 'type' => $type],
        ]));
    }
}