/* Time picker initialization for room timetables */
$(document).ready(function() {
    // Initialize timepicker on modal show
    $('#lessonModal').on('shown.bs.modal', function () {
        initializeTimePickers();
    });
    
    // Also initialize on modal hidden to clean up
    $('#lessonModal').on('hidden.bs.modal', function () {
        destroyTimePickers();
    });

    function initializeTimePickers() {
        // Target only timepickers within the modal to avoid conflicts
        const $modalTimepickers = $('#lessonModal .lesson-timepicker');
        
        // Destroy any existing datetimepicker instances first
        $modalTimepickers.each(function() {
            if ($(this).data('DateTimePicker')) {
                $(this).data('DateTimePicker').destroy();
            }
        });
        
        // Initialize bootstrap-datetimepicker (same as main form)
        if (typeof $.fn.datetimepicker !== 'undefined') {
            $modalTimepickers.each(function() {
                const $input = $(this);
                const hasPrefill = !!$input.val();
                
                $input.datetimepicker({
                    format: 'h:mm A',
                    stepping: 30,
                    minDate: moment().startOf('day').add(7, 'hours'), // 7:00 AM
                    maxDate: moment().startOf('day').add(21, 'hours'), // 9:00 PM
                    useCurrent: false, // do not auto-set current time
                    icons: {
                        up: 'fas fa-chevron-up',
                        down: 'fas fa-chevron-down',
                        previous: 'fas fa-chevron-left',
                        next: 'fas fa-chevron-right'
                    }
                });
                
                // If value exists (e.g., from edit mode), let it display as-is. Otherwise, keep empty.
                if (!hasPrefill) {
                    $input.val('');
                }
                
                // Time validation on change
                $input.on('dp.change', function() {
                    validateTimeSelection();
                });
            });
            
            console.log('Timepickers initialized for modal with time restrictions (7 AM - 9 PM)');
        } else {
            console.error('DateTimePicker plugin not available');
        }
    }
    
    function destroyTimePickers() {
        // Target only timepickers within the modal
        $('#lessonModal .lesson-timepicker').each(function() {
            if ($(this).data('DateTimePicker')) {
                $(this).data('DateTimePicker').destroy();
            }
        });
    }

    function validateTimeSelection() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();

        if (startTime && endTime) {
            // Parse times using the same format as datetimepicker (h:mm A)
            const start = moment(startTime, 'h:mm A');
            const end = moment(endTime, 'h:mm A');
            
            // Validate school hours (7 AM - 9 PM)
            const schoolStart = moment('7:00 AM', 'h:mm A');
            const schoolEnd = moment('9:00 PM', 'h:mm A');
            
            let errorMessage = '';
            
            if (start < schoolStart || start > schoolEnd || 
                end < schoolStart || end > schoolEnd) {
                errorMessage = 'Lessons must be scheduled between 7:00 AM and 9:00 PM';
            } else if (end <= start) {
                errorMessage = 'End time must be after start time';
            } else {
                const duration = moment.duration(end.diff(start));
                const minutes = duration.asMinutes();
                
                if (minutes % 30 !== 0) {
                    errorMessage = 'Lesson times must be in 30-minute intervals';
                } else if (minutes < 30) {
                    errorMessage = 'Lesson must be at least 30 minutes long';
                }
                // Note: Server-side validation handles max duration based on lesson type
                // Laboratory: 3-5 hours, Lecture: 1-3 hours
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