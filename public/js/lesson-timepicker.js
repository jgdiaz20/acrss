// Enhanced timepicker functionality
$(document).ready(function() {
    // Prevent browser suggestions
    $('.lesson-timepicker').on('focus', function(e) {
        $(this).attr('autocomplete', 'off');
    });

    // Initialize timepicker with school hours validation
    $('.lesson-timepicker').each(function() {
        $(this).attr('step', '1800'); // 30 minutes in seconds
        $(this).attr('min', '07:00');
        $(this).attr('max', '18:00');
    });

    // Validate time selection
    function validateTimeRange() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();

        if (startTime && endTime) {
            const start = moment(startTime, 'HH:mm');
            const end = moment(endTime, 'HH:mm');
            const duration = moment.duration(end.diff(start));
            const minutes = duration.asMinutes();

            if (end <= start) {
                return 'End time must be after start time';
            }
            if (minutes < 30) {
                return 'Lesson must be at least 30 minutes long';
            }
            if (minutes > 180) {
                return 'Lesson cannot be longer than 3 hours';
            }
        }
        return '';
    }

    // Add validation feedback
    $('.lesson-timepicker').on('change', function() {
        const error = validateTimeRange();
        const feedbackDiv = $('#time-feedback');
        
        if (error) {
            feedbackDiv.text(error).addClass('text-danger').removeClass('text-success');
        } else if ($('#start_time').val() && $('#end_time').val()) {
            feedbackDiv.text('Valid time slot selected').addClass('text-success').removeClass('text-danger');
        }
    });
});