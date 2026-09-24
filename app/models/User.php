<?php
class User {

    // Look up a user by username
    // Returns null if no match.
    public static function findByUsername(string $username): ?array {
        return DB::getInstance()->select('User', ['username' => $username]);
    }

    public static function findByEmail(string $email): ?array {
        return DB::getInstance()->select('User', ['email' => $email]);
    }

    // Look up a user by id
    public static function findById(int $userId): ?array {
        return DB::getInstance()->select('User', ['user_id' => $userId]);
    }

    public static function usernameExists(string $username): bool {
        return DB::getInstance()->exists('User', ['username' => $username]);
    }

    public static function emailExists(string $email): bool {
        return DB::getInstance()->exists('User', ['email' => $email]);
    }

    public static function create(array $data): int {
        return DB::getInstance()->insert('User', [
            'name'          => $data['name'],
            'username'      => $data['username'],
            'email'         => $data['email'],
            'password_hash' => $data['password_hash'],
        ]);
    }

    // Strip password_hash
    public static function withoutPassword(array $user): array {
        unset($user['password_hash']);
        return $user;
    }

    // Admin user management 

    // Filtered, paginated list for /admin/users. $f = ['q','status','type']
    public static function adminList(array $f, int $limit, int $offset): array {
        [$where, $params] = self::adminWhere($f);
        $sql = "SELECT *, created_at AS joined_at FROM `User` WHERE $where ORDER BY created_at DESC, user_id DESC LIMIT $limit OFFSET $offset";
        $rows = DB::getInstance()->query($sql, $params);
        foreach ($rows as &$r) {
            $r['user_id'] = (int) $r['user_id'];
            foreach (['is_admin', 'is_active', 'is_temp', 'is_temp_password_changed'] as $k) {
                $r[$k] = (bool) $r[$k];
            }
        }
        return $rows;
    }

    public static function adminCount(array $f): int {
        [$where, $params] = self::adminWhere($f);
        $rows = DB::getInstance()->query("SELECT COUNT(*) AS c FROM `User` WHERE $where", $params);
        return (int) $rows[0]['c'];
    }

    private static function adminWhere(array $f): array {
        $where  = ['1=1'];
        $params = [];

        if (($f['q'] ?? '') !== '') {
            $where[] = '(name LIKE ? OR email LIKE ? OR username LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like, $like);
        }
        if (($f['status'] ?? 'all') === 'active')   $where[] = 'is_active = 1';
        if (($f['status'] ?? 'all') === 'inactive') $where[] = 'is_active = 0';

        switch ($f['type'] ?? 'all') {
            case 'admin':     $where[] = 'is_admin = 1'; break;
            case 'temporary': $where[] = 'is_admin = 0 AND is_temp = 1 AND is_temp_password_changed = 0'; break;
            case 'standard':  $where[] = 'is_admin = 0 AND NOT (is_temp = 1 AND is_temp_password_changed = 0)'; break;
        }
        return [implode(' AND ', $where), $params];
    }

    // True if username/email belongs to a different user than $exceptId
    public static function takenByOther(string $field, string $value, int $exceptId = 0): bool {
        $field = $field === 'email' ? 'email' : 'username';
        $rows = DB::getInstance()->query("SELECT user_id FROM `User` WHERE $field = ? AND user_id <> ? LIMIT 1", [$value, $exceptId]);
        return !empty($rows);
    }

    // Admin-created account: temporary password
    public static function createTemp(array $d): int {
        return DB::getInstance()->insert('User', [
            'name'          => $d['name'],
            'username'      => $d['username'],
            'email'         => $d['email'],
            'password_hash' => $d['password_hash'],
            'is_admin'      => $d['is_admin'] ? 1 : 0,
            'is_temp'       => 1,
        ]);
    }

    public static function updateById(int $id, array $data): void {
        DB::getInstance()->update('User', $data, ['user_id' => $id]);
    }

    public static function deleteById(int $id): void {
        DB::getInstance()->delete('User', ['user_id' => $id]);
    }

    // Number of project memberships / owned projects that block deletion
    public static function projectLinkCount(int $id): int {
        return DB::getInstance()->count('ProjectMember', ['user_id' => $id])
             + DB::getInstance()->count('Project', ['created_by' => $id]);
    }
}