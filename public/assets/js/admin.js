document.addEventListener('DOMContentLoaded', function () {

    function openModal(modal) {
        if (!modal) return;
        modal.hidden = false;
        requestAnimationFrame(function () { modal.classList.add('is-open'); });
    }
    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('is-open');
        setTimeout(function () { modal.hidden = true; }, 200);
    }
    document.querySelectorAll('[data-close-modal]').forEach(function (el) {
        el.addEventListener('click', function () {
            closeModal(el.closest('.modal-overlay'));
        });
    });

    // User management (/admin/users)
    var usersPage = document.querySelector('.admin-users-page');
    if (usersPage) {
        /* ---- Filter form: debounced search, instant selects, reset ---- */
        var form         = document.getElementById('adminUsersFilterForm');
        var searchInput  = document.getElementById('adminUserSearch');
        var statusSelect = document.getElementById('adminStatusFilter');
        var typeSelect   = document.getElementById('adminTypeFilter');
        var resetBtn     = document.getElementById('adminFiltersReset');

        if (form) {
            var debounceTimer;
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () { form.submit(); }, 450);
                });
            }
            [statusSelect, typeSelect].forEach(function (select) {
                if (select) select.addEventListener('change', function () { form.submit(); });
            });
            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    if (searchInput) searchInput.value = '';
                    if (statusSelect) statusSelect.value = 'all';
                    if (typeSelect) typeSelect.value = 'all';
                    form.submit();
                });
            }
        }

        /* Row actions */
        var deleteModal   = document.getElementById('deleteUserModal');
        var deleteNameEl  = document.getElementById('deleteUserName');
        var confirmDelete = document.getElementById('confirmDeleteUser');
        var pendingRow    = null;

        usersPage.addEventListener('click', function (e) {
            var editBtn = e.target.closest('.js-edit-user');
            if (editBtn) {
                var name = editBtn.closest('tr').dataset.userName;
                if (window.showToast) showToast('info', 'Editing ' + name + ' isn\'t wired up yet.');
                return;
            }

            var resetBtnEl = e.target.closest('.js-reset-password');
            if (resetBtnEl) {
                var rName = resetBtnEl.closest('tr').dataset.userName;
                if (window.showToast) showToast('success', 'A temporary password has been generated for ' + rName + '.');
                return;
            }

            var toggleBtn = e.target.closest('.js-toggle-active');
            if (toggleBtn && !toggleBtn.disabled) {
                var row        = toggleBtn.closest('tr');
                var willBeOn   = !toggleBtn.classList.contains('is-on');
                var statusCell = row.querySelector('.js-status-badge');

                toggleBtn.classList.toggle('is-on', willBeOn);
                toggleBtn.setAttribute('aria-checked', willBeOn ? 'true' : 'false');
                toggleBtn.title = willBeOn ? 'Deactivate account' : 'Reactivate account';
                row.dataset.active = willBeOn ? '1' : '0';
                row.classList.toggle('is-deactivated', !willBeOn);

                if (statusCell) {
                    statusCell.classList.toggle('badge--success', willBeOn);
                    statusCell.classList.toggle('badge--neutral', !willBeOn);
                    var label = statusCell.querySelector('.js-status-label');
                    if (label) label.textContent = willBeOn ? 'Active' : 'Deactivated';
                }

                var uName = row.dataset.userName;
                if (window.showToast) {
                    showToast(willBeOn ? 'success' : 'info', (willBeOn ? 'Reactivated ' : 'Deactivated ') + uName + '.');
                }
                return;
            }

            var deleteBtn = e.target.closest('.js-delete-user');
            if (deleteBtn && !deleteBtn.disabled) {
                pendingRow = deleteBtn.closest('tr');
                if (deleteNameEl) deleteNameEl.textContent = pendingRow.dataset.userName;
                openModal(deleteModal);
                return;
            }

            var createBtn = e.target.closest('#createUserBtn');
            if (createBtn) {
                if (window.showToast) showToast('info', 'Create user flow isn\'t wired up yet.');
            }
        });

        if (confirmDelete) {
            confirmDelete.addEventListener('click', function () {
                if (pendingRow) {
                    var uName = pendingRow.dataset.userName;
                    pendingRow.remove();
                    if (window.showToast) showToast('success', uName + ' was deleted.');
                    pendingRow = null;
                }
                closeModal(deleteModal);
            });
        }
    }

    // Roles & permissions (/admin/permissions)
    var permPage = document.querySelector('.admin-permissions-page');
    if (permPage) {
        var allowedCountEl    = document.getElementById('permAllowedCount');
        var restrictedCountEl = document.getElementById('permRestrictedCount');

        permPage.addEventListener('click', function (e) {
            var toggle = e.target.closest('.js-permission-toggle');
            if (!toggle) return;

            var willBeOn = !toggle.classList.contains('is-on');
            toggle.classList.toggle('is-on', willBeOn);
            toggle.setAttribute('aria-checked', willBeOn ? 'true' : 'false');

            if (allowedCountEl && restrictedCountEl) {
                var allowed    = parseInt(allowedCountEl.textContent, 10) || 0;
                var restricted = parseInt(restrictedCountEl.textContent, 10) || 0;
                if (willBeOn) {
                    allowed++;
                    restricted--;
                } else {
                    allowed--;
                    restricted++;
                }
                allowedCountEl.textContent = allowed;
                restrictedCountEl.textContent = restricted;
            }

            if (window.showToast) {
                var label = toggle.getAttribute('aria-label') || 'Permission';
                showToast(willBeOn ? 'success' : 'info', label + (willBeOn ? ' allowed.' : ' restricted.'));
            }
        });
    }

    // Audit log (/admin/audit)
    var auditPage = document.querySelector('.admin-audit-page');
    if (auditPage) {
        var auditForm      = document.getElementById('adminAuditFilterForm');
        var auditSearch    = document.getElementById('auditSearch');
        var auditActor     = document.getElementById('auditActorFilter');
        var auditAction    = document.getElementById('auditActionFilter');
        var auditTarget    = document.getElementById('auditTargetFilter');
        var auditDateFrom  = document.getElementById('auditDateFrom');
        var auditDateTo    = document.getElementById('auditDateTo');
        var auditResetBtn  = document.getElementById('auditFiltersReset');

        if (auditForm) {
            var auditDebounce;
            if (auditSearch) {
                auditSearch.addEventListener('input', function () {
                    clearTimeout(auditDebounce);
                    auditDebounce = setTimeout(function () { auditForm.submit(); }, 450);
                });
            }
            [auditActor, auditAction, auditTarget, auditDateFrom, auditDateTo].forEach(function (field) {
                if (field) field.addEventListener('change', function () { auditForm.submit(); });
            });
            if (auditResetBtn) {
                auditResetBtn.addEventListener('click', function () {
                    if (auditSearch) auditSearch.value = '';
                    if (auditActor) auditActor.value = 'all';
                    if (auditAction) auditAction.value = 'all';
                    if (auditTarget) auditTarget.value = 'all';
                    if (auditDateFrom) auditDateFrom.value = '';
                    if (auditDateTo) auditDateTo.value = '';
                    auditForm.submit();
                });
            }
        }
    }

    // Workflow templates (/admin/templates)
    var templatesPage = document.querySelector('.admin-templates-page');
    if (templatesPage) {
        var tplForm       = document.getElementById('adminTemplatesFilterForm');
        var tplSearch     = document.getElementById('templateSearch');
        var tplDefaultBox = document.getElementById('templateDefaultOnly');

        if (tplForm) {
            var tplDebounce;
            if (tplSearch) {
                tplSearch.addEventListener('input', function () {
                    clearTimeout(tplDebounce);
                    tplDebounce = setTimeout(function () { tplForm.submit(); }, 450);
                });
            }
            if (tplDefaultBox) {
                tplDefaultBox.addEventListener('change', function () { tplForm.submit(); });
            }
        }

        var createTemplateModal   = document.querySelector('[data-modal="create-template-modal"]');
        var deleteTemplateModal   = document.querySelector('[data-modal="delete-template-modal"]');
        var deleteTemplateNameEl  = deleteTemplateModal ? deleteTemplateModal.querySelector('[data-delete-template-name]') : null;
        var confirmDeleteTemplate = document.getElementById('confirmDeleteTemplate');
        var pendingTemplateCard   = null;

        var newTemplateName          = document.getElementById('newTemplateName');
        var newTemplateDesc          = document.getElementById('newTemplateDesc');
        var newTemplateDefaultSwitch = document.getElementById('newTemplateDefaultSwitch');
        var newTemplateStageList     = document.getElementById('newTemplateStageList');
        var newTemplateAddStage      = document.getElementById('newTemplateAddStage');
        var newTemplateStageCount    = document.getElementById('newTemplateStageCount');
        var submitCreateTemplate     = document.getElementById('submitCreateTemplate');

        templatesPage.addEventListener('click', function (e) {
            var editBtn = e.target.closest('.js-edit-template');
            if (editBtn) {
                if (window.showToast) showToast('info', 'Editing "' + editBtn.dataset.templateName + '" isn\'t wired up yet.');
                return;
            }

            var dupBtn = e.target.closest('.js-duplicate-template');
            if (dupBtn) {
                if (window.showToast) showToast('success', 'Duplicated "' + dupBtn.dataset.templateName + '" as an editable copy.');
                return;
            }

            var delBtn = e.target.closest('.js-delete-template');
            if (delBtn && !delBtn.disabled) {
                pendingTemplateCard = delBtn.closest('.admin-template-card');
                if (deleteTemplateNameEl) deleteTemplateNameEl.textContent = delBtn.dataset.templateName;
                openModal(deleteTemplateModal);
                return;
            }

            var createBtn = e.target.closest('#createTemplateBtn');
            if (createBtn) {
                resetTemplateModal();
                openModal(createTemplateModal);
            }
        });

        if (confirmDeleteTemplate) {
            confirmDeleteTemplate.addEventListener('click', function () {
                if (pendingTemplateCard) {
                    var tName = pendingTemplateCard.querySelector('h3') ? pendingTemplateCard.querySelector('h3').textContent : 'Template';
                    pendingTemplateCard.remove();
                    if (window.showToast) showToast('success', '"' + tName + '" was deleted.');
                    pendingTemplateCard = null;
                }
                closeModal(deleteTemplateModal);
            });
        }

        /* ---- Create template modal ---- */
        if (createTemplateModal) {
            newTemplateDefaultSwitch.addEventListener('click', function () {
                var willBeOn = !newTemplateDefaultSwitch.classList.contains('is-on');
                newTemplateDefaultSwitch.classList.toggle('is-on', willBeOn);
                newTemplateDefaultSwitch.setAttribute('aria-checked', willBeOn ? 'true' : 'false');
            });

            newTemplateAddStage.addEventListener('click', function () {
                addTemplateStageRow('');
            });

            newTemplateStageList.addEventListener('click', function (e) {
                var removeBtn = e.target.closest('[data-remove-template-stage]');
                if (removeBtn) {
                    removeBtn.closest('.wizard-stage-row').remove();
                    renumberTemplateStages();
                    return;
                }
                var renameBtn = e.target.closest('[data-rename-template-stage]');
                if (renameBtn) {
                    var row = renameBtn.closest('.wizard-stage-row');
                    var nameEl = row.querySelector('.wizard-stage-row__name');
                    var input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'wizard-stage-row__name-input';
                    input.value = nameEl.textContent;
                    nameEl.replaceWith(input);
                    input.focus();
                    input.select();

                    function commit() {
                        var span = document.createElement('span');
                        span.className = 'wizard-stage-row__name';
                        span.textContent = input.value.trim() || 'Untitled stage';
                        input.replaceWith(span);
                    }
                    input.addEventListener('blur', commit);
                    input.addEventListener('keydown', function (ev) {
                        if (ev.key === 'Enter') input.blur();
                    });
                }
            });

            submitCreateTemplate.addEventListener('click', function () {
                var name = newTemplateName.value.trim();
                var err = createTemplateModal.querySelector('[data-error-for="newTemplateName"]');
                if (!name) {
                    if (err) { err.textContent = 'Template name is required.'; err.classList.add('is-visible'); }
                    return;
                }
                if (err) { err.textContent = ''; err.classList.remove('is-visible'); }

                var stageNames = Array.prototype.map.call(
                    newTemplateStageList.querySelectorAll('.wizard-stage-row__name'),
                    function (el) { return el.textContent.trim(); }
                ).filter(Boolean);

                prependTemplateCard({
                    name: name,
                    description: newTemplateDesc.value.trim() || 'No description provided.',
                    isDefault: newTemplateDefaultSwitch.classList.contains('is-on'),
                    stages: stageNames
                });

                closeModal(createTemplateModal);
                if (window.showToast) showToast('success', '"' + name + '" was created.');
            });
        }

        function resetTemplateModal() {
            newTemplateName.value = '';
            newTemplateDesc.value = '';
            newTemplateDefaultSwitch.classList.remove('is-on');
            newTemplateDefaultSwitch.setAttribute('aria-checked', 'false');
            newTemplateStageList.innerHTML = '';
            renumberTemplateStages();
            var err = createTemplateModal.querySelector('[data-error-for="newTemplateName"]');
            if (err) { err.textContent = ''; err.classList.remove('is-visible'); }
        }

        function addTemplateStageRow(name) {
            var icons = window.TEMPLATE_ICONS || {};
            var row = document.createElement('div');
            row.className = 'wizard-stage-row';
            row.innerHTML =
                '<span class="wizard-stage-row__grip">' + (icons.grip || '') + '</span>' +
                '<span class="wizard-stage-row__badge">0</span>' +
                '<span class="wizard-stage-row__name"></span>' +
                '<div class="wizard-stage-row__actions">' +
                    '<button type="button" class="wizard-stage-row__action" data-rename-template-stage title="Rename">' + (icons.pencil || '') + '</button>' +
                    '<button type="button" class="wizard-stage-row__action wizard-stage-row__action--danger" data-remove-template-stage title="Remove">' + (icons.trash || '') + '</button>' +
                '</div>';
            row.querySelector('.wizard-stage-row__name').textContent = name || ('Stage ' + (newTemplateStageList.children.length + 1));
            newTemplateStageList.appendChild(row);
            renumberTemplateStages();
        }

        function renumberTemplateStages() {
            var rows = newTemplateStageList.querySelectorAll('.wizard-stage-row');
            rows.forEach(function (row, i) {
                row.querySelector('.wizard-stage-row__badge').textContent = i + 1;
            });
            if (newTemplateStageCount) newTemplateStageCount.textContent = rows.length + ' stage' + (rows.length === 1 ? '' : 's');
        }

        function prependTemplateCard(tpl) {
            var grid = document.querySelector('.template-grid');
            if (!grid) return;

            var stageChips = tpl.stages.slice(0, 4).map(function (s, i) {
                return (i > 0 ? (window.TEMPLATE_ICONS ? '' : '&rarr;') : '') + '<span class="badge badge--neutral">' + s + '</span>';
            }).join('');
            var extra = tpl.stages.length > 4 ? '<span class="badge badge--outline">+' + (tpl.stages.length - 4) + ' more</span>' : '';

            var card = document.createElement('div');
            card.className = 'card admin-template-card';
            card.innerHTML =
                '<div class="admin-template-card__head">' +
                    '<span class="template-card__icon template-card__icon--orange">' + (window.TEMPLATE_ICONS ? window.TEMPLATE_ICONS.layout : '') + '</span>' +
                    '<div class="admin-template-card__title-block">' +
                        '<div class="admin-template-card__title-row"><h3>' + tpl.name + '</h3>' + (tpl.isDefault ? '<span class="badge badge--primary">Default</span>' : '') + '</div>' +
                        '<span class="admin-template-card__stage-count">' + tpl.stages.length + ' stages</span>' +
                    '</div>' +
                '</div>' +
                '<p class="admin-template-card__desc">' + tpl.description + '</p>' +
                '<div class="admin-template-card__stages">' +
                    '<span class="admin-template-card__stages-label">Stages</span>' +
                    '<div class="admin-template-card__chips">' + stageChips + extra + '</div>' +
                '</div>' +
                '<div class="admin-template-card__footer">' +
                    '<span class="admin-template-card__meta">Created by you &bull; Just now</span>' +
                    '<div class="admin-template-card__actions">' +
                        '<button type="button" class="icon-btn-sm js-edit-template" data-template-name="' + tpl.name + '" title="Edit template">' + (window.TEMPLATE_ICONS ? window.TEMPLATE_ICONS.pencil : '') + '</button>' +
                        '<button type="button" class="icon-btn-sm icon-btn-sm--danger js-delete-template" data-template-name="' + tpl.name + '" title="Delete template">' + (window.TEMPLATE_ICONS ? window.TEMPLATE_ICONS.trash : '') + '</button>' +
                    '</div>' +
                '</div>';
            grid.insertBefore(card, grid.firstChild);
        }
    }

    // System settings (/admin/settings)
    var settingsPage = document.querySelector('.admin-settings-page');
    if (settingsPage) {
        var modal        = document.getElementById('editSettingModal');
        var titleEl      = document.getElementById('editSettingTitle');
        var descEl       = document.getElementById('editSettingDesc');
        var numberField  = document.getElementById('editSettingNumberField');
        var numberInput  = document.getElementById('editSettingNumberInput');
        var unitLabel    = document.getElementById('editSettingUnitLabel');
        var selectField  = document.getElementById('editSettingSelectField');
        var selectInput  = document.getElementById('editSettingSelectInput');
        var updatedByEl  = document.getElementById('editSettingUpdatedBy');
        var updatedAtEl  = document.getElementById('editSettingUpdatedAt');
        var saveBtn      = document.getElementById('saveSettingBtn');
        var stepUpBtn    = document.getElementById('editSettingStepUp');
        var stepDownBtn  = document.getElementById('editSettingStepDown');
        var activeKey    = null;
        var activeType   = null;

        settingsPage.addEventListener('click', function (e) {
            var editBtn = e.target.closest('.js-edit-setting');
            if (!editBtn) return;

            activeKey  = editBtn.dataset.key;
            activeType = editBtn.dataset.type;

            titleEl.textContent = editBtn.dataset.label;
            descEl.textContent  = editBtn.dataset.description;
            updatedByEl.textContent = editBtn.dataset.updatedBy;
            updatedAtEl.textContent = editBtn.dataset.updatedAt;

            if (activeType === 'select') {
                numberField.hidden = true;
                selectField.hidden = false;
                selectInput.value = editBtn.dataset.value;
            } else {
                selectField.hidden = true;
                numberField.hidden = false;
                numberInput.value = editBtn.dataset.value;
                unitLabel.textContent = editBtn.dataset.unit;
            }

            openModal(modal);
        });

        function stepNumber(delta) {
            var current = parseInt(numberInput.value, 10);
            if (isNaN(current)) current = 0;
            var next = current + delta;
            if (next < 0) next = 0;
            numberInput.value = next;
        }
        if (stepUpBtn) stepUpBtn.addEventListener('click', function () { stepNumber(1); });
        if (stepDownBtn) stepDownBtn.addEventListener('click', function () { stepNumber(-1); });

        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                if (!activeKey) return;

                var display = settingsPage.querySelector('.js-setting-display[data-key="' + activeKey + '"]');
                var newValue = activeType === 'select' ? selectInput.value : numberInput.value;

                if (display) display.textContent = newValue;

                if (window.showToast) showToast('success', titleEl.textContent + ' updated.');
                closeModal(modal);
                activeKey = null;
            });
        }
    }
});