document.addEventListener('DOMContentLoaded', function () {
    var workflow = document.querySelector('.project-overview__workflow');
    if (!workflow) return;

    var stageList = workflow.querySelector('.stage-list');
    if (!stageList) return;

    stageList.querySelectorAll('.stage-item--expanded').forEach(drawConnectorsFor);

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

        if (willOpen) drawConnectorsFor(item);
    });

    var resizeTimer = null;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            stageList.querySelectorAll('.stage-item--expanded').forEach(drawConnectorsFor);
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

    var canvasWidth = strip.scrollWidth;
    var canvasHeight = strip.scrollHeight;
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
    return {
        left: cardBox.left - stripBox.left + strip.scrollLeft,
        top: cardBox.top - stripBox.top + strip.scrollTop,
        width: cardBox.width,
        height: cardBox.height,
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