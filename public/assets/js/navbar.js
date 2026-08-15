document.addEventListener('DOMContentLoaded', function () {
    const switcherDropdown = document.querySelector('[data-project-switcher]');
    if (!switcherDropdown) return;

    const searchInput = switcherDropdown.querySelector('[data-project-search]');
    const items = Array.prototype.slice.call(switcherDropdown.querySelectorAll('.dropdown__item'));
    const emptyState = switcherDropdown.querySelector('[data-project-search-empty]');
    if (!searchInput) return;

    function applyFilter() {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        items.forEach(function (item) {
            const matches = !query || item.dataset.name.indexOf(query) !== -1;
            item.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        if (emptyState) emptyState.hidden = visibleCount !== 0;
    }

    searchInput.addEventListener('input', applyFilter);

    // global.js owns opening/closing this dropdown generically (it has
    // no idea a search box lives inside this particular one) - watching
    // the class here, instead of adding project-switcher-specific logic
    // to global.js, keeps that mechanism reusable for every other
    // dropdown in the app.
    new MutationObserver(function () {
        if (switcherDropdown.classList.contains('is-open')) {
            searchInput.focus();
        } else {
            searchInput.value = '';
            applyFilter();
        }
    }).observe(switcherDropdown, { attributes: true, attributeFilter: ['class'] });
});