(function () {
    let selectedTemplateId = null; // number | 'scratch' | null
    let stages = [];               // [{ id, name }]
    let nextStageId = 1;
    let draggedIndex = null;

    // ---------- Template selection ----------

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

    // ---------- Stage list rendering + editing ----------
    const listEl = document.querySelector('[data-stage-list]');

    function renderStages() {
        listEl.innerHTML = stages.map((s, i) => `
            <div class="stage-row" draggable="true" data-stage-id="${s.id}">
                <span class="stage-row__grip">${window.STAGE_ICONS.grip}</span>
                <span class="stage-row__badge">${i + 1}</span>
                <span class="stage-row__name" data-name-display>${escapeHtml(s.name)}</span>
                <input type="text" class="stage-row__name-input" data-name-input value="${escapeHtml(s.name)}" draggable="false" hidden>
                <span class="stage-row__actions">
                    <button type="button" class="stage-row__action" data-edit-stage draggable="false">${window.STAGE_ICONS.pencil}</button>
                    <button type="button" class="stage-row__action stage-row__action--danger" data-delete-stage draggable="false">${window.STAGE_ICONS.trash}</button>
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

    function attachRowHandlers() {
        listEl.querySelectorAll('.stage-row').forEach(row => {
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
                draggedIndex = overIndex;
                renderStages();
                // re-mark dragging row after re-render, since renderStages rebuilds the DOM
                const newRow = listEl.querySelector(`[data-stage-id="${id}"]`);
                if (newRow) newRow.classList.add('is-dragging');
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