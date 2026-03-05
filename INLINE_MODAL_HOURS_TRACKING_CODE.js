/**
 * Hours Tracking Functions for Inline Editing Modal
 * Add these methods to the InlineEditingSystem class in inline-editing.js
 */

// ===== UPDATE suggestDuration() METHOD =====
// Replace the existing suggestDuration() method with this enhanced version:

suggestDuration() {
    const lessonType = $('#lesson_type').val();
    const startTime = $('#start_time').val();
    const currentEndTime = $('#end_time').val();

    // ENHANCED BEHAVIOR WITH INTELLIGENT CAPPING:
    // 1. Only suggest if start_time is populated
    // 2. Recalculate if end_time was auto-filled (not manually entered)
    // 3. Don't override manually entered end_time
    // 4. Cap duration based on remaining hours
    // 5. Don't auto-fill if no hours remaining
    
    if (!startTime) {
        console.log('Duration suggestion skipped: start_time not populated');
        return;
    }
    
    if (!lessonType) {
        console.log('Duration suggestion skipped: lesson_type not selected');
        return;
    }
    
    // Check if end_time was manually entered
    if (currentEndTime && this.endTimeManuallyEntered) {
        console.log('Duration suggestion skipped: end_time was manually entered by user');
        return;
    }
    
    // Get remaining hours from hours tracking data
    let remainingHours = null;
    if (this.hoursTrackingData) {
        if (lessonType === 'lecture') {
            remainingHours = this.hoursTrackingData.lecture_hours.remaining;
        } else if (lessonType === 'laboratory') {
            remainingHours = this.hoursTrackingData.lab_hours.remaining;
        }
    }
    
    // If no hours remaining, don't auto-fill
    if (remainingHours !== null && remainingHours === 0) {
        console.log('Duration suggestion skipped: No remaining hours for this lesson type');
        $('#end_time').val('');
        this.updateHoursTrackingDisplay();
        return;
    }
    
    // Determine default duration
    let defaultDuration = lessonType === 'laboratory' ? 3 : 1;
    
    // Apply intelligent capping
    let suggestedDuration = defaultDuration;
    if (remainingHours !== null && remainingHours < defaultDuration) {
        // Cap to remaining hours
        suggestedDuration = remainingHours;
        console.log(`Duration capped to remaining hours: ${suggestedDuration}h (default was ${defaultDuration}h)`);
    }
    
    try {
        // Calculate end time
        const start = moment(startTime, 'h:mm A');
        const suggestedEnd = start.clone().add(suggestedDuration, 'hours');
        $('#end_time').val(suggestedEnd.format('h:mm A'));
        this.endTimeManuallyEntered = false; // Mark as auto-filled
        
        const typeLabel = lessonType === 'laboratory' ? 'Laboratory' : 'Lecture';
        console.log(`${typeLabel}: Auto-suggested ${suggestedDuration}-hour duration (start + ${suggestedDuration}h)`);
        
        // Update hours tracking display
        this.updateHoursTrackingDisplay();
        
        // Trigger validation after auto-filling
        this.validateDuration();
    } catch (error) {
        console.error('Duration suggestion error:', error);
    }
}

// ===== ADD NEW METHODS TO InlineEditingSystem CLASS =====

/**
 * Fetch hours tracking data from server
 */
