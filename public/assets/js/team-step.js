(function () {
    let members = []; // [{ identifier, role }]

    const identifierInput = document.getElementById('team-identifier');
    const roleSelect = document.getElementById('team-role');
    const addBtn = document.querySelector('[data-add-member]');
    const listEl = document.querySelector('[data-team-members-list]');
    const sectionEl = document.querySelector('[data-team-members-section]');
    const errorEl = document.querySelector('[data-error-for="team-identifier"]');

    const AVATAR_COLORS = ['avatar-color-1', 'avatar-color-2', 'avatar-color-3', 'avatar-color-4'];

    function colorForIdentifier(identifier) {
        let sum = 0;
        for (let i = 0; i < identifier.length; i++) sum += identifier.charCodeAt(i);
        return AVATAR_COLORS[sum % AVATAR_COLORS.length];
    }

    function roleLabel(role) {
        const labels = { developer: 'Developer', designer: 'Designer', team_lead: 'Team Lead', manager: 'Manager' };
        return labels[role] || role;
    }

    function isValidEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function isValidUsername(value) {
        return /^[a-zA-Z0-9._-]{3,50}$/.test(value);
    }

    function isValidIdentifier(value) {
        return isValidEmail(value) || isValidUsername(value);
    }

    function render() {
        sectionEl.hidden = members.length === 0;
        listEl.innerHTML = members.map((m, i) => `
            <div class="team-member-row">
                <span class="team-member-row__avatar ${colorForIdentifier(m.identifier)}">${m.identifier.charAt(0).toUpperCase()}</span>
                <span class="team-member-row__identifier">${m.identifier}</span>
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
        const identifier = identifierInput.value.trim();

        if (!identifier || !isValidIdentifier(identifier)) {
            errorEl.textContent = 'Enter a valid username or email address.';
            errorEl.classList.add('is-visible');
            return;
        }
        if (members.some(m => m.identifier.toLowerCase() === identifier.toLowerCase())) {
            errorEl.textContent = 'That username or email has already been added.';
            errorEl.classList.add('is-visible');
            return;
        }

        errorEl.classList.remove('is-visible');
        members.push({ identifier, role: roleSelect.value });
        identifierInput.value = '';
        render();
    });

    identifierInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); addBtn.click(); }
    });

    window.teamStepReset = function () {
        members = [];
        identifierInput.value = '';
        errorEl.classList.remove('is-visible');
        render();
    };

    window.teamGetMembers = function () {
        return members.slice();
    };
})();