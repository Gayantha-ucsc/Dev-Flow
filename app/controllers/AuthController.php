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
        if (!verifyCsrf()) {
            Session::flash('error', 'Your session expired. Please try again.');
            header('Location: ' . url('login'));
            exit;
        }

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

    // GET /register
    public function showRegister(): void {
        Middleware::guestOnly();

        $old = [
            'name'     => Session::getOld('name'),
            'username' => Session::getOld('username'),
            'email'    => Session::getOld('email'),
        ];
        Session::clearOld();

        $this->render('auth/register', [
            'pageTitle' => 'Create Account',
            'old'       => $old,
        ], 'auth');
    }

    // POST /register
    public function register(): void {
        if (!verifyCsrf()) {
            Session::flash('error', 'Your session expired. Please try again.');
            header('Location: ' . url('register'));
            exit;
        }

        $name            = trim($_POST['name'] ?? '');
        $username        = trim($_POST['username'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        $email    = mb_strtolower($email);
        $username = mb_strtolower($username);

        $errors = [];

        foreach ([
            Validator::name($name),
            Validator::username($username),
            Validator::email($email),
            Validator::password($password),
            Validator::passwordsMatch($password, $confirmPassword),
        ] as $error) {
            if ($error !== null) {
                $errors[] = $error;
            }
        }

        if (empty($errors)) {
            if (User::usernameExists($username)) {
                $errors[] = 'That username is already taken.';
            }
            if (User::emailExists($email)) {
                $errors[] = 'An account with that email already exists.';
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Session::flash('error', $error);
            }
            Session::flashOld(['name' => $name, 'username' => $username, 'email' => $email]);
            header('Location: ' . url('register'));
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $userId = User::create([
            'name'          => $name,
            'username'      => $username,
            'email'         => $email,
            'password_hash' => $passwordHash,
        ]);

        Session::flash('success', 'Account created! Please log in.');
        header('Location: ' . url('login'));
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