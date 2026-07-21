// Generic dropdown handling
document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-dropdown-trigger]');

    if (trigger) {
        const dropdown = trigger.closest('[data-dropdown]');
        const wasOpen = dropdown.classList.contains('is-open');

        // Close any other open dropdowns first
        document.querySelectorAll('[data-dropdown].is-open').forEach(function (d) {
            d.classList.remove('is-open');
        });

        if (!wasOpen) {
            dropdown.classList.add('is-open');
        }
        return;
    }

    // Clicked outside any dropdown entirely — close all
    const closer = e.target.closest('[data-dropdown]');
    if (!closer) {
        document.querySelectorAll('[data-dropdown].is-open').forEach(function (d) {
            d.classList.remove('is-open');
        });
    }
});

document.addEventListener('click', function (e) {
    const opener = e.target.closest('[data-modal-trigger]');
    if (opener) {
        e.preventDefault();
        const modal = document.querySelector(`[data-modal="${opener.dataset.modalTrigger}"]`);
        if (modal) {
            modal.classList.add('is-open');
            modal.dispatchEvent(new CustomEvent('modal:opened'));
        }
        return;
    }

    const closer = e.target.closest('[data-modal-close]');
    if (closer) {
        const modal = closer.closest('[data-modal]');
        if (modal) modal.classList.remove('is-open');
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[data-modal].is-open').forEach(m => m.classList.remove('is-open'));
    }
});