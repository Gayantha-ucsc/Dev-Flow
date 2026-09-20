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
                var card = delBtn.closest('.admin-template-card');
                if (card) card.remove();
                if (window.showToast) showToast('success', '"' + delBtn.dataset.templateName + '" was deleted.');
                return;
            }

            var createBtn = e.target.closest('#createTemplateBtn');
            if (createBtn) {
                if (window.showToast) showToast('info', 'Creating a template from scratch isn\'t wired up yet.');
            }
        });
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