<div class="auth-card">
    <img src="<?= url('assets/img/logo.png') ?>" alt="Dev-Flow" class="auth-logo">
    <h1 class="auth-title">Create your Dev-Flow account</h1>

    <form method="POST" action="<?= url('register') ?>" class="auth-form" novalidate>
        <?= csrfField() ?>

        <label class="auth-label" for="name">Full Name</label>
        <input class="auth-input" type="text" id="name" name="name" required autofocus
               autocomplete="name" value="<?= htmlspecialchars($old['name']) ?>">

        <label class="auth-label" for="username">Username</label>
        <input class="auth-input" type="text" id="username" name="username" required
               autocomplete="username" value="<?= htmlspecialchars($old['username']) ?>">
        <p class="auth-hint">3-30 characters — letters, numbers and underscores only.</p>

        <label class="auth-label" for="email">Email</label>
        <input class="auth-input" type="email" id="email" name="email" required
               autocomplete="email" value="<?= htmlspecialchars($old['email']) ?>">

        <label class="auth-label" for="password">Password</label>
        <input class="auth-input" type="password" id="password" name="password" required
               autocomplete="new-password">
        <p class="auth-hint">At least 8 characters, with an uppercase letter, a lowercase letter, a number and a special character.</p>

        <label class="auth-label" for="confirm_password">Confirm Password</label>
        <input class="auth-input" type="password" id="confirm_password" name="confirm_password" required
               autocomplete="new-password">

        <button type="submit" class="auth-submit">Create Account</button>
    </form>

    <p class="auth-switch">
        Already have an account? <a href="<?= url('login') ?>">Log in</a>
    </p>
</div>