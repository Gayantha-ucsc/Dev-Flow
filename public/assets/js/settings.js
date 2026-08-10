document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('.settings-page');
    if (!page) return;

    // Password visibility toggles
    page.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = btn.previousElementSibling;
            const showing = btn.classList.toggle('is-visible');
            input.type = showing ? 'text' : 'password';
            btn.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
        });
    });

    //  Change photo
    const changePhotoBtn = page.querySelector('[data-change-photo]');
    const photoInput = page.querySelector('[data-photo-input]');
    const avatar = page.querySelector('[data-profile-avatar]');

    if (changePhotoBtn && photoInput) {
        changePhotoBtn.addEventListener('click', function () {
            photoInput.click();
        });

        photoInput.addEventListener('change', function () {
            const file = photoInput.files && photoInput.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function () {
                avatar.innerHTML = '<img src="' + reader.result + '" alt="">';
            };
            reader.readAsDataURL(file);
        });
    }

    // Save profile (name)
    const saveProfileBtn = page.querySelector('[data-save-profile]');
    if (saveProfileBtn) {
        saveProfileBtn.addEventListener('click', function () {
            const nameInput = document.getElementById('full-name');
            const name = nameInput.value.trim();
            if (!name) {
                nameInput.focus();
                return;
            }

            document.querySelectorAll('.user-name').forEach(function (el) { el.textContent = name; });

            if (window.showToast) window.showToast('success', 'Profile updated.');
        });
    }

    // Update password
    const updatePasswordBtn = page.querySelector('[data-update-password]');
    if (updatePasswordBtn) {
        updatePasswordBtn.addEventListener('click', function () {
            const current = document.getElementById('current-password');
            const next = document.getElementById('new-password');
            const confirm = document.getElementById('confirm-password');

            page.querySelectorAll('.field-error').forEach(function (el) {
                el.textContent = '';
                el.classList.remove('is-visible');
            });

            function showError(input, message) {
                const error = page.querySelector('[data-error-for="' + input.id + '"]');
                if (error) {
                    error.textContent = message;
                    error.classList.add('is-visible');
                }
            }

            let valid = true;
            if (!current.value) { showError(current, 'Enter your current password.'); valid = false; }
            if (!next.value) { showError(next, 'Enter a new password.'); valid = false; }
            if (next.value && confirm.value !== next.value) { showError(confirm, 'Passwords do not match.'); valid = false; }
            if (!valid) return;

            current.value = '';
            next.value = '';
            confirm.value = '';
            if (window.showToast) window.showToast('success', 'Password updated.');
        });
    }

    // Delete account confirmation
    const confirmDeleteBtn = document.querySelector('[data-confirm-delete]');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function () {
            const modal = confirmDeleteBtn.closest('[data-modal]');
            if (modal && window.closeModal) window.closeModal(modal);
            if (window.showToast) window.showToast('info', 'Account deletion isn\'t connected to a backend yet.');
        });
    }
});