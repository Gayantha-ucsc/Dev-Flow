<?php

class AuthController extends Controller {

    // GET /login
    public function showLogin(): void {
        Middleware::guestOnly();

        $this->render('auth/login', [
            'pageTitle' => 'Log In',
        ], 'auth');
    }

    // POST /login
    public function login(): void {
        $email    = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            Session::flash('error', 'Please enter both your email and password.');
            header('Location: ' . url('login'));
            exit;
        }

        $user = Auth::attempt($email, $password);

        if (!$user) {
            Session::flash('error', 'Incorrect email or password.');
            header('Location: ' . url('login'));
            exit;
        }

        Auth::login($user);
        Session::flash('success', 'Welcome back, ' . $user['name'] . '.');
        header('Location: ' . url('dashboard'));
        exit;
    }

    // GET /logout
    public function logout(): void {
        Auth::logout();
        Session::start();
        Session::flash('success', 'You have been logged out.');
        header('Location: ' . url('login'));
        exit;
    }
}