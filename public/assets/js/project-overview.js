document.addEventListener('DOMContentLoaded', function () {
    var workflow = document.querySelector('.project-overview__workflow');
    if (!workflow) return;

    var stageList = workflow.querySelector('.stage-list');
    if (!stageList) return;

    // Auto-expand the current stage only on first entry / project change.
    // After that, remember what the user left open (or closed) across reloads.
    var m = location.pathname.match(/projects\/(\d+)/);
    var projectKey = m ? m[1] : location.pathname + location.search;
    var saved = null;
    try { saved = JSON.parse(sessionStorage.getItem('devflow_stage_state') || 'null'); } catch (err) {}
    if (saved && saved.project === projectKey) {
        stageList.querySelectorAll('.stage-item--expanded').forEach(collapseStage);
        if (saved.open) {
            stageList.querySelectorAll('.stage-item').forEach(function (it) {
                if (stageKeyOf(it) === saved.open) {
                    var ex = it.querySelector('.stage-expand');
                    var tg = it.querySelector('.stage-row__expand-toggle');
                    ex.hidden = false;
                    it.classList.add('stage-item--expanded');
                    if (tg) tg.setAttribute('aria-expanded', 'true');
                }
            });
        }
    } else {
        saveStageState(projectKey, stageList);
    }
    stageList.dataset.projectKey = projectKey;

    stageList.querySelectorAll('.stage-item').forEach(setupZoom);
    stageList.querySelectorAll('.stage-item--expanded').forEach(function (it) { layoutStage(it); drawConnectorsFor(it); });

    // Clicking a real task opens its detail page (unless the stage is zoomed out, handled below).
    stageList.addEventListener('click', function (e) {
        var card = e.target.closest('.task-card[data-task-url]');
        if (!card) return;
        var item = card.closest('.stage-item');
        if (item && getScale(item) < 1) return;
        window.location.href = card.getAttribute('data-task-url');
    });

    // Clicking a task while zoomed out zooms into it instead of opening it.
    stageList.addEventListener('click', function (e) {
        var card = e.target.closest('.task-card:not(.task-card--add)');
        if (!card) return;
        var item = card.closest('.stage-item');
        if (!item || getScale(item) >= 1) return;
        e.stopPropagation();
        e.preventDefault();
        setScale(item, 1);
        var vp = item.querySelector('.stage-viewport');
        var strip = item.querySelector('[data-task-strip]');
        vp.scrollLeft = Math.max(0, card.offsetLeft + card.parentElement.offsetLeft - vp.clientWidth / 2 + card.offsetWidth / 2);
        vp.scrollTop = Math.max(0, card.offsetTop + card.parentElement.offsetTop - vp.clientHeight / 2 + card.offsetHeight / 2);
    }, true);

    stageList.addEventListener('click', function (e) {
        // Reorder/edit/delete are their own actions, not a toggle.
        if (e.target.closest('.stage-row__reorder')) return;
        if (e.target.closest('.stage-row__actions') && !e.target.closest('.stage-row__expand-toggle')) return;

        var row = e.target.closest('.stage-row');
        if (!row) return;

        var item = row.closest('.stage-item');
        var expand = item.querySelector('.stage-expand');
        var toggleBtn = row.querySelector('.stage-row__expand-toggle');
        var willOpen = expand.hasAttribute('hidden');

        if (willOpen) {
            stageList.querySelectorAll('.stage-item--expanded').forEach(function (openItem) {
                if (openItem === item) return;
                collapseStage(openItem);
            });
        }

        expand.hidden = !willOpen;
        item.classList.toggle('stage-item--expanded', willOpen);
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', String(willOpen));

        saveStageState(projectKey, stageList);
        if (willOpen) { layoutStage(item); drawConnectorsFor(item); scrollStageIntoView(item); }
    });

    var resizeTimer = null;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            stageList.querySelectorAll('.stage-item--expanded').forEach(function (it) { layoutStage(it); drawConnectorsFor(it); });
        }, 150);
    });
});

function collapseStage(item) {
    var expand = item.querySelector('.stage-expand');
    var toggleBtn = item.querySelector('.stage-row__expand-toggle');

    expand.hidden = true;
    item.classList.remove('stage-item--expanded');
    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
}

function drawConnectorsFor(stageItem) {
    var strip = stageItem.querySelector('[data-task-strip]');
    var svg = stageItem.querySelector('[data-connector-layer]');
    if (!strip || !svg) return;

    while (svg.firstChild) svg.removeChild(svg.firstChild);

    var canvasWidth = strip.offsetWidth;
    var canvasHeight = strip.offsetHeight;
    svg.setAttribute('width', canvasWidth);
    svg.setAttribute('height', canvasHeight);
    svg.setAttribute('viewBox', '0 0 ' + canvasWidth + ' ' + canvasHeight);

    var cards = strip.querySelectorAll('.task-card[id]');
    cards.forEach(function (card) {
        var dependsAttr = card.getAttribute('data-depends-ids');
        if (!dependsAttr) return;

        var toRect = rectRelativeTo(card, strip);

        dependsAttr.split(',').filter(Boolean).forEach(function (fromId) {
            var fromCard = document.getElementById(fromId);
            if (!fromCard) return; // defensive: id should always exist, but never let a bad id break the whole strip

            drawOneConnector(svg, rectRelativeTo(fromCard, strip), toRect);
        });
    });
}

// @returns {{left:number, top:number, width:number, height:number}}
function rectRelativeTo(card, strip) {
    var cardBox = card.getBoundingClientRect();
    var stripBox = strip.getBoundingClientRect();
    var k = strip.offsetWidth ? stripBox.width / strip.offsetWidth : 1; // current zoom scale
    return {
        left: (cardBox.left - stripBox.left) / k,
        top: (cardBox.top - stripBox.top) / k,
        width: cardBox.width / k,
        height: cardBox.height / k,
    };
}

