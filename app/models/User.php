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
}