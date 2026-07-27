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

    // Strip password_hash
    public static function withoutPassword(array $user): array {
        unset($user['password_hash']);
        return $user;
    }
}