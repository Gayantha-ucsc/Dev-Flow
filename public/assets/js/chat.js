document.addEventListener('DOMContentLoaded', function () {
    var dataEl = document.getElementById('chatData');
    if (!dataEl) return;

    var chatData;
    try {
        chatData = JSON.parse(dataEl.textContent);
    } catch (e) {
        return;
    }

    var messagesEl = document.getElementById('chatMessages');
    var roomTitle = document.getElementById('chatRoomTitle');
    var roomSubtitle = document.getElementById('chatRoomSubtitle');
    var roomBadge = document.getElementById('chatRoomBadge');
    var lockedBanner = document.getElementById('clientLockedBanner');
    var composer = document.getElementById('chatComposer');
    var currentRoom = { type: 'project', key: null };

    var roleLabels = {
        manager: 'Manager',
        team_lead: 'Team Lead',
        developer: 'Developer',
        designer: 'Designer',
        client: 'Client'
    };

    function escapeHtml(str) {
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function setActiveNav(type) {
        document.querySelectorAll('.chat-room-btn').forEach(function (btn) {
            var room = btn.dataset.room;
            var isActive = room === type
                || (type === 'stage' && btn.id === 'openStageChat')
                || (type === 'task' && btn.id === 'openTaskChat');
            btn.classList.toggle('is-active', isActive);
        });
    }

    function updateRoomBadge(type) {
        if (!roomBadge) return;
        if (type === 'client') {
            roomBadge.textContent = 'Joint approval';
            roomBadge.className = 'chat-perm-badge chat-perm-badge--joint';
        } else {
            roomBadge.textContent = 'Team Lead led';
            roomBadge.className = 'chat-perm-badge';
        }
    }

    function renderMessages(messages) {
        if (!messagesEl) return;
        messagesEl.innerHTML = '';

        if (!messages || !messages.length) {
            messagesEl.innerHTML = '<p class="chat-messages__empty">No messages yet. Start the conversation.</p>';
            return;
        }

        messages.forEach(function (msg) {
            var el = document.createElement('div');
            el.className = 'chat-message';
            el.innerHTML =
                '<span class="avatar avatar--primary">' + escapeHtml((msg.author || '?').charAt(0).toUpperCase()) + '</span>' +
                '<div class="chat-message__body">' +
                    '<div class="chat-message__header">' +
                        '<span class="chat-message__author">' + escapeHtml(msg.author || '') + '</span>' +
                        '<span class="chat-message__role">' + escapeHtml(roleLabels[msg.role] || msg.role || '') + '</span>' +
                        '<time class="chat-message__time">' + escapeHtml(msg.createdAt || '') + '</time>' +
                    '</div>' +
                    '<p class="chat-message__text">' + escapeHtml(msg.body || '') + '</p>' +
                '</div>';
            messagesEl.appendChild(el);
        });

        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function loadRoom(type, key) {
        currentRoom = { type: type, key: key };
        var messages = [];
        var title = 'Project Chat';
        var subtitle = 'All team members';

        if (type === 'project') {
            messages = chatData.projectMessages || [];
        } else if (type === 'stage') {
            messages = (chatData.stageMessages && chatData.stageMessages[key]) || [];
            title = 'Stage Chat';
            subtitle = key;
        } else if (type === 'task') {
            messages = (chatData.taskMessages && chatData.taskMessages[String(key)]) || [];
            title = 'Task Chat';
            var sel = document.getElementById('taskChatSelect');
            subtitle = sel ? sel.options[sel.selectedIndex].text : 'Task #' + key;
        } else if (type === 'client') {
            messages = chatData.clientMessages || [];
            title = 'Client Chat Room';
            subtitle = 'Manager, Team Lead, Client & approved contributors';
            if (lockedBanner) lockedBanner.hidden = true;
        }

        if (roomTitle) roomTitle.textContent = title;
        if (roomSubtitle) roomSubtitle.textContent = subtitle;
        updateRoomBadge(type);
        setActiveNav(type);
        renderMessages(messages);
    }

    /* Deep link from task detail: ?room=task&task=101 */
    var params = new URLSearchParams(window.location.search);
    if (params.get('room') === 'task' && params.get('task')) {
        var taskSel = document.getElementById('taskChatSelect');
        var taskId = params.get('task');
        if (taskSel) taskSel.value = taskId;
        loadRoom('task', taskId);
    } else if (params.get('room') === 'stage' && params.get('stage')) {
        var stageSel = document.getElementById('stageChatSelect');
        var stageName = params.get('stage');
        if (stageSel) stageSel.value = stageName;
        loadRoom('stage', stageName);
    } else if (params.get('room') === 'client') {
        loadRoom('client');
    } else {
        loadRoom('project');
    }

    document.querySelectorAll('.chat-room-btn[data-room]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var room = btn.dataset.room;

            if (room === 'client' && btn.disabled) {
                if (lockedBanner) lockedBanner.hidden = false;
                return;
            }

            if (room === 'project') {
                loadRoom('project');
            } else if (room === 'client') {
                loadRoom('client');
            } else if (room === 'stage') {
                var stageSelect = document.getElementById('stageChatSelect');
                if (stageSelect && stageSelect.value) loadRoom('stage', stageSelect.value);
            } else if (room === 'task') {
                var taskSelect = document.getElementById('taskChatSelect');
                if (taskSelect && taskSelect.value) loadRoom('task', taskSelect.value);
            }
        });
    });

    var stageSelect = document.getElementById('stageChatSelect');
    if (stageSelect) {
        stageSelect.addEventListener('change', function () {
            if (stageSelect.value) loadRoom('stage', stageSelect.value);
        });
    }

    var taskSelect = document.getElementById('taskChatSelect');
    if (taskSelect) {
        taskSelect.addEventListener('change', function () {
            if (taskSelect.value) loadRoom('task', taskSelect.value);
        });
    }

    if (composer) {
        composer.addEventListener('submit', function (e) {
            e.preventDefault();
            var textarea = composer.querySelector('textarea');
            if (!textarea || !textarea.value.trim()) return;

            var empty = messagesEl.querySelector('.chat-messages__empty');
            if (empty) empty.remove();

            var el = document.createElement('div');
            el.className = 'chat-message chat-message--self';
            el.innerHTML =
                '<span class="avatar avatar--primary">Y</span>' +
                '<div class="chat-message__body">' +
                    '<div class="chat-message__header">' +
                        '<span class="chat-message__author">You</span>' +
                        '<time class="chat-message__time">Just now</time>' +
                    '</div>' +
                    '<p class="chat-message__text">' + escapeHtml(textarea.value.trim()) + '</p>' +
                '</div>';
            messagesEl.appendChild(el);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            textarea.value = '';

            if (window.showToast) window.showToast('success', 'Message sent.');
        });
    }

    var requestAccess = document.getElementById('requestClientAccess');
    if (requestAccess) {
        requestAccess.addEventListener('click', function () {
            var accessPanel = document.getElementById('chat-access');
            if (accessPanel) accessPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            if (window.showToast) window.showToast('success', 'Use the access panel to submit your request.');
        });
    }

    document.querySelectorAll('.js-grant-access').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var req = btn.closest('.chat-access-request');
            if (req) req.remove();
            if (window.showToast) window.showToast('success', 'Client chat access granted.');
        });
    });

    document.querySelectorAll('.js-deny-access').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var req = btn.closest('.chat-access-request');
            if (req) req.remove();
            if (window.showToast) window.showToast('info', 'Access request denied.');
        });
    });

    var accessForm = document.getElementById('accessRequestForm');
    if (accessForm) {
        accessForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (window.showToast) window.showToast('success', 'Access request submitted for approval.');
            accessForm.reset();
        });
    }
});
