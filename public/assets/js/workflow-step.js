(function () {
    let selectedTemplateId = null; // number | 'scratch' | null
    let stages = [];               // [{ id, name }]
    let nextStageId = 1;
    let draggedIndex = null;

    // Template selection 

    document.querySelectorAll('[data-template-radio]').forEach(radio => {
        radio.addEventListener('change', function () {
            selectedTemplateId = parseInt(this.value, 10);
            updateCardSelection();
        });
    });

    const scratchBtn = document.querySelector('[data-template-scratch]');
    if (scratchBtn) {
        scratchBtn.addEventListener('click', function () {
            selectedTemplateId = 'scratch';
            document.querySelectorAll('[data-template-radio]').forEach(r => r.checked = false);
            updateCardSelection();
        });
    }

    function updateCardSelection() {
        document.querySelectorAll('.template-card').forEach(card => {
            const radio = card.querySelector('[data-template-radio]');
            const isScratchSelected = card.hasAttribute('data-template-scratch') && selectedTemplateId === 'scratch';
            const isRadioSelected = radio && radio.checked;
            card.classList.toggle('is-selected', isScratchSelected || isRadioSelected);
        });
        document.querySelector('[data-error-for="workflow-template"]').classList.remove('is-visible');
    }

    window.workflowHasSelection = function () {
        return selectedTemplateId !== null;
    };

    window.workflowShowSelectionError = function () {
        const el = document.querySelector('[data-error-for="workflow-template"]');
        el.textContent = 'Choose a template or start from scratch to continue.';
        el.classList.add('is-visible');
    };

    // Populates the stage list when moving from 'select' into 'arrange'.
    window.workflowPopulateStages = function () {
        let names;
        let subtext;

        if (selectedTemplateId === 'scratch') {
            names = ['New Stage'];
            subtext = 'Build your stages from scratch.';
        } else {
            const tpl = window.WORKFLOW_TEMPLATES.find(t => t.id === selectedTemplateId);
            names = tpl.stages;
            subtext = `Reorder, rename, add, or remove stages for ${tpl.name}.`;
        }

        stages = names.map(name => ({ id: nextStageId++, name }));
        document.querySelector('[data-arrange-subtext]').textContent = subtext;
        renderStages();
    };

    window.workflowHasStages = function () {
        return stages.length > 0;
    };

    window.workflowReset = function () {
        selectedTemplateId = null;
        stages = [];
        document.querySelectorAll('[data-template-radio]').forEach(r => r.checked = false);
        document.querySelectorAll('.template-card').forEach(c => c.classList.remove('is-selected'));
        document.querySelector('[data-error-for="workflow-template"]').classList.remove('is-visible');
    };

    window.workflowGetSummary = function () {
        if (selectedTemplateId === 'scratch') {
            return { name: 'Custom Workflow', icon: 'layout-template', stages: stages.map(s => s.name) };
        }
        const tpl = window.WORKFLOW_TEMPLATES.find(t => t.id === selectedTemplateId);
        return { name: tpl ? tpl.name : '—', icon: tpl ? tpl.icon : 'layout-template', stages: stages.map(s => s.name) };
    };

    // ---------- Stage list rendering + editing ----------
    const listEl = document.querySelector('[data-wizard-stage-list]');

    function renderStages() {
        listEl.innerHTML = stages.map((s, i) => `
            <div class="wizard-stage-row" draggable="true" data-stage-id="${s.id}">
                <span class="wizard-stage-row__grip">${window.WIZARD_ICONS.grip}</span>
                <span class="wizard-stage-row__badge">${i + 1}</span>
                <span class="wizard-stage-row__name" data-name-display>${escapeHtml(s.name)}</span>
                <input type="text" class="wizard-stage-row__name-input" data-name-input value="${escapeHtml(s.name)}" draggable="false" hidden>
                <span class="wizard-stage-row__actions">
                    <button type="button" class="wizard-stage-row__action" data-edit-stage draggable="false">${window.WIZARD_ICONS.pencil}</button>
                    <button type="button" class="wizard-stage-row__action wizard-stage-row__action--danger" data-delete-stage draggable="false">${window.WIZARD_ICONS.trash}</button>
                </span>
            </div>
        `).join('');
        attachRowHandlers();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function updateStageBadges() {
        listEl.querySelectorAll('.wizard-stage-row').forEach((row, i) => {
            const badge = row.querySelector('.wizard-stage-row__badge');
            if (badge) badge.textContent = i + 1;
        });
    }

    function attachRowHandlers() {
        listEl.querySelectorAll('.wizard-stage-row').forEach(row => {
            const id = parseInt(row.dataset.stageId, 10);

            // Edit
            row.querySelector('[data-edit-stage]').addEventListener('click', () => startEdit(row, id));

            // Delete
            row.querySelector('[data-delete-stage]').addEventListener('click', () => {
                if (stages.length <= 1) return;
                stages = stages.filter(s => s.id !== id);
                renderStages();
            });

            // Drag and drop reordering
            row.addEventListener('dragstart', function () {
                draggedIndex = stages.findIndex(s => s.id === id);
                row.classList.add('is-dragging');
            });
            row.addEventListener('dragend', function () {
                row.classList.remove('is-dragging');
            });
            row.addEventListener('dragover', function (e) {
                e.preventDefault();
                const overIndex = stages.findIndex(s => s.id === id);
                if (draggedIndex === null || overIndex === draggedIndex) return;

                const moved = stages.splice(draggedIndex, 1)[0];
                stages.splice(overIndex, 0, moved);

                const draggedRow = listEl.querySelector(`[data-stage-id="${moved.id}"]`);
                const rows = Array.from(listEl.children);
                const targetRow = rows[overIndex];
                if (draggedRow && targetRow && draggedRow !== targetRow) {
                    if (overIndex > draggedIndex) {
                        targetRow.after(draggedRow);
                    } else {
                        targetRow.before(draggedRow);
                    }
                }
                updateStageBadges();
                draggedIndex = overIndex;
            });
        });
    }

    function startEdit(row, id) {
        const display = row.querySelector('[data-name-display]');
        const input = row.querySelector('[data-name-input]');
        display.hidden = true;
        input.hidden = false;
        input.focus();
        input.select();

        function save() {
            const value = input.value.trim();
            const stage = stages.find(s => s.id === id);
            if (value) stage.name = value;
            renderStages();
        }

        input.addEventListener('blur', save, { once: true });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') input.blur();
        });
    }

    const addBtn = document.querySelector('[data-add-stage]');
    if (addBtn) {
        addBtn.addEventListener('click', function () {
            const newStage = { id: nextStageId++, name: 'New Stage' };
            stages.push(newStage);
            renderStages();
            const row = listEl.querySelector(`[data-stage-id="${newStage.id}"]`);
            if (row) startEdit(row, newStage.id); // auto-focus the new row for immediate rename
        });
    }
})();