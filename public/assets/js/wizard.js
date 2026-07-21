document.querySelectorAll('[data-modal="project-wizard"]').forEach(function (modal) {
    const steps = modal.querySelectorAll('.wizard-step');
    const panels = modal.querySelectorAll('.wizard-panel');
    const continueBtn = modal.querySelector('[data-wizard-continue]');
    let currentStep = 1;
    let maxStepReached = 1;

    function goToStep(index) {
        steps.forEach(s => {
            const idx = parseInt(s.dataset.stepIndex, 10);
            s.classList.toggle('is-active', idx === index);
            s.classList.toggle('is-complete', idx < maxStepReached || (idx < index && idx <= maxStepReached));
            s.classList.toggle('is-clickable', idx <= maxStepReached);
        });
        panels.forEach(p => {
            p.classList.toggle('is-active', parseInt(p.dataset.panelIndex, 10) === index);
        });
        currentStep = index;
    }

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
        if (currentStep === 1 && !validateStep1()) return;
        if (currentStep < 4) {
            maxStepReached = Math.max(maxStepReached, currentStep + 1);
            goToStep(currentStep + 1);
        }
    });

    // click a step number to jump back
    steps.forEach(function (stepEl) {
        stepEl.addEventListener('click', function () {
            const idx = parseInt(stepEl.dataset.stepIndex, 10);
            if (idx <= maxStepReached) {
                goToStep(idx);
            }
        });
    });

    modal.addEventListener('modal:opened', function () {
        currentStep = 1;
        maxStepReached = 1;
        goToStep(1);
    });
});