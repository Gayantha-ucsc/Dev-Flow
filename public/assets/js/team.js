document.addEventListener('DOMContentLoaded', function () {
    /* ---- Member table filters ---- */
    var table = document.getElementById('memberTable');
    if (table) {
        var searchInput  = document.getElementById('memberSearch');
        var roleFilters   = document.getElementById('roleFilters');
        var statusFilter  = document.getElementById('statusFilter');
        var noResults     = document.getElementById('memberNoResults');

        if (searchInput && roleFilters && statusFilter) {
            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            var activeRole = 'all';

            function applyFilters() {
                var query = (searchInput.value || '').trim().toLowerCase();
                var status = statusFilter.value;
                var visibleCount = 0;

                rows.forEach(function (row) {
                    var matchesSearch = !query || row.dataset.name.indexOf(query) !== -1;
                    var matchesRole   = activeRole === 'all' || row.dataset.role === activeRole;
                    var matchesStatus = status === 'all' || row.dataset.status === status;
                    var visible = matchesSearch && matchesRole && matchesStatus;
                    row.style.display = visible ? '' : 'none';
                    if (visible) visibleCount++;
                });

                if (noResults) noResults.hidden = visibleCount !== 0;
            }

            searchInput.addEventListener('input', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
            roleFilters.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-filter]');
                if (!btn) return;
                roleFilters.querySelectorAll('.filter-pill').forEach(function (p) { p.classList.remove('is-active'); });
                btn.classList.add('is-active');
                activeRole = btn.dataset.filter;
                applyFilters();
            });
        }
    }

    /* ---- Manage member dropdown + modals ---- */
    var manageMenu = document.getElementById('memberManageMenu');
    var changeRoleModal = document.getElementById('changeRoleModal');
    var deactivateModal = document.getElementById('deactivateModal');
    var activeMember = null;

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

    document.querySelectorAll('.js-manage-member').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            activeMember = {
                id: btn.dataset.memberId,
                name: btn.dataset.memberName,
                role: btn.dataset.memberRole,
                status: btn.dataset.memberStatus
            };
            if (!manageMenu) return;
            var rect = btn.getBoundingClientRect();
            manageMenu.hidden = false;
            manageMenu.style.top = (rect.bottom + 4) + 'px';
            manageMenu.style.left = (rect.left - 160) + 'px';
        });
    });

    document.addEventListener('click', function () {
        if (manageMenu) manageMenu.hidden = true;
    });

    if (manageMenu) {
        manageMenu.addEventListener('click', function (e) {
            e.stopPropagation();
            var action = e.target.closest('[data-action]');
            if (!action || !activeMember) return;
            manageMenu.hidden = true;
            if (action.dataset.action === 'change-role') {
                var nameEl = document.getElementById('changeRoleMemberName');
                if (nameEl) nameEl.textContent = activeMember.name;
                openModal(changeRoleModal);
            } else if (action.dataset.action === 'deactivate') {
                var deactName = document.getElementById('deactivateMemberName');
                if (deactName) deactName.textContent = activeMember.name;
                openModal(deactivateModal);
            }
        });
    }

    var changeRoleForm = document.getElementById('changeRoleForm');
    if (changeRoleForm) {
        changeRoleForm.addEventListener('submit', function (e) {
            e.preventDefault();
            closeModal(changeRoleModal);
            if (window.showToast) window.showToast('success', 'Role change submitted for approval.');
        });
    }

    var deactivateForm = document.getElementById('deactivateForm');
    if (deactivateForm) {
        deactivateForm.addEventListener('submit', function (e) {
            e.preventDefault();
            closeModal(deactivateModal);
            var action = deactivateForm.querySelector('input[name="action"]:checked');
            var label = action && action.value === 'remove' ? 'Removal request submitted.' : 'Member deactivated.';
            if (window.showToast) window.showToast('success', label);
        });
    }

    /* ---- Add member: identifier entry (no directory lookup, same pattern as project wizard) ---- */
    var userIdentifier = document.getElementById('userIdentifier');
    if (userIdentifier) {
        var identifierError = document.getElementById('identifierError');
        var addIdentifierBtn = document.getElementById('addIdentifierBtn');
        var memberRoleSelect = document.getElementById('memberRole');
        var addedSection = document.getElementById('addedMemberSection');
        var addedList = document.getElementById('addedMemberList');
        var submitBtn = document.getElementById('submitAddMember');
        var addedMember = null; // { identifier, role }

        var AVATAR_COLORS = ['avatar-color-1', 'avatar-color-2', 'avatar-color-3', 'avatar-color-4'];

        function colorForIdentifier(identifier) {
            var sum = 0;
            for (var i = 0; i < identifier.length; i++) sum += identifier.charCodeAt(i);
            return AVATAR_COLORS[sum % AVATAR_COLORS.length];
        }

        function roleLabel(role) {
            var labels = { developer: 'Developer', designer: 'Designer', team_lead: 'Team Lead', manager: 'Manager' };
            return labels[role] || role;
        }

        function isValidEmail(value) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }
        function isValidUsername(value) {
            return /^[a-zA-Z0-9._-]{3,50}$/.test(value);
        }
        function isValidIdentifier(value) {
            return isValidEmail(value) || isValidUsername(value);
        }

        function render() {
            if (!addedMember) {
                addedSection.hidden = true;
                addedList.innerHTML = '';
                submitBtn.disabled = true;
                return;
            }
            addedSection.hidden = false;
            addedList.innerHTML =
                '<div class="team-member-row">' +
                    '<span class="team-member-row__avatar ' + colorForIdentifier(addedMember.identifier) + '">' +
                        addedMember.identifier.charAt(0).toUpperCase() +
                    '</span>' +
                    '<span class="team-member-row__identifier"></span>' +
                    '<span class="team-member-row__role">' + roleLabel(addedMember.role).toUpperCase() + '</span>' +
                    '<button type="button" class="team-member-row__remove" id="removeAddedMember">' +
                        '&times;' +
                    '</button>' +
                '</div>';
            addedList.querySelector('.team-member-row__identifier').textContent = addedMember.identifier;
            document.getElementById('removeAddedMember').addEventListener('click', function () {
                addedMember = null;
                userIdentifier.value = '';
                render();
            });
            submitBtn.disabled = false;
        }

        function addIdentifier() {
            var value = userIdentifier.value.trim();
            identifierError.textContent = '';
            identifierError.classList.remove('is-visible');

            if (!value || !isValidIdentifier(value)) {
                identifierError.textContent = 'Enter a valid username or email address.';
                identifierError.classList.add('is-visible');
                return;
            }

            // Accepted as-is - no directory lookup is performed, the identifier is
            // assumed correct and resolved server-side when the form is submitted.
            addedMember = { identifier: value, role: memberRoleSelect.value };
            userIdentifier.value = '';
            render();
        }

        addIdentifierBtn.addEventListener('click', addIdentifier);
        userIdentifier.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); addIdentifier(); }
        });

        render();
    }

    var addMemberForm = document.getElementById('addMemberForm');
    if (addMemberForm) {
        addMemberForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var role = document.getElementById('memberRole');
            var isDirectTL = role && role.value === 'team_lead' && role.querySelector('option[value="team_lead"]') && role.options[0].textContent.indexOf('direct') !== -1;
            var msg = isDirectTL
                ? 'Team Lead added successfully (unilateral add).'
                : 'Member request submitted for qualifying approver review.';
            if (window.showToast) window.showToast('success', msg);
        });
    }

    /* ---- Approval queue actions ---- */
    document.querySelectorAll('.js-approve-request').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = btn.closest('.approval-item');
            if (item) item.remove();
            if (window.showToast) window.showToast('success', 'Member request approved.');
        });
    });
    document.querySelectorAll('.js-reject-request').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = btn.closest('.approval-item');
            if (item) item.remove();
            if (window.showToast) window.showToast('info', 'Member request rejected.');
        });
    });

    /* ---- Invite client ---- */
    var inviteForm = document.getElementById('inviteClientForm');
    if (inviteForm) {
        inviteForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (window.showToast) window.showToast('success', 'Client invitation sent successfully.');
        });
    }
});