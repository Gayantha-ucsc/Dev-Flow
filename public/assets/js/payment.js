document.addEventListener('DOMContentLoaded', function () {
    const page = document.getElementById('paymentPage');
    if (!page || page.dataset.view === 'client') return;

    const table = document.getElementById('paymentTable');
    const body = table.querySelector('tbody');
    const search = document.getElementById('paySearch');
    const filters = document.getElementById('payFilters');
    const empty = document.getElementById('payEmpty');
    const filterAttr = table.dataset.filterAttr;
    let activeFilter = 'all';

    const rows = () => Array.from(body.querySelectorAll('tr'));

    function applyFilters() {
        const query = (search.value || '').trim().toLowerCase();
        let total = 0;
        let shown = 0;

        rows().forEach(function (row) {
            total++;
            const matchesSearch = !query || row.dataset.search.indexOf(query) !== -1;
            const matchesFilter = activeFilter === 'all' || row.dataset[filterAttr] === activeFilter;
            const visible = matchesSearch && matchesFilter;
            row.style.display = visible ? '' : 'none';
            if (visible) shown++;
        });

        empty.hidden = shown !== 0;
        empty.textContent = total === 0 ? empty.dataset.emptyText : 'Nothing matches your filters.';
    }

    search.addEventListener('input', applyFilters);
    filters.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-filter]');
        if (!btn) return;
        filters.querySelectorAll('.filter-pill').forEach(function (p) { p.classList.remove('is-active'); });
        btn.classList.add('is-active');
        activeFilter = btn.dataset.filter;
        applyFilters();
    });

    if (page.dataset.view !== 'schedule') return;

    const canManage = page.dataset.canManage === '1';
    const ICONS = window.PAYMENT_ICONS || {};
    const STATUS = {
        pending:   { tone: 'neutral', label: 'Pending' },
        requested: { tone: 'warning', label: 'Requested' },
        paid:      { tone: 'success', label: 'Paid' }
    };
    const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const moneyFormat = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' });

    const money = (amount) => moneyFormat.format(amount);
    const plural = (n, word) => n + ' ' + word + (n === 1 ? '' : 's');

    function esc(value) {
        return String(value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function parseDate(iso) {
        const parts = iso.split('-').map(Number);
        return new Date(parts[0], parts[1] - 1, parts[2]);
    }

    function formatDate(iso) {
        const d = parseDate(iso);
        return MONTHS[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    }

    function dueInfo(m) {
        if (!m.dueDate || m.status === 'paid') return { label: '', tone: '' };

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const days = Math.round((parseDate(m.dueDate) - today) / 86400000);

        if (days < 0) return { label: 'Overdue by ' + plural(-days, 'day'), tone: 'danger' };
        if (days === 0) return { label: 'Due today', tone: 'warning' };
        if (days <= 7) return { label: 'Due in ' + plural(days, 'day'), tone: 'warning' };
        return { label: '', tone: '' };
    }

    function readRow(tr) {
        return {
            id: tr.dataset.id,
            description: tr.dataset.description,
            stage: tr.dataset.stage,
            amount: Number(tr.dataset.amount),
            dueDate: tr.dataset.due,
            status: tr.dataset.status
        };
    }

    function actionsHtml(m) {
        if (m.status !== 'pending') return '';
        return '<div class="payment-actions">' +
            '<button type="button" class="btn-sm btn-sm--primary" data-action="request">Request payment</button>' +
            '<button type="button" class="icon-btn-sm" data-action="edit" aria-label="Edit milestone" title="Edit">' + (ICONS.edit || '') + '</button>' +
            '<button type="button" class="icon-btn-sm icon-btn-sm--danger" data-action="delete" aria-label="Remove milestone" title="Remove">' + (ICONS.remove || '') + '</button>' +
            '</div>';
    }

    function paintRow(tr, m) {
        const meta = STATUS[m.status];
        const due = dueInfo(m);
        const stage = m.stage
            ? '<span class="badge badge--neutral">' + esc(m.stage) + '</span>'
            : '<span class="reports-table__muted">Not linked</span>';
        const dueSub = due.label
            ? '<span class="payment-due__sub payment-due__sub--' + due.tone + '">' + esc(due.label) + '</span>'
            : '';

        tr.dataset.id = m.id;
        tr.dataset.status = m.status;
        tr.dataset.amount = m.amount;
        tr.dataset.stage = m.stage;
        tr.dataset.due = m.dueDate;
        tr.dataset.description = m.description;
        tr.dataset.search = (m.description + ' ' + m.stage).toLowerCase();

        tr.innerHTML =
            '<td class="payment-desc">' + esc(m.description) + '</td>' +
            '<td>' + stage + '</td>' +
            '<td class="payment-amount">' + money(m.amount) + '</td>' +
            '<td><div class="payment-due"><span>' + (m.dueDate ? formatDate(m.dueDate) : 'Not set') + '</span>' + dueSub + '</div></td>' +
            '<td><span class="badge badge--' + meta.tone + '">' + meta.label + '</span></td>' +
            (canManage ? '<td>' + actionsHtml(m) + '</td>' : '');
    }

    function setText(key, text) {
        const el = page.querySelector('[data-sum="' + key + '"]');
        if (el) el.textContent = text;
    }

    function refresh() {
        const cents = { scheduled: 0, collected: 0, outstanding: 0, notRequested: 0 };
        const counts = { all: 0, pending: 0, requested: 0, paid: 0 };

        rows().forEach(function (tr) {
            const amount = Math.round(Number(tr.dataset.amount) * 100);
            const status = tr.dataset.status;
            cents.scheduled += amount;
            counts.all++;
            counts[status]++;

            if (status === 'paid') cents.collected += amount;
            else if (status === 'requested') cents.outstanding += amount;
            else cents.notRequested += amount;
        });

        const percent = cents.scheduled > 0 ? Math.round(cents.collected / cents.scheduled * 100) : 0;

        setText('scheduled', money(cents.scheduled / 100));
        setText('scheduledMeta', plural(counts.all, 'milestone'));
        setText('requested', money((cents.collected + cents.outstanding) / 100));
        setText('requestedMeta', money(cents.notRequested / 100) + ' not yet requested');
        setText('collected', money(cents.collected / 100));
        setText('collectedMeta', percent + '% of scheduled');
        setText('outstanding', money(cents.outstanding / 100));
        setText('outstandingMeta', counts.requested > 0 ? counts.requested + ' awaiting client payment' : 'Nothing outstanding');

        const bar = page.querySelector('[data-sum="collectedBar"]');
        if (bar) bar.style.width = percent + '%';

        Object.keys(counts).forEach(function (key) {
            const el = page.querySelector('[data-count="' + key + '"]');
            if (el) el.textContent = counts[key];
        });

        applyFilters();
    }

    if (!canManage) return;

    const milestoneModal = document.querySelector('[data-modal="payment-milestone"]');
    const requestModal = document.querySelector('[data-modal="payment-request"]');
    const deleteModal = document.querySelector('[data-modal="payment-delete"]');

    const form = document.getElementById('milestoneForm');
    const fieldDescription = document.getElementById('msDescription');
    const fieldStage = document.getElementById('msStage');
    const fieldAmount = document.getElementById('msAmount');
    const fieldDue = document.getElementById('msDue');
    const formError = document.getElementById('milestoneError');

    let activeRow = null;

    function openForm(row) {
        activeRow = row || null;
        form.reset();
        formError.hidden = true;
        document.getElementById('milestoneModalTitle').textContent = row ? 'Edit Milestone' : 'Add Milestone';
        document.getElementById('milestoneSubmit').textContent = row ? 'Save Changes' : 'Add Milestone';

        if (row) {
            const m = readRow(row);
            fieldDescription.value = m.description;
            fieldStage.value = m.stage;
            fieldAmount.value = m.amount;
            fieldDue.value = m.dueDate;
        }

        openModal(milestoneModal);
        setTimeout(function () { fieldDescription.focus(); }, 60);
    }

    function showError(message) {
        formError.textContent = message;
        formError.hidden = false;
    }

    document.getElementById('addMilestoneBtn').addEventListener('click', function () { openForm(null); });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const description = fieldDescription.value.trim();
        const amount = Math.round(parseFloat(fieldAmount.value) * 100) / 100;

        if (!description) return showError('Enter a description for this milestone.');
        if (!(amount > 0)) return showError('Enter an amount greater than zero.');
        if (amount > 9999999999.99) return showError('That amount is too large.');
        if (!fieldDue.value) return showError('Choose a due date.');

        const milestone = {
            id: activeRow ? activeRow.dataset.id : 'new-' + Date.now(),
            description: description,
            stage: fieldStage.value,
            amount: amount,
            dueDate: fieldDue.value,
            status: 'pending'
        };

        if (activeRow) {
            paintRow(activeRow, milestone);
        } else {
            const tr = document.createElement('tr');
            paintRow(tr, milestone);
            body.appendChild(tr);
        }

        closeModal(milestoneModal);
        refresh();
        window.showToast('success', activeRow ? 'Milestone updated.' : 'Milestone added.');
    });

    body.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        activeRow = btn.closest('tr');
        const m = readRow(activeRow);

        if (btn.dataset.action === 'edit') {
            openForm(activeRow);
        } else if (btn.dataset.action === 'request') {
            document.getElementById('requestAmount').textContent = money(m.amount);
            document.getElementById('requestDescription').textContent = m.description;
            openModal(requestModal);
        } else if (btn.dataset.action === 'delete') {
            document.getElementById('deleteDescription').textContent = m.description;
            openModal(deleteModal);
        }
    });

    document.getElementById('confirmRequest').addEventListener('click', function () {
        if (!activeRow) return;
        const m = readRow(activeRow);
        m.status = 'requested';
        paintRow(activeRow, m);
        closeModal(requestModal);
        refresh();
        window.showToast('success', 'Payment requested. The client has been notified.');
    });

    document.getElementById('confirmDelete').addEventListener('click', function () {
        if (!activeRow) return;
        activeRow.remove();
        closeModal(deleteModal);
        refresh();
        window.showToast('success', 'Milestone removed.');
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const page = document.getElementById('paymentPage');
    if (!page || page.dataset.view !== 'client') return;

    const ICONS = window.PAYMENT_ICONS || {};
    const moneyFormat = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' });
    const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    const money = (amount) => moneyFormat.format(amount);
    const plural = (n, word) => n + ' ' + word + (n === 1 ? '' : 's');
    const cards = () => Array.from(page.querySelectorAll('.client-pay-card'));
    const slot = (card, name) => card.querySelector('[data-slot="' + name + '"]');

    function setText(key, text) {
        const el = page.querySelector('[data-sum="' + key + '"]');
        if (el) el.textContent = text;
    }

    function refresh() {
        const cents = { scheduled: 0, paid: 0, due: 0, upcoming: 0 };
        let paidCount = 0;

        cards().forEach(function (card) {
            const amount = Math.round(Number(card.dataset.amount) * 100);
            cents.scheduled += amount;
            if (card.dataset.status === 'paid') {
                cents.paid += amount;
                paidCount++;
            } else if (card.dataset.status === 'requested') {
                cents.due += amount;
            } else {
                cents.upcoming += amount;
            }
        });

        const percent = cents.scheduled > 0 ? Math.round(cents.paid / cents.scheduled * 100) : 0;

        setText('collected', money(cents.paid / 100));
        setText('scheduled', money(cents.scheduled / 100));
        setText('percentMeta', percent + '% paid');
        setText('countMeta', paidCount + ' of ' + plural(cards().length, 'instalment') + ' paid');
        setText('tilePaid', money(cents.paid / 100));
        setText('tileDue', money(cents.due / 100));
        setText('tileUpcoming', money(cents.upcoming / 100));

        const bar = page.querySelector('[data-sum="collectedBar"]');
        if (bar) bar.style.width = percent + '%';
    }

    function markPaid(card) {
        const now = new Date();
        const stamp = String(now.getFullYear()).slice(2) + String(now.getMonth() + 1).padStart(2, '0') + String(now.getDate()).padStart(2, '0');
        const reference = 'GW-' + stamp + '-' + Math.floor(1000 + Math.random() * 9000);
        const paidOn = MONTHS[now.getMonth()] + ' ' + now.getDate() + ', ' + now.getFullYear();

        card.dataset.status = 'paid';
        card.classList.remove('client-pay-card--requested');
        card.classList.add('client-pay-card--paid');

        const badge = slot(card, 'badge');
        badge.className = 'badge badge--success';
        badge.textContent = 'Paid';

        slot(card, 'actions').innerHTML = '';
        slot(card, 'footer').innerHTML =
            '<div class="client-pay-card__footer client-pay-card__footer--paid">' + (ICONS.circleCheck || '') +
            '<span>Paid on ' + paidOn + ' (Ref: ' + reference + ')</span></div>';

        const due = slot(card, 'due');
        due.querySelectorAll('.payment-due__sub').forEach(function (el) { el.remove(); });
        const label = due.querySelector('span');
        if (label) label.textContent = label.textContent.replace(/^Due/, 'Was due');
    }

    const payModal = document.querySelector('[data-modal="payment-pay"]');
    const confirmBtn = document.getElementById('confirmPay');
    let activeCard = null;

    page.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action="pay"]');
        if (!btn) return;

        activeCard = btn.closest('.client-pay-card');
        document.getElementById('payAmount').textContent = money(Number(activeCard.dataset.amount));
        document.getElementById('payDescription').textContent = activeCard.dataset.description;
        openModal(payModal);
    });

    confirmBtn.addEventListener('click', function () {
        if (!activeCard) return;

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Redirecting...';

        setTimeout(function () {
            markPaid(activeCard);
            refresh();
            closeModal(payModal);
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Continue to payment';
            window.showToast('success', 'Payment received. Thank you.');
        }, 900);
    });
});