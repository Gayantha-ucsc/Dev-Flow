<div class="login-split">

    <div class="login-visual">
        <div class="login-visual__brand">
            <img src="<?= url('assets/img/logo.png') ?>" alt="" class="login-visual__logo">
            <span>DevFlow</span>
        </div>

        <div class="login-visual__pipeline" role="img" aria-label="Design, then Review, then Approved, then Delivered">
            <div class="login-visual__step login-visual__step--done">
                <span class="login-visual__dot"><?= renderIcon('palette') ?></span>
                <span class="login-visual__label">Design</span>
            </div>
            <div class="login-visual__step login-visual__step--done">
                <span class="login-visual__dot"><?= renderIcon('eye') ?></span>
                <span class="login-visual__label">Review</span>
            </div>
            <div class="login-visual__step login-visual__step--current">
                <span class="login-visual__dot"><?= renderIcon('circle-check') ?></span>
                <span class="login-visual__label">Approved</span>
            </div>
            <div class="login-visual__step">
                <span class="login-visual__dot"><?= renderIcon('rocket') ?></span>
                <span class="login-visual__label">Delivered</span>
            </div>
        </div>

        <div class="login-visual__copy">
            <h2>Every approval, recorded.</h2>
            <p>No more chasing feedback across chats and emails - stages, tasks and client sign-off, all in one place.</p>
        </div>
    </div>

    <div class="login-form-panel">
        <div class="auth-card">
            <h1 class="auth-title auth-title--tight">Welcome back</h1>
            <p class="auth-subtitle">Log in to continue to your workspace.</p>

            <form method="POST" action="<?= url('login') ?>" class="auth-form" novalidate>
                <?= csrfField() ?>

                <label class="auth-label" for="username">Username</label>
                <div class="auth-input-wrap">
                    <?= renderIcon('user') ?>
                    <input class="auth-input" type="text" id="username" name="username"
                           placeholder="yourusername" required autofocus autocomplete="username">
                </div>

                <label class="auth-label" for="password">Password</label>
                <div class="auth-input-wrap auth-input-wrap--password">
                    <?= renderIcon('lock') ?>
                    <input class="auth-input" type="password" id="password" name="password"
                           required autocomplete="current-password">
                    <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                        <?= renderIcon('eye') ?>
                        <?= renderIcon('eye-off') ?>
                    </button>
                </div>

                <button type="submit" class="auth-submit">
                    Log In <?= renderIcon('arrow-right') ?>
                </button>
            </form>

            <p class="auth-switch">
                Don't have an account? <a href="<?= url('register') ?>">Register</a>
            </p>
        </div>
    </div>

</div>

<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.parentElement.querySelector('input');
            var showing = btn.classList.toggle('is-visible');
            input.type = showing ? 'text' : 'password';
            btn.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
        });
    });
</script>