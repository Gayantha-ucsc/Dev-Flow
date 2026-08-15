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
        $username = trim($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            Session::flash('error', 'Please enter both your username and password.');
            header('Location: ' . url('login'));
            exit;
        }

        $user = Auth::attempt($username, $password);

        if (!$user) {
            if (Auth::isDeactivated($username, $password)) {
                Session::flash('error', 'This account has been deactivated. Contact your administrator.');
            } else {
                Session::flash('error', 'Incorrect username or password.');
            }
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