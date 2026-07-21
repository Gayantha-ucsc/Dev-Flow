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

const MODAL_TRANSITION_MS = 200; // matches modal.css's transition duration

function openModal(modal) {
    modal.removeAttribute('hidden');
    requestAnimationFrame(() => modal.classList.add('is-open'));
    modal.dispatchEvent(new CustomEvent('modal:opened'));
}

function closeModal(modal) {
    modal.classList.remove('is-open');
    setTimeout(() => modal.setAttribute('hidden', ''), MODAL_TRANSITION_MS);
}

document.addEventListener('click', function (e) {
    const opener = e.target.closest('[data-modal-trigger]');
    if (opener) {
        e.preventDefault();
        const modal = document.querySelector(`[data-modal="${opener.dataset.modalTrigger}"]`);
        if (modal) openModal(modal);
        return;
    }

    const closer = e.target.closest('[data-modal-close]');
    if (closer) {
        const modal = closer.closest('[data-modal]');
        if (modal) closeModal(modal);
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[data-modal].is-open').forEach(closeModal);
    }
});