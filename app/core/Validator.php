<?php

// Validator — plain static helper methods
class Validator {

    public static function name(string $name): ?string {
        if ($name === '') {
            return 'Please enter your name.';
        }
        if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            return 'Name must be between 2 and 100 characters.';
        }
        if (!preg_match("/^[\p{L}][\p{L}\s\-\']*$/u", $name)) {
            return 'Name can only contain letters, spaces, hyphens and apostrophes.';
        }
        return null;
    }

    public static function username(string $username): ?string {
        if ($username === '') {
            return 'Please choose a username.';
        }
        if (mb_strlen($username) < 3 || mb_strlen($username) > 30) {
            return 'Username must be between 3 and 30 characters.';
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return 'Username can only contain letters, numbers and underscores.';
        }
        if (ctype_digit($username)) {
            return 'Username cannot be only numbers.';
        }
        return null;
    }

    public static function email(string $email): ?string {
        if ($email === '') {
            return 'Please enter your email address.';
        }
        if (mb_strlen($email) > 190) { // matches User.email
            return 'Email address is too long.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Please enter a valid email address.';
        }
        return null;
    }

    public static function password(string $password): ?string {
        if ($password === '') {
            return 'Please enter a password.';
        }
        if (strlen($password) < 8) {
            return 'Password must be at least 8 characters long.';
        }
        if (strlen($password) > 72) { // bcrypt silently ignores bytes past 72
            return 'Password must be no more than 72 characters long.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            return 'Password must include at least one lowercase letter.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Password must include at least one uppercase letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            return 'Password must include at least one number.';
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return 'Password must include at least one special character.';
        }
        return null;
    }

    public static function passwordsMatch(string $password, string $confirm): ?string {
        if ($password !== $confirm) {
            return 'Passwords do not match.';
        }
        return null;
    }
}