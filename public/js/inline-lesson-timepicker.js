// Inline editing time picker functionality
$(document).ready(function() {
    // Initialize time pickers when the modal opens
    $('#lessonModal').on('show.bs.modal', function() {
        initializeTimePickers();
    });

    function initializeTimePickers() {
        $('.lesson-timepicker').each(function() {
            // Set initial attributes
            $(this)
                .attr('type', 'time')
                .attr('step', '1800') // 30 minutes
                .attr('min', '07:00')
                .attr('max', '21:00');

            // Remove any existing event listeners
            $(this).off('input change blur');

            // Handle time selection
            $(this).on('input', function(e) {
                const selectedTime = $(this).val();
                if (selectedTime) {
                    // Ensure time is in 30-minute intervals
                    const [hours, minutes] = selectedTime.split(':').map(Number);
                    if (minutes % 30 !== 0) {
                        const roundedMinutes = Math.round(minutes / 30) * 30;
                        const adjustedTime = `${hours.toString().padStart(2, '0')}:${roundedMinutes.toString().padStart(2, '0')}`;
                        $(this).val(adjustedTime);
                    }
                }
                validateTimeSelection();
            });

            // Handle focus/blur
            $(this).on('focus', function() {
                $(this).attr('type', 'time');
            });

            // Add change handler for validation
            $(this).on('change', function() {
                validateTimeSelection();
            });
        });
    }

    function validateTimeSelection() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();

        if (!startTime || !endTime) return true;

        const start = moment(startTime, 'HH:mm');
        const end = moment(endTime, 'HH:mm');
        const schoolStart = moment('07:00', 'HH:mm');
        const schoolEnd = moment('21:00', 'HH:mm');

        let errorMessage = '';

        // Validate school hours
        if (start < schoolStart || start > schoolEnd || end < schoolStart || end > schoolEnd) {
            errorMessage = 'Classes must be scheduled between 7:00 AM and 9:00 PM';
        }
        // Validate time sequence
        else if (end <= start) {
            errorMessage = 'End time must be after start time';
        }
        else {
            // Validate duration
            const duration = moment.duration(end.diff(start));
            const minutes = duration.asMinutes();

            if (minutes % 30 !== 0) {
                errorMessage = 'Class times must be in 30-minute intervals';
            } else if (minutes < 30) {
                errorMessage = 'Classes must be at least 30 minutes long';
            } else if (minutes > 180) {
                errorMessage = 'Classes cannot be longer than 3 hours';
            }
        }

        // Show/hide error message
        const errorContainer = $('#timeValidationError');
        if (errorMessage) {
            if (errorContainer.length === 0) {
                $('<div id="timeValidationError" class="alert alert-danger mt-2">' +
                    '<i class="fas fa-exclamation-circle"></i> ' + errorMessage +
                '</div>').insertAfter('#end_time').closest('.form-group');
            } else {
                errorContainer.html('<i class="fas fa-exclamation-circle"></i> ' + errorMessage);
            }
            $('#saveLessonButton').prop('disabled', true);
            return false;
        } else {
            errorContainer.remove();
            $('#saveLessonButton').prop('disabled', false);
            return true;
        }
    }

    // Add form submission validation
    $('#lessonForm').on('submit', function(e) {
        if (!validateTimeSelection()) {
            e.preventDefault();
            return false;
        }
    });

    // Handle time input formatting
    $('.lesson-timepicker').on('input', function() {
        let value = $(this).val();
        // Only allow numbers and :
        value = value.replace(/[^\d:]/g, '');
        // Format as HH:mm
        if (value.length >= 2 && !value.includes(':')) {
            value = value.substr(0, 2) + ':' + value.substr(2);
        }
        $(this).val(value);
    });
});