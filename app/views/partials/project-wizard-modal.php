<?php $workflowTemplates = require __DIR__ . '/../../../config/mock/workflow-templates.php'; ?>

<div class="modal-overlay" data-modal="project-wizard" hidden>
    <div class="modal-backdrop" data-modal-close></div>

    <div class="modal-box wizard-modal">
        <div class="wizard-header">
            <h2>Create New Project</h2>
            <div class="wizard-steps">
                <div class="wizard-step is-active" data-step-index="1">
                    <span class="wizard-step__dot">
                        <span class="wizard-step__num">1</span>
                        <span class="wizard-step__check"><?= renderIcon('check') ?></span>
                    </span>
                    <span class="wizard-step__label">DETAILS</span>
                </div>
                <span class="wizard-step__line"></span>
                <div class="wizard-step" data-step-index="2">
                    <span class="wizard-step__dot">
                        <span class="wizard-step__num">2</span>
                        <span class="wizard-step__check"><?= renderIcon('check') ?></span>
                    </span>
                    <span class="wizard-step__label">WORKFLOW</span>
                </div>
                <span class="wizard-step__line"></span>
                <div class="wizard-step" data-step-index="3">
                    <span class="wizard-step__dot">
                        <span class="wizard-step__num">3</span>
                        <span class="wizard-step__check"><?= renderIcon('check') ?></span>
                    </span>
                    <span class="wizard-step__label">TEAM</span>
                </div>
                <span class="wizard-step__line"></span>
                <div class="wizard-step" data-step-index="4">
                    <span class="wizard-step__dot">
                        <span class="wizard-step__num">4</span>
                        <span class="wizard-step__check"><?= renderIcon('check') ?></span>
                    </span>
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

            <!-- Step 2: Workflow -->
            <div class="wizard-panel" data-panel-index="2">

                <!-- Sub-phase A: pick a template -->
                <div class="workflow-phase is-active" data-phase="select">
                    <h3>Choose a workflow template</h3>
                    <p class="wizard-subtext">Start from a preset template or build your own stages from scratch.</p>
                    <span class="field-error" data-error-for="workflow-template"></span>

                    <div class="template-list">
                        <?php foreach ($workflowTemplates as $tpl): ?>
                            <label class="template-card">
                                <input type="radio" name="workflow-template" value="<?= (int)$tpl['id'] ?>" class="template-card__radio-input" data-template-radio>
                                <span class="template-card__icon template-card__icon--<?= htmlspecialchars($tpl['icon_bg']) ?>"><?= renderIcon($tpl['icon']) ?></span>
                                <span class="template-card__body">
                                    <span class="template-card__name"><?= htmlspecialchars($tpl['name']) ?></span>
                                    <span class="template-card__desc"><?= htmlspecialchars($tpl['description']) ?></span>
                                    <span class="template-card__count"><?= count($tpl['stages']) ?> stages</span>
                                </span>
                                <span class="template-card__radio-dot"></span>
                            </label>
                        <?php endforeach; ?>

                        <button type="button" class="template-card template-card--scratch" data-template-scratch>
                            <span class="template-card__scratch-icon"><?= renderIcon('plus') ?></span>
                            <span class="template-card__name">Start from Scratch</span>
                            <span class="template-card__desc">Build your perfect workflow board stage by stage.</span>
                        </button>
                    </div>
                </div>

                <!-- Sub-phase B: arrange stages -->
                <div class="workflow-phase" data-phase="arrange">
                    <h3>Arrange your stages</h3>
                    <p class="wizard-subtext" data-arrange-subtext>Reorder, rename, add, or remove stages.</p>

                    <div class="wizard-stage-list" data-wizard-stage-list></div>

                    <button type="button" class="stage-add-btn" data-add-stage>
                        <span><?= renderIcon('plus') ?></span>
                        <span>Add Stage</span>
                    </button>
                </div>
            </div>

            <!-- Icon + template data handed to JS once -->
            <script>
                window.WORKFLOW_TEMPLATES = <?= json_encode($workflowTemplates) ?>;
                window.WIZARD_ICONS = {
                    grip:           <?= iconJson('grip-vertical') ?>,
                    pencil:         <?= iconJson('pencil') ?>,
                    trash:          <?= iconJson('trash-2') ?>,
                    arrowLeft:      <?= iconJson('arrow-left') ?>,
                    layoutTemplate: <?= iconJson('layout-template') ?>,
                    info:           <?= iconJson('info') ?>,
                    user:           <?= iconJson('user') ?>
                };
            </script>

            <!-- Step 3: Team -->
            <div class="wizard-panel" data-panel-index="3">
                <h3>Add Team Members</h3>
                <p class="wizard-subtext">Invite people to collaborate on this project. You can always add more later.</p>

                <div class="team-add-row">
                    <input type="email" id="team-email" class="team-add-row__input" placeholder="Enter email address">
                    <select id="team-role" class="team-add-row__select">
                        <option value="developer">Developer</option>
                        <option value="designer">Designer</option>
                        <option value="team_lead">Team Lead</option>
                    </select>
                    <button type="button" class="team-add-row__btn" data-add-member>
                        <?= renderIcon('plus') ?> <span>Add</span>
                    </button>
                </div>
                <span class="field-error" data-error-for="team-email"></span>

                <div class="team-members" data-team-members-section hidden>
                    <div class="team-members__label">TEAM MEMBERS</div>
                    <div class="team-members__list" data-team-members-list></div>
                </div>
            </div>

            <!-- Step 4: Review -->
            <div class="wizard-panel" data-panel-index="4">
                <h3>Review your project</h3>
                <p class="wizard-subtext">Check everything looks right before creating.</p>

                <div class="review-card">
                    <div class="review-card__header">
                        <span>PROJECT DETAILS</span>
                        <button type="button" class="review-card__edit" data-review-edit="1"><?= renderIcon('pencil') ?> Edit</button>
                    </div>
                    <div class="review-card__body" data-review-details></div>
                </div>

                <div class="review-card">
                    <div class="review-card__header">
                        <span>WORKFLOW</span>
                        <button type="button" class="review-card__edit" data-review-edit="2"><?= renderIcon('pencil') ?> Edit</button>
                    </div>
                    <div class="review-card__body" data-review-workflow></div>
                </div>

                <div data-review-team></div>
            </div>

        </div>

        <!-- footer -->
        <div class="wizard-footer">
            <button type="button" class="btn-cancel" data-wizard-back>Cancel</button>
            <div class="wizard-footer__right">
                <button type="button" class="btn-skip" data-wizard-skip hidden>Skip</button>
                <button type="button" class="btn-continue" data-wizard-continue>
                    <span data-continue-label>Continue</span> <?= renderIcon('arrow-right') ?>
                </button>
            </div>
        </div>
    </div>
</div>