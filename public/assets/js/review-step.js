(function () {
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : str;
        return div.innerHTML;
    }

    function formatDate(iso) {
        if (!iso) return '—';
        const d = new Date(iso + 'T00:00:00');
        return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function roleLabel(role) {
        const labels = { developer: 'Developer', designer: 'Designer', team_lead: 'Team Lead', manager: 'Manager' };
        return labels[role] || role;
    }

    function attachEditHandlers() {
        document.querySelectorAll('[data-review-edit]').forEach(btn => {
            btn.addEventListener('click', function () {
                const step = parseInt(this.dataset.reviewEdit, 10);
                if (window.wizardGoToStep) window.wizardGoToStep(step);
            });
        });
    }

    window.reviewStepPopulate = function () {
        // --- Project details ---
        const name = document.getElementById('project-name').value.trim();
        const description = document.getElementById('project-description').value.trim();
        const deadline = document.getElementById('project-deadline').value;

        document.querySelector('[data-review-details]').innerHTML = `
            <div class="review-row"><span class="review-row__label">Name</span><span class="review-row__value">${escapeHtml(name)}</span></div>
            <div class="review-row"><span class="review-row__label">Description</span><span class="review-row__value${description ? '' : ' is-muted'}">${description ? escapeHtml(description) : 'No description provided'}</span></div>
            <div class="review-row"><span class="review-row__label">Deadline</span><span class="review-row__value">${formatDate(deadline)}</span></div>
        `;

        // --- Workflow ---
        const summary = window.workflowGetSummary ? window.workflowGetSummary() : { name: '—', stages: [] };
        document.querySelector('[data-review-workflow]').innerHTML = `
            <div class="review-row"><span class="review-row__label">Template</span><span class="review-row__value">${escapeHtml(summary.name)}</span></div>
            <div class="review-row"><span class="review-row__label">Stages</span><span class="review-row__value">${summary.stages.map(escapeHtml).join(', ')}</span></div>
        `;

        // --- Team ---
        const members = window.teamGetMembers ? window.teamGetMembers() : [];
        const teamEl = document.querySelector('[data-review-team]');
        if (members.length === 0) {
            teamEl.innerHTML = `
                <div class="review-card">
                    <div class="review-card__header">
                        <span>TEAM MEMBERS</span>
                        <button type="button" class="review-card__edit" data-review-edit="3">${window.WIZARD_ICONS.pencil} Edit</button>
                    </div>
                    <div class="review-card__body">
                        <div class="review-row"><span class="review-row__value is-muted">No members added yet</span></div>
                    </div>
                </div>
            `;
        } else {
            teamEl.innerHTML = `
                <div class="review-card">
                    <div class="review-card__header">
                        <span>TEAM MEMBERS</span>
                        <button type="button" class="review-card__edit" data-review-edit="3">${window.WIZARD_ICONS.pencil} Edit</button>
                    </div>
                    <div class="review-card__body">
                        ${members.map(m => `
                            <div class="review-row">
                                <span class="review-row__label">${escapeHtml(m.identifier)}</span>
                                <span class="review-row__value">${roleLabel(m.role)}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        attachEditHandlers();
    };
})();