(function () {
    let members = []; // [{ email, role }]

    const emailInput = document.getElementById('team-email');
    const roleSelect = document.getElementById('team-role');
    const addBtn = document.querySelector('[data-add-member]');
    const listEl = document.querySelector('[data-team-members-list]');
    const sectionEl = document.querySelector('[data-team-members-section]');
    const errorEl = document.querySelector('[data-error-for="team-email"]');

    const AVATAR_COLORS = ['avatar-color-1', 'avatar-color-2', 'avatar-color-3', 'avatar-color-4'];

    function colorForEmail(email) {
        let sum = 0;
        for (let i = 0; i < email.length; i++) sum += email.charCodeAt(i);
        return AVATAR_COLORS[sum % AVATAR_COLORS.length];
    }

    function roleLabel(role) {
        const labels = { developer: 'Developer', designer: 'Designer', team_lead: 'Team Lead', manager: 'Manager' };
        return labels[role] || role;
    }

    function isValidEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function render() {
        sectionEl.hidden = members.length === 0;
        listEl.innerHTML = members.map((m, i) => `
            <div class="team-member-row">
                <span class="team-member-row__avatar ${colorForEmail(m.email)}">${m.email.charAt(0).toUpperCase()}</span>
                <span class="team-member-row__email">${m.email}</span>
                <span class="team-member-row__role">${roleLabel(m.role).toUpperCase()}</span>
                <button type="button" class="team-member-row__remove" data-remove-index="${i}">${window.WIZARD_ICONS.x}</button>
            </div>
        `).join('');

        listEl.querySelectorAll('[data-remove-index]').forEach(btn => {
            btn.addEventListener('click', function () {
                members.splice(parseInt(this.dataset.removeIndex, 10), 1);
                render();
            });
        });
    }

    addBtn.addEventListener('click', function () {
        const email = emailInput.value.trim();

        if (!email || !isValidEmail(email)) {
            errorEl.textContent = 'Enter a valid email address.';
            errorEl.classList.add('is-visible');
            return;
        }
        if (members.some(m => m.email.toLowerCase() === email.toLowerCase())) {
            errorEl.textContent = 'That email has already been added.';
            errorEl.classList.add('is-visible');
            return;
        }

        errorEl.classList.remove('is-visible');
        members.push({ email, role: roleSelect.value });
        emailInput.value = '';
        render();
    });

    // Enter key in the email field triggers Add
    emailInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); addBtn.click(); }
    });

    window.teamStepReset = function () {
        members = [];
        emailInput.value = '';
        errorEl.classList.remove('is-visible');
        render();
    };

    window.teamGetMembers = function () {
        return members.slice();
    };
})();