fetchHoursTracking() {
    const classId = $('#class_id').val();
    const subjectId = $('#subject_id').val();
    
    if (!classId || !subjectId) {
        $('#modal-hours-tracking-container').hide();
        this.hoursTrackingData = null;
        return;
    }
    
    console.log('Fetching hours tracking for class:', classId, 'subject:', subjectId);
    
    // Determine exclude_lesson_id for edit mode
    const excludeLessonId = this.currentAction === 'edit' && this.currentData ? this.currentData.id : null;
    
    $.ajax({
        url: '/admin/lessons/hours-tracking',
        method: 'GET',
        data: {
            class_id: classId,
            subject_id: subjectId,
            exclude_lesson_id: excludeLessonId
        },
        success: (response) => {
            if (response.success) {
                this.hoursTrackingData = response;
                console.log('Hours tracking data received:', this.hoursTrackingData);
                this.updateHoursTrackingDisplay();
                $('#modal-hours-tracking-container').show();
            } else {
                console.error('Hours tracking error:', response.error);
                $('#modal-hours-tracking-container').hide();
                this.hoursTrackingData = null;
            }
        },
        error: (xhr, status, error) => {
            console.error('Failed to fetch hours tracking:', error);
            $('#modal-hours-tracking-container').hide();
            this.hoursTrackingData = null;
        }
    });
}

/**
 * Update hours tracking display with current data
 */
updateHoursTrackingDisplay() {
    if (!this.hoursTrackingData) {
        return;
    }
    
    const lectureData = this.hoursTrackingData.lecture_hours;
    const labData = this.hoursTrackingData.lab_hours;
    const schedulingMode = this.hoursTrackingData.scheduling_mode;
    
    // Conditionally show/hide sections based on scheduling mode
    if (schedulingMode === 'lab') {
        // Pure lab: Show only lab hours
        $('#modal-lecture-hours-section').hide();
        $('#modal-lab-hours-section').show();
    } else if (schedulingMode === 'lecture') {
        // Pure lecture: Show only lecture hours
        $('#modal-lecture-hours-section').show();
        $('#modal-lab-hours-section').hide();
    } else {
        // Flexible: Show both
        $('#modal-lecture-hours-section').show();
        $('#modal-lab-hours-section').show();
    }
    
    // Calculate current lesson duration
    const startTime = $('#start_time').val();
    const endTime = $('#end_time').val();
    let currentDuration = 0;
    
    if (startTime && endTime) {
        const start = moment(startTime, 'h:mm A');
        const end = moment(endTime, 'h:mm A');
        currentDuration = end.diff(start, 'hours', true);
    }
    
    const lessonType = $('#lesson_type').val();
    
    // Update lecture hours display (if visible)
    if (schedulingMode === 'lecture' || schedulingMode === 'flexible') {
        this.updateProgressBar('modal-lecture', lectureData, currentDuration, lessonType === 'lecture');
    }
    
    // Update lab hours display (if visible)
    if (schedulingMode === 'lab' || schedulingMode === 'flexible') {
        this.updateProgressBar('modal-lab', labData, currentDuration, lessonType === 'laboratory');
    }
    
    // Check for errors and update submit button
    this.validateHoursAndUpdateSubmit(currentDuration, lessonType);
}

/**
 * Update individual progress bar
 */
updateProgressBar(type, data, currentDuration, isCurrentType) {
    const total = data.total;
    const scheduled = data.scheduled;
    const remaining = data.remaining;
    
    // Calculate what the new scheduled would be if this lesson is added
    const projectedScheduled = isCurrentType ? scheduled + currentDuration : scheduled;
    const projectedRemaining = Math.max(0, total - projectedScheduled);
    const progress = total > 0 ? Math.min(100, (projectedScheduled / total) * 100) : 0;
    
    // Update text displays
    $(`#${type}-hours-text`).text(`${projectedScheduled.toFixed(1)}h / ${total}h`);
    $(`#${type}-remaining-text`).text(`${projectedRemaining.toFixed(1)}h remaining`);
    $(`#${type}-progress-text`).text(`${progress.toFixed(0)}%`);
    
    // Update progress bar
    const $progressBar = $(`#${type}-progress-bar`);
    $progressBar.css('width', `${progress}%`);
    $progressBar.attr('aria-valuenow', progress);
    
    // Color coding based on remaining percentage
    $progressBar.removeClass('bg-success bg-warning bg-danger');
    const remainingPercent = total > 0 ? (projectedRemaining / total) * 100 : 0;
    
    if (remainingPercent > 50) {
        $progressBar.addClass('bg-success');
    } else if (remainingPercent >= 20) {
        $progressBar.addClass('bg-warning');
    } else {
        $progressBar.addClass('bg-danger');
    }
    
    console.log(`${type} hours - Total: ${total}, Scheduled: ${scheduled}, Current: ${currentDuration}, Projected: ${projectedScheduled}, Remaining: ${projectedRemaining}`);
}

