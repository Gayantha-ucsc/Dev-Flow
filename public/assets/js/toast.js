document.addEventListener('DOMContentLoaded', function () {
    const MAX_AGE_MS = {
        success : 4000,
        error   : 6000,
        info    : 4500
    };

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
        setTimeout(() => el.remove(), 200);
    }

    function enforceOverflow() {
        const available = window.innerHeight - container.getBoundingClientRect().top - 20;
        const toasts = Array.from(container.children).filter(el => !el.dataset.removing);
        const gap = parseFloat(getComputedStyle(container).gap) || 0;

        let totalHeight = toasts.reduce((sum, el) => sum + el.offsetHeight, 0)
            + gap * Math.max(0, toasts.length - 1);

        let i = 0;
        while (totalHeight > available && i < toasts.length) {
            totalHeight -= toasts[i].offsetHeight + gap;
            removeToast(toasts[i]);
            i++;
        }
    }

    window.showToast = function (type, message, duration) {
        const el = document.createElement('div');
        el.className = `toast toast--${type}`;
        el.innerHTML = `
            <span class="toast__icon">${icon(type)}</span>
            <span class="toast__message"></span>
            <button type="button" class="toast__close" aria-label="Dismiss">
                ${window.APP_ICONS ? window.APP_ICONS.x : ''}
            </button>
        `;
        el.querySelector('.toast__message').textContent = message;
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
});