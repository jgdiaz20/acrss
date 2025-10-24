/* Time picker initialization for room timetables */
$(document).ready(function() {
    // Initialize timepicker on modal show
    $('#lessonModal').on('shown.bs.modal', function () {
        initializeTimePickers();
    });

    function initializeTimePickers() {
        $('.lesson-timepicker').each(function() {
            $(this).attr('type', 'time');
            $(this).attr('step', '1800'); // 30 minutes in seconds
            $(this).attr('min', '07:00');
            $(this).attr('max', '21:00');
        });

        // Enable time picker functionality while keeping readonly for manual input prevention
        $('.lesson-timepicker').off('click').on('click', function() {
            $(this).removeAttr('readonly');
            $(this).focus();
            $(this).blur(() => {
                $(this).attr('readonly', 'readonly');
            });
        });

        // Time validation
        $('.lesson-timepicker').on('change', function() {
            validateTimeSelection();
        });
    }

    function validateTimeSelection() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();

        if (startTime && endTime) {
            const start = moment(startTime, 'HH:mm');
            const end = moment(endTime, 'HH:mm');
            
            // Validate school hours (7 AM - 9 PM)
            const schoolStart = moment('07:00', 'HH:mm');
            const schoolEnd = moment('21:00', 'HH:mm');
            
            let errorMessage = '';
            
            if (start < schoolStart || start > schoolEnd || 
                end < schoolStart || end > schoolEnd) {
                errorMessage = 'Lessons must be scheduled between 7 AM and 9 PM';
            } else if (end <= start) {
                errorMessage = 'End time must be after start time';
            } else {
                const duration = moment.duration(end.diff(start));
                const minutes = duration.asMinutes();
                
                if (minutes % 30 !== 0) {
                    errorMessage = 'Lesson times must be in 30-minute intervals';
                } else if (minutes < 30) {
                    errorMessage = 'Lesson must be at least 30 minutes long';
                } else if (minutes > 180) {
                    errorMessage = 'Lesson cannot be longer than 3 hours';
                }
            }

            if (errorMessage) {
                showValidationError(errorMessage);
                return false;
            } else {
                clearValidationError();
                return true;
            }
        }
        return true;
    }

    function showValidationError(message) {
        // Show error in the modal
        const alertHtml = `
            <div class="alert alert-danger mt-2" id="timeValidationError">
                <i class="fas fa-exclamation-circle"></i> ${message}
            </div>`;
        
        $('#timeValidationError').remove(); // Remove any existing error
        $('.modal-body').append(alertHtml);
        
        // Disable save button
        $('#saveLessonButton').prop('disabled', true);
    }

    function clearValidationError() {
        $('#timeValidationError').remove();
        $('#saveLessonButton').prop('disabled', false);
    }

    // Add form submission validation
    $('#lessonForm').on('submit', function(e) {
        if (!validateTimeSelection()) {
            e.preventDefault();
            return false;
        }
    });
});