/**
 * Validate hours and update submit button state
 */
validateHoursAndUpdateSubmit(currentDuration, lessonType) {
    if (!this.hoursTrackingData || !lessonType || currentDuration <= 0) {
        $('#modal-hours-error-message').hide();
        $('#modal-hours-info-message').hide();
        return;
    }
    
    const data = lessonType === 'lecture' ? this.hoursTrackingData.lecture_hours : this.hoursTrackingData.lab_hours;
    const remaining = data.remaining;
    const total = data.total;
    const scheduled = data.scheduled;
    
    const $submitBtn = $('#saveLesson');
    
    // Check if duration exceeds remaining hours
    if (currentDuration > remaining) {
        const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
        const errorMsg = `This lesson (${currentDuration.toFixed(1)}h) exceeds remaining ${typeLabel.toLowerCase()} hours (${remaining.toFixed(1)}h). Scheduled: ${scheduled.toFixed(1)}h / Total: ${total}h`;
        
        $('#modal-hours-error-text').text(errorMsg);
        $('#modal-hours-error-message').show();
        $('#modal-hours-info-message').hide();
        
        // Disable submit button
        $submitBtn.prop('disabled', true);
        console.log('Submit disabled: Duration exceeds remaining hours');
    } else if (remaining === 0) {
        const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
        const errorMsg = `No remaining ${typeLabel.toLowerCase()} hours for this class. All ${total}h have been scheduled.`;
        
        $('#modal-hours-error-text').text(errorMsg);
        $('#modal-hours-error-message').show();
        $('#modal-hours-info-message').hide();
        
        // Disable submit button
        $submitBtn.prop('disabled', true);
        console.log('Submit disabled: No remaining hours');
    } else {
        // Valid duration
        $('#modal-hours-error-message').hide();
        
        // Show info message about hours usage
        const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
        const newRemaining = remaining - currentDuration;
        const infoMsg = `This lesson will use ${currentDuration.toFixed(1)}h (${newRemaining.toFixed(1)}h ${typeLabel.toLowerCase()} hours remaining after)`;
        
        $('#modal-hours-info-text').text(infoMsg);
        $('#modal-hours-info-message').show();
        
        // Re-enable submit button (if no other errors)
        $submitBtn.prop('disabled', false);
        console.log('Submit enabled: Valid duration within remaining hours');
    }
}

// ===== UPDATE showModal() METHOD =====
// Add this code to the showModal() method after populateModal(data):

// Fetch hours tracking after populating modal
if (data.class_id && data.subject_id) {
    this.fetchHoursTracking();
}

// ===== UPDATE attachTimeChangeHandlers() METHOD =====
// Add this code to attachTimeChangeHandlers() method:

// Update hours tracking when end_time changes
$('#end_time').off('change.hoursTracking dp.change.hoursTracking').on('change.hoursTracking dp.change.hoursTracking', () => {
    this.updateHoursTrackingDisplay();
});

// ===== UPDATE MODAL CLOSE HANDLER =====
// Add this to the modal close handler:

$('#lessonModal').on('hidden.bs.modal', () => {
    // ... existing code ...
    this.hoursTrackingData = null;
    $('#modal-hours-tracking-container').hide();
});

// ===== ADD EVENT HANDLERS FOR CLASS/SUBJECT CHANGES =====
// Add these event handlers in the appropriate location:

$('#class_id, #subject_id').off('change.hoursTracking').on('change.hoursTracking', () => {
    this.fetchHoursTracking();
});
