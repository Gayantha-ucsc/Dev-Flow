(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.querySelector('[data-modal="project-wizard"]');
        if (!modal) return;

        const panels = Array.from(modal.querySelectorAll('.wizard-panel'));
        const steps = Array.from(modal.querySelectorAll('.wizard-step'));
        const continueBtn = modal.querySelector('[data-wizard-continue]');
        const backBtn = modal.querySelector('[data-wizard-back]');
        const phaseSelect = modal.querySelector('[data-phase="select"]');
        const phaseArrange = modal.querySelector('[data-phase="arrange"]');

        let currentStep = 1;
        let currentPhase = phaseSelect ? 'select' : 'arrange';

        function setPhase(phase) {
            currentPhase = phase;
            if (phaseSelect) phaseSelect.classList.toggle('is-active', phase === 'select');
            if (phaseArrange) phaseArrange.classList.toggle('is-active', phase === 'arrange');
        }

        function showStep(step) {
            currentStep = step;
            panels.forEach(panel => panel.classList.toggle('is-active', parseInt(panel.dataset.panelIndex, 10) === step));
            steps.forEach(stepEl => stepEl.classList.toggle('is-active', parseInt(stepEl.dataset.stepIndex, 10) === step));
            updateFooter();
            if (step === 4 && window.reviewStepPopulate) {
                window.reviewStepPopulate();
            }
        }

        function updateFooter() {
            const continueLabel = continueBtn.querySelector('[data-continue-label]');
            continueLabel.textContent = currentStep === 4 ? 'Create Project' : 'Continue';
            backBtn.textContent = currentStep === 1 ? 'Cancel' : 'Back';
        }

        function clearErrors() {
            modal.querySelectorAll('.field-error').forEach(el => {
                el.textContent = '';
                el.classList.remove('is-visible');
            });
        }

        function showError(selector, message) {
            const el = modal.querySelector(selector);
            if (!el) return;
            el.textContent = message;
            el.classList.add('is-visible');
        }

        function validateDetails() {
            const name = modal.querySelector('#project-name');
            const deadline = modal.querySelector('#project-deadline');
            let valid = true;

            if (!name || !name.value.trim()) {
                showError('[data-error-for="project-name"]', 'Enter a project name.');
                valid = false;
            }
            if (!deadline || !deadline.value) {
                showError('[data-error-for="project-deadline"]', 'Pick a deadline to continue.');
                valid = false;
            }

            return valid;
        }

        function validateWorkflowSelect() {
            if (window.workflowHasSelection && window.workflowHasSelection()) {
                return true;
            }
            if (window.workflowShowSelectionError) {
                window.workflowShowSelectionError();
            }
            return false;
        }

        function validateWorkflowArrange() {
            if (window.workflowHasStages && window.workflowHasStages()) {
                return true;
            }
            showError('[data-error-for="workflow-template"]', 'Add at least one stage to continue.');
            return false;
        }

        continueBtn.addEventListener('click', function () {
            clearErrors();

            if (currentStep === 1) {
                if (!validateDetails()) return;
                showStep(2);
                return;
            }

            if (currentStep === 2) {
                if (currentPhase === 'select') {
                    if (!validateWorkflowSelect()) return;
                    if (window.workflowPopulateStages) window.workflowPopulateStages();
                    setPhase('arrange');
                    return;
                }
                if (currentPhase === 'arrange') {
                    if (!validateWorkflowArrange()) return;
                    showStep(3);
                    return;
                }
            }

            if (currentStep === 3) {
                showStep(4);
                return;
            }

            if (currentStep === 4) {
                if (window.showToast) window.showToast('success', 'Project created successfully!');
                closeModal(modal);
                resetWizard();
            }
        });

        backBtn.addEventListener('click', function () {
            if (currentStep === 1) {
                closeModal(modal);
                return;
            }

            if (currentStep === 2 && currentPhase === 'arrange') {
                setPhase('select');
                return;
            }

            showStep(currentStep - 1);
        });

        function resetWizard() {
            showStep(1);
            setPhase('select');
            if (window.teamStepReset) window.teamStepReset();
            if (window.workflowReset) window.workflowReset();
            clearErrors();

            const nameInput = modal.querySelector('#project-name');
            const descriptionInput = modal.querySelector('#project-description');
            const deadlineInput = modal.querySelector('#project-deadline');
            const dateDisplay = modal.querySelector('[data-date-display]');

            if (nameInput) nameInput.value = '';
            if (descriptionInput) descriptionInput.value = '';
            if (deadlineInput) deadlineInput.value = '';
            if (dateDisplay) {
                dateDisplay.textContent = 'mm/dd/yyyy';
                dateDisplay.classList.remove('has-value');
            }
        }

        function closeModal(modalEl) {
            modalEl.classList.remove('is-open');
            setTimeout(() => modalEl.setAttribute('hidden', ''), 200);
        }

        modal.addEventListener('modal:opened', function () {
            showStep(1);
            setPhase('select');
        });
    });
})();