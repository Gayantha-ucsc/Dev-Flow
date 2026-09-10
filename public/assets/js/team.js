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

    /* ---- Add member: user search ---- */
    var userSearch = document.getElementById('userSearch');
    var userResults = document.getElementById('userSearchResults');
    if (userSearch && userResults) {
        var searchIdle = document.getElementById('userSearchIdle');
        var searchLoading = document.getElementById('userSearchLoading');
        var searchEmpty = document.getElementById('userSearchEmpty');
        var selectedUser = document.getElementById('selectedUser');
        var selectedPlaceholder = document.getElementById('selectedUserPlaceholder');
        var submitBtn = document.getElementById('submitAddMember');
        var MIN_QUERY_LENGTH = 3;
        var DEBOUNCE_MS = 300;
        var debounceTimer = null;
        var currentRequestId = 0;

        function setState(state) {
            // state: 'idle' | 'loading' | 'empty' | 'results'
            if (searchIdle) searchIdle.hidden = state !== 'idle';
            if (searchLoading) searchLoading.hidden = state !== 'loading';
            if (searchEmpty) searchEmpty.hidden = state !== 'empty';
        }

        function clearRenderedResults() {
            userResults.querySelectorAll('.user-result').forEach(function (el) { el.remove(); });
        }

        function avatarClass(name) {
            var sum = 0;
            for (var i = 0; i < name.length; i++) sum += name.charCodeAt(i);
            var classes = ['primary', 'pink', 'teal', 'amber'];
            return classes[sum % classes.length];
        }

        function renderResults(users) {
            clearRenderedResults();
            users.forEach(function (user) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'user-result';
                btn.dataset.userId = user.user_id;
                btn.dataset.userName = user.name;
                btn.dataset.userEmail = user.email;
                btn.innerHTML =
                    '<span class="avatar avatar--' + avatarClass(user.name) + '">' +
                        user.name.charAt(0).toUpperCase() +
                    '</span>' +
                    '<span class="user-result__body">' +
                        '<span class="user-result__name"></span>' +
                        '<span class="user-result__email"></span>' +
                    '</span>';
                btn.querySelector('.user-result__name').textContent = user.name;
                btn.querySelector('.user-result__email').textContent = user.email;
                btn.addEventListener('click', function () {
                    userResults.querySelectorAll('.user-result').forEach(function (b) {
                        b.classList.remove('is-selected');
                    });
                    btn.classList.add('is-selected');
                    showSelectedUser(user.name, user.email);
                });
                userResults.appendChild(btn);
            });
        }

        function showSelectedUser(name, email) {
            if (selectedPlaceholder) selectedPlaceholder.hidden = true;
            if (selectedUser) {
                selectedUser.hidden = false;
                document.getElementById('selectedUserName').textContent = name;
                document.getElementById('selectedUserEmail').textContent = email;
                var avatar = document.getElementById('selectedUserAvatar');
                avatar.textContent = name.charAt(0).toUpperCase();
                avatar.className = 'avatar avatar--primary';
            }
            if (submitBtn) submitBtn.disabled = false;
        }

        function clearSelectedUser() {
            userResults.querySelectorAll('.user-result').forEach(function (b) { b.classList.remove('is-selected'); });
            if (selectedUser) selectedUser.hidden = true;
            if (selectedPlaceholder) selectedPlaceholder.hidden = false;
            if (submitBtn) submitBtn.disabled = true;
        }

        function searchEndpoint(query) {
            var base = (window.APP_BASE_URL || '/').replace(/\/$/, '');
            return base + '/team/search-users?q=' + encodeURIComponent(query);
        }

        function runSearch(query) {
            var requestId = ++currentRequestId;
            setState('loading');
            fetch(searchEndpoint(query))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (requestId !== currentRequestId) return; // stale response, a newer query superseded it
                    var results = data.results || [];
                    if (results.length === 0) {
                        clearRenderedResults();
                        setState('empty');
                    } else {
                        renderResults(results);
                        setState('results');
                    }
                })
                .catch(function () {
                    if (requestId !== currentRequestId) return;
                    clearRenderedResults();
                    setState('empty');
                    if (window.showToast) window.showToast('error', 'Could not search users right now.');
                });
        }

        userSearch.addEventListener('input', function () {
            var q = userSearch.value.trim();
            clearTimeout(debounceTimer);
            clearSelectedUser();

            if (q.length < MIN_QUERY_LENGTH) {
                currentRequestId++; // invalidate any in-flight request
                clearRenderedResults();
                setState('idle');
                return;
            }

            debounceTimer = setTimeout(function () { runSearch(q); }, DEBOUNCE_MS);
        });

        var clearBtn = document.getElementById('clearSelectedUser');
        if (clearBtn) {
            clearBtn.addEventListener('click', clearSelectedUser);
        }
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