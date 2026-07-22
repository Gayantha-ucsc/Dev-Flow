document.querySelectorAll('[data-datepicker]').forEach(function (field) {
    const trigger = field.querySelector('[data-date-trigger]');
    const display = field.querySelector('[data-date-display]');
    const hiddenInput = field.querySelector('[data-date-value]');
    const calendarEl = field.querySelector('[data-date-calendar]');

    let viewDate = new Date();
    viewDate.setDate(1);
    let selectedDate = null;
    let view = 'days'; // 'days' | 'months' | 'years'
    let yearRangeStart = Math.floor(viewDate.getFullYear() / 12) * 12;

    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const monthShort = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    function formatDisplay(d) {
        return `${String(d.getMonth() + 1).padStart(2, '0')}/${String(d.getDate()).padStart(2, '0')}/${d.getFullYear()}`;
    }
    function formatISO(d) {
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }
    function headerHtml(centerHtml) {
        return `
            <div class="date-calendar__header">
                <button type="button" class="date-calendar__nav" data-nav="-1">&lsaquo;</button>
                <span class="date-calendar__center">${centerHtml}</span>
                <button type="button" class="date-calendar__nav" data-nav="1">&rsaquo;</button>
            </div>`;
    }

    function render() {
        if (view === 'days') renderDays();
        else if (view === 'months') renderMonths();
        else renderYears();
    }

    function renderDays() {
        const year = viewDate.getFullYear();
        const month = viewDate.getMonth();
        const startWeekday = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date(); today.setHours(0, 0, 0, 0);

        let html = headerHtml(`
            <button type="button" class="date-calendar__label" data-view="months">${monthNames[month]}</button>
            <button type="button" class="date-calendar__label" data-view="years">${year}</button>
        `);
        html += `<div class="date-calendar__weekdays">${['S','M','T','W','T','F','S'].map(d => `<span>${d}</span>`).join('')}</div>`;
        html += `<div class="date-calendar__days">`;
        for (let i = 0; i < startWeekday; i++) html += `<span class="date-calendar__day is-empty"></span>`;
        for (let day = 1; day <= daysInMonth; day++) {
            const thisDate = new Date(year, month, day);
            const isPast = thisDate < today;
            const isSelected = selectedDate && thisDate.getTime() === selectedDate.getTime();
            const cls = ['date-calendar__day'];
            if (isPast) cls.push('is-disabled');
            if (isSelected) cls.push('is-selected');
            html += `<button type="button" class="${cls.join(' ')}" ${isPast ? 'disabled' : ''} data-day="${day}">${day}</button>`;
        }
        html += `</div>`;
        calendarEl.innerHTML = html;

        calendarEl.querySelectorAll('[data-nav]').forEach(btn => btn.addEventListener('click', function () {
            viewDate.setMonth(viewDate.getMonth() + parseInt(btn.dataset.nav, 10));
            render();
        }));
        calendarEl.querySelectorAll('[data-view]').forEach(btn => btn.addEventListener('click', function () {
            view = btn.dataset.view;
            if (view === 'years') yearRangeStart = Math.floor(viewDate.getFullYear() / 12) * 12;
            render();
        }));
        calendarEl.querySelectorAll('.date-calendar__day:not(.is-empty):not(.is-disabled)').forEach(btn => btn.addEventListener('click', function () {
            selectedDate = new Date(year, month, parseInt(btn.dataset.day, 10));
            display.textContent = formatDisplay(selectedDate);
            display.classList.add('has-value');
            hiddenInput.value = formatISO(selectedDate);
            field.classList.remove('is-open');
            view = 'days';
            render();
        }));
    }

    function renderMonths() {
        const year = viewDate.getFullYear();
        let html = headerHtml(`<span class="date-calendar__label date-calendar__label--static">${year}</span>`);
        html += `<div class="date-calendar__grid date-calendar__grid--months">`;
        monthShort.forEach((m, idx) => {
            const isCurrent = idx === viewDate.getMonth();
            html += `<button type="button" class="date-calendar__cell ${isCurrent ? 'is-selected' : ''}" data-month="${idx}">${m}</button>`;
        });
        html += `</div>`;
        calendarEl.innerHTML = html;

        calendarEl.querySelectorAll('[data-nav]').forEach(btn => btn.addEventListener('click', function () {
            viewDate.setFullYear(viewDate.getFullYear() + parseInt(btn.dataset.nav, 10));
            render();
        }));
        calendarEl.querySelectorAll('[data-month]').forEach(btn => btn.addEventListener('click', function () {
            viewDate.setMonth(parseInt(btn.dataset.month, 10));
            view = 'days';
            render();
        }));
    }

    function renderYears() {
        let html = headerHtml(`<span class="date-calendar__label date-calendar__label--static">${yearRangeStart} – ${yearRangeStart + 11}</span>`);
        html += `<div class="date-calendar__grid date-calendar__grid--years">`;
        for (let y = yearRangeStart; y < yearRangeStart + 12; y++) {
            const isCurrent = y === viewDate.getFullYear();
            html += `<button type="button" class="date-calendar__cell ${isCurrent ? 'is-selected' : ''}" data-year="${y}">${y}</button>`;
        }
        html += `</div>`;
        calendarEl.innerHTML = html;

        calendarEl.querySelectorAll('[data-nav]').forEach(btn => btn.addEventListener('click', function () {
            yearRangeStart += parseInt(btn.dataset.nav, 10) * 12;
            render();
        }));
        calendarEl.querySelectorAll('[data-year]').forEach(btn => btn.addEventListener('click', function () {
            viewDate.setFullYear(parseInt(btn.dataset.year, 10));
            view = 'days';
            render();
        }));
    }

    // Fixes bug 2: stops any click inside the calendar from bubbling to the
    // global outside-click dropdown-closer. Attached once — calendarEl
    // itself persists across re-renders, only its innerHTML is replaced.
    calendarEl.addEventListener('click', e => e.stopPropagation());

    // Reset hook — clears the picked date and restores the placeholder display.
    field.datepickerReset = function () {
        selectedDate = null;
        viewDate = new Date();
        viewDate.setDate(1);
        view = 'days';
        display.textContent = 'mm/dd/yyyy';
        display.classList.remove('has-value');
        hiddenInput.value = '';
        field.classList.remove('is-open');
    };

    trigger.addEventListener('click', function () {
        view = 'days';
        render();
    });
});