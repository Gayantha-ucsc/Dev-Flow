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
            <h1 class="auth-title auth-title--tight">Create your account</h1>
            <p class="auth-subtitle">Set up access to your DevFlow workspace.</p>

            <form method="POST" action="<?= url('register') ?>" class="auth-form" novalidate>
                <?= csrfField() ?>

                <label class="auth-label" for="name">Full Name</label>
                <div class="auth-input-wrap">
                    <?= renderIcon('user') ?>
                    <input class="auth-input" type="text" id="name" name="name" required autofocus
                           autocomplete="name" value="<?= htmlspecialchars($old['name']) ?>">
                </div>

                <label class="auth-label" for="username">Username</label>
                <div class="auth-input-wrap">
                    <?= renderIcon('user-check') ?>
                    <input class="auth-input" type="text" id="username" name="username" required
                           autocomplete="username" value="<?= htmlspecialchars($old['username']) ?>">
                </div>
                <p class="auth-hint">3-30 characters - letters, numbers and underscores only.</p>

                <label class="auth-label" for="email">Email</label>
                <div class="auth-input-wrap">
                    <?= renderIcon('mail') ?>
                    <input class="auth-input" type="email" id="email" name="email" required
                           autocomplete="email" value="<?= htmlspecialchars($old['email']) ?>">
                </div>

                <label class="auth-label" for="password">Password</label>
                <div class="auth-input-wrap auth-input-wrap--password">
                    <?= renderIcon('lock') ?>
                    <input class="auth-input" type="password" id="password" name="password"
                           required autocomplete="new-password">
                    <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                        <?= renderIcon('eye') ?>
                        <?= renderIcon('eye-off') ?>
                    </button>
                </div>
                <p class="auth-hint">At least 8 characters, with an uppercase letter, a lowercase letter, a number and a special character.</p>

                <label class="auth-label" for="confirm_password">Confirm Password</label>
                <div class="auth-input-wrap auth-input-wrap--password">
                    <?= renderIcon('lock') ?>
                    <input class="auth-input" type="password" id="confirm_password" name="confirm_password"
                           required autocomplete="new-password">
                    <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                        <?= renderIcon('eye') ?>
                        <?= renderIcon('eye-off') ?>
                    </button>
                </div>

                <button type="submit" class="auth-submit">
                    Create Account <?= renderIcon('arrow-right') ?>
                </button>
            </form>

            <p class="auth-switch">
                Already have an account? <a href="<?= url('login') ?>">Log in</a>
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