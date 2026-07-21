document.querySelectorAll('[data-datepicker]').forEach(function (field) {
    const trigger = field.querySelector('[data-date-trigger]');
    const display = field.querySelector('[data-date-display]');
    const hiddenInput = field.querySelector('[data-date-value]');
    const calendarEl = field.querySelector('[data-date-calendar]');

    let viewDate = new Date();
    viewDate.setDate(1);
    let selectedDate = null;

    function formatDisplay(d) {
        return `${String(d.getMonth() + 1).padStart(2, '0')}/${String(d.getDate()).padStart(2, '0')}/${d.getFullYear()}`;
    }
    function formatISO(d) {
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }

    function renderCalendar() {
        const year = viewDate.getFullYear();
        const month = viewDate.getMonth();
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const startWeekday = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date(); today.setHours(0, 0, 0, 0);

        let html = `
            <div class="date-calendar__header">
                <button type="button" class="date-calendar__nav" data-nav="-1">&lsaquo;</button>
                <span class="date-calendar__month">${monthNames[month]} ${year}</span>
                <button type="button" class="date-calendar__nav" data-nav="1">&rsaquo;</button>
            </div>
            <div class="date-calendar__weekdays">${['S','M','T','W','T','F','S'].map(d => `<span>${d}</span>`).join('')}</div>
            <div class="date-calendar__days">`;

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
            renderCalendar();
        }));

        calendarEl.querySelectorAll('.date-calendar__day:not(.is-empty):not(.is-disabled)').forEach(btn => btn.addEventListener('click', function () {
            selectedDate = new Date(year, month, parseInt(btn.dataset.day, 10));
            display.textContent = formatDisplay(selectedDate);
            display.classList.add('has-value');
            hiddenInput.value = formatISO(selectedDate);
            field.classList.remove('is-open');
            renderCalendar();
        }));
    }

    trigger.addEventListener('click', renderCalendar); // fresh render every time it opens
});