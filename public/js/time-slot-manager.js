class TimeSlotManager {
    constructor(options = {}) {
        this.options = {
            startSelector: '#start_time',
            endSelector: '#end_time',
            feedbackSelector: '#time-feedback',
            schoolHours: {
                start: '07:00',
                end: '18:00',
                interval: 30
            },
            ...options
        };
        
        this.initialize();
    }

    initialize() {
        this.startEl = document.querySelector(this.options.startSelector);
        this.endEl = document.querySelector(this.options.endSelector);
        this.feedbackEl = document.querySelector(this.options.feedbackSelector);
        
        if (this.startEl && this.endEl) {
            this.populateTimeSlots();
            this.initializeValidation();
        }
    }

    generateTimeSlots() {
        const slots = [];
        let current = moment(this.options.schoolHours.start, 'HH:mm');
        const end = moment(this.options.schoolHours.end, 'HH:mm');
        
        while (current < end) {
            slots.push({
                value: current.format('HH:mm'),
                label: current.format('h:mm A')
            });
            current.add(this.options.schoolHours.interval, 'minutes');
        }
        return slots;
    }

    populateTimeSlots() {
        const timeSlots = this.generateTimeSlots();
        
        [this.startEl, this.endEl].forEach(select => {
            select.innerHTML = '<option value="">Select Time</option>';
            timeSlots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.value;
                option.textContent = slot.label;
                select.appendChild(option);
            });
        });
    }

    initializeValidation() {
        [this.startEl, this.endEl].forEach(el => {
            el.addEventListener('change', () => this.validateTimeSlot());
        });
    }

    validateTimeSlot() {
        const startTime = this.startEl.value;
        const endTime = this.endEl.value;

        if (!startTime || !endTime) return;

        const start = moment(startTime, 'HH:mm');
        const end = moment(endTime, 'HH:mm');

        if (end <= start) {
            this.showFeedback('End time must be after start time', 'error');
            return false;
        }

        const duration = moment.duration(end.diff(start));
        const minutes = duration.asMinutes();

        if (minutes < 30) {
            this.showFeedback('Lesson must be at least 30 minutes long', 'error');
            return false;
        }

        if (minutes > 180) {
            this.showFeedback('Lesson cannot be longer than 3 hours', 'error');
            return false;
        }

        this.showFeedback('Valid time slot selected', 'success');
        return true;
    }

    showFeedback(message, type) {
        if (this.feedbackEl) {
            this.feedbackEl.textContent = message;
            this.feedbackEl.className = `time-feedback ${type}`;
        }
    }
}