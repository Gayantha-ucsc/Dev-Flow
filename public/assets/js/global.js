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
    if (!e.target.closest('[data-dropdown]')) {
        document.querySelectorAll('[data-dropdown].is-open').forEach(function (d) {
            d.classList.remove('is-open');
        });
    }
});