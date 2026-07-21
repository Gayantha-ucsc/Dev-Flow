<div class="modal-overlay" data-modal="project-wizard">
    <div class="modal-backdrop" data-modal-close></div>

    <div class="modal-box wizard-modal">
        <div class="wizard-header">
            <h2>Create New Project</h2>
            <div class="wizard-steps">
                <div class="wizard-step is-active" data-step-index="1">
                    <span class="wizard-step__dot">1</span>
                    <span class="wizard-step__label">DETAILS</span>
                </div>
                <span class="wizard-step__line"></span>
                <div class="wizard-step" data-step-index="2">
                    <span class="wizard-step__dot">2</span>
                    <span class="wizard-step__label">WORKFLOW</span>
                </div>
                <span class="wizard-step__line"></span>
                <div class="wizard-step" data-step-index="3">
                    <span class="wizard-step__dot">3</span>
                    <span class="wizard-step__label">TEAM</span>
                </div>
                <span class="wizard-step__line"></span>
                <div class="wizard-step" data-step-index="4">
                    <span class="wizard-step__dot">4</span>
                    <span class="wizard-step__label">REVIEW</span>
                </div>
            </div>
        </div>

        <div class="wizard-body">

            <!-- Step 1: Details -->
            <div class="wizard-panel is-active" data-panel-index="1">
                <h3>Project Details</h3>
                <p class="wizard-subtext">Provide the basic information for your new project.</p>

                <div class="form-group">
                    <label for="project-name">Project Name <span class="required">*</span></label>
                    <input type="text" id="project-name" placeholder="e.g. Web Development Project">
                    <span class="field-error" data-error-for="project-name"></span>
                </div>

                <div class="form-group">
                    <label for="project-description">Description</label>
                    <textarea id="project-description" rows="4" placeholder="Briefly describe the project's goals and scope..."></textarea>
                </div>

                <div class="form-group">
                    <label>Deadline <span class="required">*</span></label>
                    <div class="dropdown date-field" data-dropdown data-datepicker>
                        <button type="button" class="date-field__input" data-dropdown-trigger data-date-trigger>
                            <?= renderIcon('calendar') ?>
                            <span data-date-display>mm/dd/yyyy</span>
                        </button>
                        <input type="hidden" id="project-deadline" data-date-value>
                        <div class="dropdown__menu dropdown__menu--calendar" data-dropdown-menu data-date-calendar></div>
                    </div>
                    <span class="field-error" data-error-for="project-deadline"></span>
                </div>
            </div>

            <!-- Step 2: Workflow — placeholder, built next -->
            <div class="wizard-panel" data-panel-index="2">
                <p class="wizard-placeholder">Workflow template selection — coming soon.</p>
            </div>

            <!-- Step 3: Team — placeholder -->
            <div class="wizard-panel" data-panel-index="3">
                <p class="wizard-placeholder">Team setup — coming soon.</p>
            </div>

            <!-- Step 4: Review — placeholder -->
            <div class="wizard-panel" data-panel-index="4">
                <p class="wizard-placeholder">Review and confirm — coming soon.</p>
            </div>

        </div>

        <div class="wizard-footer">
            <button type="button" class="btn-cancel" data-modal-close>Cancel</button>
            <button type="button" class="btn-continue" data-wizard-continue>Continue</button>
        </div>
    </div>
</div>