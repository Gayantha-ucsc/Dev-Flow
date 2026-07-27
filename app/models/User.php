<?php
class User {

    // Look up a user by email - used for login. 
    // Returns the raw DB row null if no match.
    public static function findByEmail(string $email): ?array {
        return DB::getInstance()->select('User', ['email' => $email]);
    }

    // Look up a user by id - used to re-fetch the current user fresh
    public static function findById(int $userId): ?array {
        return DB::getInstance()->select('User', ['user_id' => $userId]);
    }

    // Strip password_hash
    public static function withoutPassword(array $user): array {
        unset($user['password_hash']);
        return $user;
    }
}