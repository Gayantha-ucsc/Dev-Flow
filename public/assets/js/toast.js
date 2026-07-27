(function () {
    const MAX_AGE_MS = { success: 4000, error: 6000, info: 4500 };

    let container = document.querySelector('[data-toast-container]');
    if (!container) {
        container = document.createElement('div');
        container.setAttribute('data-toast-container', '');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    function icon(type) {
        if (type === 'success') return window.APP_ICONS ? window.APP_ICONS.circleCheck : '';
        return window.APP_ICONS ? window.APP_ICONS.circleAlert : '';
    }

    function removeToast(el) {
        if (!el || el.dataset.removing) return;
        el.dataset.removing = 'true';
        el.classList.remove('is-visible');
        setTimeout(() => el.remove(), 200); // matches the CSS transition duration
    }

    function enforceOverflow() {
        const available = window.innerHeight - container.getBoundingClientRect().top - 20;
        while (container.scrollHeight > available && container.firstElementChild) {
            removeToast(container.firstElementChild);
        }
    }

    window.showToast = function (type, message, duration) {
        const el = document.createElement('div');
        el.className = `toast toast--${type}`;
        el.innerHTML = `
            <span class="toast__icon">${icon(type)}</span>
            <span class="toast__message">${message}</span>
            <button type="button" class="toast__close" aria-label="Dismiss">${window.APP_ICONS ? window.APP_ICONS.x : ''}</button>
        `;
        container.appendChild(el);

        requestAnimationFrame(() => el.classList.add('is-visible'));

        const timeoutMs = duration || MAX_AGE_MS[type] || MAX_AGE_MS.info;
        const timer = setTimeout(() => removeToast(el), timeoutMs);

        el.querySelector('.toast__close').addEventListener('click', function () {
            clearTimeout(timer);
            removeToast(el);
        });

        enforceOverflow();
    };
})();