function drawOneConnector(svg, fromRect, toRect) {
    var x1 = fromRect.left + fromRect.width;
    var y1 = fromRect.top + fromRect.height / 2;
    var x2 = toRect.left;
    var y2 = toRect.top + toRect.height / 2;
    var midX = x1 + (x2 - x1) / 2;

    var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('class', 'connector-line');
    path.setAttribute('d', 'M ' + x1 + ' ' + y1 + ' L ' + midX + ' ' + y1 + ' L ' + midX + ' ' + y2 + ' L ' + x2 + ' ' + y2);
    svg.appendChild(path);

    var arrowSize = 5;
    var arrow = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
    arrow.setAttribute('class', 'connector-arrow');
    arrow.setAttribute('points', [
        x2 + ',' + y2,
        (x2 - arrowSize * 1.6) + ',' + (y2 - arrowSize),
        (x2 - arrowSize * 1.6) + ',' + (y2 + arrowSize),
    ].join(' '));
    svg.appendChild(arrow);
}

/* ---- Zoom + fit-to-screen for expanded stages ---- */
var MIN_SCALE = 0.3, MAX_SCALE = 2;

function getScale(item) { return parseFloat(item.dataset.scale || '1'); }

function setupZoom(item) {
    var expand = item.querySelector('.stage-expand');
    var strip = item.querySelector('[data-task-strip]');
    var actions = item.querySelector('.stage-row__actions');
    if (!expand || !strip || !actions) return;

    // viewport (scrolls both ways) > sizer (scaled size) > strip (transformed)
    var vp = document.createElement('div');
    vp.className = 'stage-viewport';
    var sizer = document.createElement('div');
    sizer.className = 'stage-sizer';
    expand.insertBefore(vp, strip);
    vp.appendChild(sizer);
    sizer.appendChild(strip);

    var box = document.createElement('div');
    box.className = 'stage-zoom';
    box.innerHTML =
        '<button type="button" class="icon-btn-sm" data-zoom="out" aria-label="Zoom out">&minus;</button>' +
        '<button type="button" class="icon-btn-sm" data-zoom="fit" aria-label="Fit all tasks" title="Fit all">&#9974;</button>' +
        '<button type="button" class="icon-btn-sm" data-zoom="in" aria-label="Zoom in">+</button>';
    actions.insertBefore(box, actions.firstChild);

    box.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-zoom]');
        if (!btn) return;
        e.stopPropagation();
        if (expand.hasAttribute('hidden')) {
            expand.hidden = false;
            item.classList.add('stage-item--expanded');
            var t = item.querySelector('.stage-row__expand-toggle');
            if (t) t.setAttribute('aria-expanded', 'true');
        }
        var k = btn.getAttribute('data-zoom');
        if (k === 'in') setScale(item, getScale(item) + 0.15);
        else if (k === 'out') setScale(item, getScale(item) - 0.15);
        else fitStage(item);
    });
}

function setScale(item, s) {
    item.dataset.scale = String(Math.min(MAX_SCALE, Math.max(MIN_SCALE, s)));
    layoutStage(item);
    drawConnectorsFor(item);
}

function availHeight(item) {
    var vp = item.querySelector('.stage-viewport');
    var nav = document.querySelector('.navbar');
    var navH = nav ? nav.offsetHeight : 80;
    var offset = 16;
    var vpTop = vp.getBoundingClientRect().top - item.getBoundingClientRect().top; // header + padding above viewport
    var below = parseFloat(getComputedStyle(item.querySelector('.stage-expand')).paddingBottom) || 0;
    return Math.max(240, window.innerHeight - navH - offset * 2 - vpTop - below - 2);
}

function fitStage(item) {
    var vp = item.querySelector('.stage-viewport');
    var strip = item.querySelector('[data-task-strip]');
    if (!vp || !strip) return;
    var w = strip.offsetWidth, h = strip.offsetHeight;
    var s = Math.min((vp.clientWidth - 4) / w, (availHeight(item) - 4) / h);
    setScale(item, s); // may enlarge (up to MAX_SCALE) or shrink
    vp.scrollLeft = 0; vp.scrollTop = 0;
}

// Sizes viewport to fit the screen and the sizer to the scaled content.
function layoutStage(item) {
    var vp = item.querySelector('.stage-viewport');
    var strip = item.querySelector('[data-task-strip]');
    var sizer = item.querySelector('.stage-sizer');
    if (!vp || !strip || !sizer) return;
    var s = getScale(item);
    strip.style.transform = 'scale(' + s + ')';
    sizer.style.width = Math.ceil(strip.offsetWidth * s) + 'px';
    sizer.style.height = Math.ceil(strip.offsetHeight * s) + 'px';
    vp.style.maxHeight = availHeight(item) + 'px';
}

// Stage header sits at the top of the screen with a small offset.
function scrollStageIntoView(item) {
    var vp = item.querySelector('.stage-viewport');
    var tall = vp && vp.scrollHeight > vp.clientHeight - 1;
    if (tall || item.getBoundingClientRect().bottom > window.innerHeight) {
        item.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}


function stageKeyOf(item) {
    return item.getAttribute('data-stage-id') || item.getAttribute('data-stage-name');
}

function saveStageState(projectKey, stageList) {
    var open = stageList.querySelector('.stage-item--expanded');
    try {
        sessionStorage.setItem('devflow_stage_state', JSON.stringify({ project: projectKey, open: open ? stageKeyOf(open) : null }));
    } catch (err) {}
}
