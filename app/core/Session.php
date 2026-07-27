<?php

class Session {
    private static bool $started = false;

    // Starts (or resumes) the session
    public static function start(): void {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,   // blocks XSS-based session theft
                'samesite' => 'Lax',
                // 'secure' => true,   // uncomment once served over HTTPS
            ]);
            session_start();
        }

        self::$started = true;

        $app = require __DIR__ . '/../../config/app.php';
        self::enforceTimeout($app['session_lifetime'] ?? 3600);
    }

    //  if the session has been idle longer than the configured lifetime, wipe it and start clean.
    private static function enforceTimeout(int $lifetimeSeconds): void {
        $lastActivity = $_SESSION['_last_activity'] ?? null;

        if ($lastActivity !== null && (time() - $lastActivity) > $lifetimeSeconds) {
            $_SESSION = [];
        }

        $_SESSION['_last_activity'] = time();
    }

    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    //  Full logout / timeout wipe.
    public static function destroy(): void {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 3600,
                $params['path'], $params['domain'] ?? '',
                $params['secure'] ?? false, $params['httponly'] ?? true
            );
            session_destroy();
        }

        self::$started = false;
    }

    public static function regenerate(): void {
        session_regenerate_id(true);
    }

    // ---------------- Flash messages ----------------
    public static function flash(string $key, string $message): void {
        $_SESSION['_flash'][$key][] = $message;
    }

    // Returns all queued messages for $key (empty array if none)
    public static function getFlash(string $key): array {
        $messages = $_SESSION['_flash'][$key] ?? [];
        unset($_SESSION['_flash'][$key]);
        return $messages;
    }
}