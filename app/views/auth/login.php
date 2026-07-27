<div class="auth-card">
    <img src="<?= url('assets/img/logo.png') ?>" alt="Dev-Flow" class="auth-logo">
    <h1 class="auth-title">Log in to Dev-Flow</h1>

    <form method="POST" action="<?= url('login') ?>" class="auth-form" novalidate>
        <label class="auth-label" for="email">Email</label>
        <input class="auth-input" type="email" id="email" name="email" required autofocus>

        <label class="auth-label" for="password">Password</label>
        <input class="auth-input" type="password" id="password" name="password" required>

        <button type="submit" class="auth-submit">Log In</button>
    </form>
</div>