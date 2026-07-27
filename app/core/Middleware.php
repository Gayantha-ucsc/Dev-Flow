<?php

class Middleware {

    // Require a logged-in, still-active user; redirects to /login otherwise
    public static function requireAuth(): void {
        if (!Auth::check()) {
            Session::flash('error', 'Please log in to continue.');
            header('Location: ' . url('login'));
            exit;
        }

        $user = Auth::user();

        if (!$user) {
            // user_id in the session doesn't match any row anymore
            Auth::logout();
            Session::start();
            Session::flash('error', 'Please log in to continue.');
            header('Location: ' . url('login'));
            exit;
        }

        if (!$user['is_active']) {
            Auth::logout();
            Session::start();
            Session::flash('error', 'Your account has been deactivated. Contact your administrator.');
            header('Location: ' . url('login'));
            exit;
        }
    }

    public static function guestOnly(): void {
        if (Auth::check() && Auth::user()) {
            header('Location: ' . url('dashboard'));
            exit;
        }
    }
}