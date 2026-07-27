<?php

class Auth {

    public static function attempt(string $email, string $password): ?array {
        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        if (!$user['is_active']) {
            return null;
        }

        return User::withoutPassword($user);
    }

    public static function login(array $user): void {
        Session::regenerate();
        $_SESSION['user_id'] = $user['user_id'];
    }

    public static function logout(): void {
        Session::destroy();
    }

    public static function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    // Current user
    public static function user(): ?array {
        static $cached = null;
        static $fetched = false;

        if ($fetched) {
            return $cached;
        }
        $fetched = true;

        if (!self::check()) {
            return null;
        }

        $user = User::findById(self::id());
        $cached = $user ? User::withoutPassword($user) : null;
        return $cached;
    }
}