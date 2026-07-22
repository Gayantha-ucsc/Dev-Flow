document.querySelectorAll('[data-modal="project-wizard"]').forEach(function (modal) {
    const steps = modal.querySelectorAll('.wizard-step');
    const panels = modal.querySelectorAll('.wizard-panel');
    const continueBtn = modal.querySelector('[data-wizard-continue]');
    const backBtn = modal.querySelector('[data-wizard-back]');
    const skipBtn = modal.querySelector('[data-wizard-skip]');
    let currentStep = 1;
    let maxStepReached = 1;

    function activeWorkflowPhase() {
        const el = modal.querySelector('.workflow-phase.is-active');
        return el ? el.dataset.phase : null;
    }

    function setWorkflowPhase(phase) {
        modal.querySelectorAll('.workflow-phase').forEach(p => {
            p.classList.toggle('is-active', p.dataset.phase === phase);
        });
    }

    function updateFooter() {
        const onFirstScreen = currentStep === 1;
        if (onFirstScreen) {
            backBtn.className = 'btn-cancel';
            backBtn.innerHTML = 'Cancel';
            backBtn.setAttribute('data-modal-close', '');
        } else {
            backBtn.className = 'btn-back';
            backBtn.innerHTML = `${window.WIZARD_ICONS.arrowLeft} <span>Back</span>`;
            backBtn.removeAttribute('data-modal-close');
        }
        skipBtn.hidden = currentStep !== 3;

        const continueLabel = modal.querySelector('[data-continue-label]');
        continueLabel.textContent = currentStep === 4 ? 'Create Project' : 'Continue';
    }

    function goToStep(index) {
        steps.forEach(s => {
            const idx = parseInt(s.dataset.stepIndex, 10);
            s.classList.toggle('is-active', idx === index);
            s.classList.toggle('is-complete', idx < maxStepReached);
            s.classList.toggle('is-clickable', idx <= maxStepReached);
        });
        panels.forEach(p => {
            p.classList.toggle('is-active', parseInt(p.dataset.panelIndex, 10) === index);
        });
        currentStep = index;
        updateFooter();
    }
    window.wizardGoToStep = goToStep;

    function validateStep1() {
        let valid = true;
        const name = modal.querySelector('#project-name');
        const nameError = modal.querySelector('[data-error-for="project-name"]');
        if (!name.value.trim()) {
            nameError.textContent = 'Project name is required.';
            nameError.classList.add('is-visible');
            valid = false;
        } else {
            nameError.classList.remove('is-visible');
        }

        const deadline = modal.querySelector('#project-deadline');
        const deadlineError = modal.querySelector('[data-error-for="project-deadline"]');
        if (!deadline.value) {
            deadlineError.textContent = 'Deadline is required.';
            deadlineError.classList.add('is-visible');
            valid = false;
        } else {
            const picked = new Date(deadline.value + 'T00:00:00');
            const today = new Date(); today.setHours(0, 0, 0, 0);
            if (picked < today) {
                deadlineError.textContent = 'Deadline cannot be in the past.';
                deadlineError.classList.add('is-visible');
                valid = false;
            } else {
                deadlineError.classList.remove('is-visible');
            }
        }
        return valid;
    }

    continueBtn.addEventListener('click', function () {
        if (currentStep === 1) {
            if (!validateStep1()) return;
            maxStepReached = Math.max(maxStepReached, 2);
            goToStep(2);
            return;
        }

        if (currentStep === 2) {
            const phase = activeWorkflowPhase();
            if (phase === 'select') {
                if (!window.workflowHasSelection()) { window.workflowShowSelectionError(); return; }
                window.workflowPopulateStages();
                setWorkflowPhase('arrange');
                return;
            }
            if (phase === 'arrange') {
                if (!window.workflowHasStages()) return;
                maxStepReached = Math.max(maxStepReached, 3);
                goToStep(3);
                return;
            }
        }

        if (currentStep === 3) {
            maxStepReached = Math.max(maxStepReached, 4);
            if (window.reviewStepPopulate) window.reviewStepPopulate();
            goToStep(4);
            return;
        }

        if (currentStep === 4) {
            console.log('Create Project — mock submit', {
                name: document.getElementById('project-name').value,
                description: document.getElementById('project-description').value,
                deadline: document.getElementById('project-deadline').value,
                workflow: window.workflowGetSummary(),
                team: window.teamGetMembers(),
            });
            modal.classList.remove('is-open');
            setTimeout(() => modal.setAttribute('hidden', ''), 200);
        }
    });

    backBtn.addEventListener('click', function () {
        if (currentStep === 1) return; // data-modal-close on this button handles closing

        if (currentStep === 2 && activeWorkflowPhase() === 'arrange') {
            setWorkflowPhase('select');
            return;
        }
        goToStep(currentStep - 1);
    });

    skipBtn.addEventListener('click', function () {
        maxStepReached = Math.max(maxStepReached, 4);
        if (window.reviewStepPopulate) window.reviewStepPopulate();
        goToStep(4);
    });

    steps.forEach(stepEl => {
        stepEl.addEventListener('click', function () {
            const idx = parseInt(stepEl.dataset.stepIndex, 10);
            if (idx <= maxStepReached) goToStep(idx);
        });
    });

    modal.addEventListener('modal:opened', function () {
        currentStep = 1;
        maxStepReached = 1;
        setWorkflowPhase('select');
        if (window.workflowReset) window.workflowReset();
        if (window.teamStepReset) window.teamStepReset();
        goToStep(1);
    });

    updateFooter();
});