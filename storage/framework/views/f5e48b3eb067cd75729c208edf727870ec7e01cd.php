<?php $__env->startSection('content'); ?>


<?php if($errors->any()): ?>
    <div class="alert alert-danger mb-3">
        <i class="fas fa-exclamation-circle"></i>
        <strong>Unable to update schedule</strong>
        <p class="mb-0 mt-2">Please correct the errors highlighted below.</p>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <?php echo e(trans('global.edit')); ?> <?php echo e(trans('cruds.lesson.title_singular')); ?>

    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo e(route("admin.lessons.update", [$lesson->id])); ?>" enctype="multipart/form-data">
            <?php echo method_field('PUT'); ?>
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label class="required" for="class_id"><?php echo e(trans('cruds.lesson.fields.section')); ?></label>
                <select class="form-control select2 <?php echo e($errors->has('class') ? 'is-invalid' : ''); ?>" name="class_id" id="class_id" required>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php echo e(($lesson->class ? $lesson->class->id : old('class_id')) == $id ? 'selected' : ''); ?>><?php echo e($class); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('class')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('class')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block"><?php echo e(trans('cruds.lesson.fields.class_helper')); ?></span>
            </div>
            <div class="form-group">
                <label class="required" for="subject_id">Subject</label>
                <select class="form-control select2 <?php echo e($errors->has('subject_id') ? 'is-invalid' : ''); ?>" name="subject_id" id="subject_id" required>
                    <option value="">-- Select Subject --</option>
                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php echo e(($lesson->subject ? $lesson->subject->id : old('subject_id')) == $id ? 'selected' : ''); ?>><?php echo e($subject); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('subject_id')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('subject_id')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Select the subject for this class schedule</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="teacher_id"><?php echo e(trans('cruds.lesson.fields.teacher')); ?></label>
                <select class="form-control select2 <?php echo e($errors->has('teacher') ? 'is-invalid' : ''); ?>" name="teacher_id" id="teacher_id" required>
                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php echo e(($lesson->teacher ? $lesson->teacher->id : old('teacher_id')) == $id ? 'selected' : ''); ?>><?php echo e($teacher); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('teacher')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('teacher')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block"><?php echo e(trans('cruds.lesson.fields.teacher_helper')); ?></span>
            </div>
            <div class="form-group">
                <label class="required" for="room_id">Room</label>
                <select class="form-control select2 <?php echo e($errors->has('room') ? 'is-invalid' : ''); ?>" name="room_id" id="room_id" required>
                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php echo e(($lesson->room ? $lesson->room->id : old('room_id')) == $id ? 'selected' : ''); ?>><?php echo e($room); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('room')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('room')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Select the room for this class schedule</span>
            </div>
            <div class="form-group">
                <label class="required" for="lesson_type"> Schedule Type</label>
                <select class="form-control <?php echo e($errors->has('lesson_type') ? 'is-invalid' : ''); ?>" name="lesson_type" id="lesson_type" required>
                    <option value="">-- Select Type --</option>
                    <?php $__currentLoopData = \App\Lesson::LESSON_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('lesson_type', $lesson->lesson_type) == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('lesson_type')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('lesson_type')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block" id="lesson-type-help">Select whether this is a lecture or laboratory session</span>
            </div>
            
            
            <div id="hours-tracking-container" style="display: none;" class="mb-3">
                <div class="card border-info">
                    <div class="card-header bg-info text-white py-2">
                        <h6 class="mb-0"><i class="fas fa-clock mr-2"></i>Hours Tracking</h6>
                    </div>
                    <div class="card-body p-3">
                        <div id="hours-tracking-content">
                            <div id="lecture-hours-section" class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="font-weight-bold">Lecture Hours:</span>
                                    <span id="lecture-hours-text" class="badge badge-secondary">0h / 0h</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div id="lecture-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <span id="lecture-progress-text">0%</span>
                                    </div>
                                </div>
                                <small id="lecture-remaining-text" class="text-muted">0h remaining</small>
                            </div>
                            
                            <div id="lab-hours-section" class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="font-weight-bold">Lab Hours:</span>
                                    <span id="lab-hours-text" class="badge badge-secondary">0h / 0h</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div id="lab-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <span id="lab-progress-text">0%</span>
                                    </div>
                                </div>
                                <small id="lab-remaining-text" class="text-muted">0h remaining</small>
                            </div>
                            
                            <div id="hours-error-message" class="alert alert-danger mt-2" style="display: none;">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span id="hours-error-text"></span>
                            </div>
                            
                            <div id="hours-info-message" class="alert alert-info mt-2" style="display: none;">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span id="hours-info-text"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="required" for="weekday"><?php echo e(trans('cruds.lesson.fields.weekday')); ?></label>
                <select class="form-control <?php echo e($errors->has('weekday') ? 'is-invalid' : ''); ?>" name="weekday" id="weekday" required>
                    <option value="">Select Day</option>
                    <?php $__currentLoopData = \App\Lesson::WEEK_DAYS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e((old('weekday', $lesson->weekday) == $key) ? 'selected' : ''); ?>><?php echo e($day); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('weekday')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('weekday')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block" id="weekday-help"><?php echo e(trans('cruds.lesson.fields.weekday_helper')); ?></span>
            </div>
            <div class="form-group">
                <label class="required" for="start_time"><?php echo e(trans('cruds.lesson.fields.start_time')); ?></label>
                <input class="form-control lesson-timepicker <?php echo e($errors->has('start_time') ? 'is-invalid' : ''); ?>" type="text" name="start_time" id="start_time" value="<?php echo e(old('start_time', $lesson->start_time)); ?>" required>
                <?php if($errors->has('start_time')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('start_time')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block"><?php echo e(trans('cruds.lesson.fields.start_time_helper')); ?></span>
            </div>
            <div class="form-group">
                <label class="required" for="end_time"><?php echo e(trans('cruds.lesson.fields.end_time')); ?></label>
                <input class="form-control lesson-timepicker <?php echo e($errors->has('end_time') || $errors->has('duration_hours') ? 'is-invalid' : ''); ?>" type="text" name="end_time" id="end_time" value="<?php echo e(old('end_time', $lesson->end_time)); ?>" required>
                <?php if($errors->has('end_time')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('end_time')); ?>

                    </div>
                <?php endif; ?>
                <?php if($errors->has('duration_hours')): ?>
                    <div class="invalid-feedback" style="display: block;">
                        <?php echo e($errors->first('duration_hours')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block"><?php echo e(trans('cruds.lesson.fields.end_time_helper')); ?></span>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> <?php echo e(trans('global.save')); ?>

                </button>
                <a href="<?php echo e(route('admin.lessons.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    // No weekend filtering needed - all program types can schedule weekend classes
    function updateWeekdayOptions(programType) {
        // Function kept for compatibility but no filtering applied
    }
    
    // Fetch program type when class is selected
    $('#class_id').on('change', function() {
        const classId = $(this).val();
        
        if (classId) {
            $.get('<?php echo e(route("admin.school-classes.program-type", ":id")); ?>'.replace(':id', classId), function(data) {
                updateWeekdayOptions(data.program_type);
            });
        } else {
            // Reset to show only weekdays if no class selected
            updateWeekdayOptions('');
        }
    });
    
    // Trigger on page load if class is already selected
    if ($('#class_id').val()) {
        $('#class_id').trigger('change');
    }
    
    // Dynamic filtering based on subject selection
    $('#subject_id').on('change', function() {
        var subjectId = $(this).val();
        var currentTeacherId = $('#teacher_id').val(); // Preserve current teacher selection
        
        if (subjectId) {
            // Update teachers based on subject
            $.get('<?php echo e(route("admin.lessons.get-teachers-for-subject")); ?>', {
                subject_id: subjectId
            }, function(data) {
                var teacherSelect = $('#teacher_id');
                var currentValue = teacherSelect.val(); // Get current value before clearing
                teacherSelect.empty();
                teacherSelect.append('<option value="">Select Teacher</option>');
                
                $.each(data.teachers, function(id, name) {
                    var selected = (id == currentValue) ? ' selected' : '';
                    teacherSelect.append('<option value="' + id + '"' + selected + '>' + name + '</option>');
                });
                
                // Only trigger change if value actually changed
                if (teacherSelect.val() !== currentValue) {
                    teacherSelect.trigger('change');
                }
            });
            
            // Update rooms based on subject requirements
            $.get('<?php echo e(route("admin.lessons.get-rooms-for-subject")); ?>', {
                subject_id: subjectId
            }, function(data) {
                var roomSelect = $('#room_id');
                var currentRoomValue = roomSelect.val(); // Preserve current room selection
                roomSelect.empty();
                roomSelect.append('<option value="">Select Room</option>');
                
                $.each(data.rooms, function(id, name) {
                    var selected = (id == currentRoomValue) ? ' selected' : '';
                    roomSelect.append('<option value="' + id + '"' + selected + '>' + name + '</option>');
                });
            });
            
            // Fetch subject data for lesson type auto-select/disable
            $.get('<?php echo e(route("admin.lessons.hours-tracking")); ?>', {
                subject_id: subjectId,
                class_id: $('#class_id').val() || 0,
                exclude_lesson_id: <?php echo e($lesson->id); ?>

            }, function(response) {
                if (response && response.success) {
                    subjectData = response;
                    updateLessonTypeField(); // This triggers change event which calls updateLessonTypeHelp()
                }
            }).fail(function() {
                console.log('Could not fetch subject data');
            });
        } else {
            // Reset to all options if no subject selected
            $.get('<?php echo e(route("admin.lessons.get-teachers-for-subject")); ?>', {}, function(data) {
                var teacherSelect = $('#teacher_id');
                var currentValue = teacherSelect.val();
                teacherSelect.empty();
                teacherSelect.append('<option value="">Select Teacher</option>');
                
                $.each(data.teachers, function(id, name) {
                    var selected = (id == currentValue) ? ' selected' : '';
                    teacherSelect.append('<option value="' + id + '"' + selected + '>' + name + '</option>');
                });
            });
            
            $.get('<?php echo e(route("admin.lessons.get-rooms-for-subject")); ?>', {}, function(data) {
                var roomSelect = $('#room_id');
                var currentValue = roomSelect.val();
                roomSelect.empty();
                roomSelect.append('<option value="">Select Room</option>');
                
                $.each(data.rooms, function(id, name) {
                    var selected = (id == currentValue) ? ' selected' : '';
                    roomSelect.append('<option value="' + id + '"' + selected + '>' + name + '</option>');
                });
            });
        }
    });
    
    // Trigger change on page load if subject is already selected
    <?php if(old('subject_id') || $lesson->subject_id): ?>
        $('#subject_id').trigger('change');
    <?php endif; ?>
    
    // ===== CREDIT SYSTEM ENHANCEMENTS =====
    let subjectData = {};
    let hoursTrackingData = null;
    
    // Track if end_time was manually entered vs auto-filled
    // In edit mode, start with true since existing data is considered "manually set"
    let endTimeManuallyEntered = true;
    
    // Handle lesson type selection
    $('#lesson_type').on('change', function() {
        updateLessonTypeHelp();
        // Always try to suggest duration when lesson type changes
        suggestDuration();
    });
    
    // Handle time changes for duration calculation and auto-suggestion
    $('#start_time, #end_time').on('change', function() {
        calculateDuration();
        // Trigger duration suggestion when start_time changes
        if ($(this).attr('id') === 'start_time') {
            suggestDuration();
        }
    });
    
    // Handle datetimepicker change events (dp.change is triggered by the picker widget)
    $('#start_time').on('dp.change', function() {
        console.log('Start time changed via datetimepicker');
        suggestDuration();
        calculateDuration();
    });
    
    $('#end_time').on('dp.change', function() {
        console.log('End time changed via datetimepicker');
        calculateDuration();
    });
    
    // Track manual changes to end_time
    $('#end_time').on('input', function() {
        // Mark as manually entered if user types directly
        endTimeManuallyEntered = true;
        console.log('End time marked as manually entered');
    });
    
    // Hours tracking display removed - validation still works server-side
    
    function updateLessonTypeField() {
        const $lessonTypeField = $('#lesson_type');
        const subjectId = $('#subject_id').val();
        
        if (!subjectId || !subjectData.scheduling_mode) {
            $lessonTypeField.prop('disabled', false);
            return;
        }
        
        // Lab mode: Auto-select laboratory and disable
        if (subjectData.scheduling_mode === 'lab') {
            $lessonTypeField.val('laboratory');
            $lessonTypeField.prop('disabled', true);
            console.log('Lab mode: Auto-selected laboratory, field disabled');
        }
        // Lecture mode: Auto-select lecture and disable
        else if (subjectData.scheduling_mode === 'lecture') {
            $lessonTypeField.val('lecture');
            $lessonTypeField.prop('disabled', true);
            console.log('Lecture mode: Auto-selected lecture, field disabled');
        }
        // Flexible mode: Enable field for user selection
        else if (subjectData.scheduling_mode === 'flexible') {
            $lessonTypeField.prop('disabled', false);
            console.log('Flexible mode: Field enabled for user selection');
        }
        
        $lessonTypeField.trigger('change');
    }
    
    function updateLessonTypeHelp() {
        const lessonType = $('#lesson_type').val();
        let helpText = 'Select whether this is a lecture or laboratory session';
        
        if (lessonType === 'laboratory') {
            helpText = '<strong>Laboratory:</strong> Duration between 3 to 5 hours. Default 3-hour session is advised.';
        } else if (lessonType === 'lecture') {
            helpText = '<strong>Lecture:</strong> Duration between 1 to 3 hours (30-minute intervals). Default 1-hour session is advised.';
        }
        
        if (subjectData.scheduling_mode === 'lab') {
            helpText += '<br><span class="text-info">This subject is in Pure Laboratory. Schedule type is automatically set.</span>';
        } else if (subjectData.scheduling_mode === 'lecture') {
            helpText += '<br><span class="text-info">This subject is Pure Lecture. Schedule type is automatically set.</span>';
        }
        
        $('#lesson-type-help').html(helpText);
    }
    
    function suggestDuration() {
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
        if (currentEndTime && endTimeManuallyEntered) {
            console.log('Duration suggestion skipped: end_time was manually entered by user');
            return;
        }
        
        // Get remaining hours from hours tracking data
        let remainingHours = null;
        if (hoursTrackingData) {
            if (lessonType === 'lecture') {
                remainingHours = hoursTrackingData.lecture_hours.remaining;
            } else if (lessonType === 'laboratory') {
                remainingHours = hoursTrackingData.lab_hours.remaining;
            }
        }
        
        // If no hours remaining, don't auto-fill
        if (remainingHours !== null && remainingHours === 0) {
            console.log('Duration suggestion skipped: No remaining hours for this lesson type');
            $('#end_time').val('');
            updateHoursTrackingDisplay();
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
        
        // Calculate end time
        const start = moment(startTime, 'h:mm A');
        const suggestedEnd = start.clone().add(suggestedDuration, 'hours');
        $('#end_time').val(suggestedEnd.format('h:mm A'));
        endTimeManuallyEntered = false; // Mark as auto-filled
        
        const typeLabel = lessonType === 'laboratory' ? 'Laboratory' : 'Lecture';
        console.log(`${typeLabel}: Auto-suggested ${suggestedDuration}-hour duration (start + ${suggestedDuration}h)`);
        
        // Update hours tracking display
        updateHoursTrackingDisplay();
    }
    
    function calculateDuration() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        
        if (startTime && endTime) {
            const start = moment(startTime, 'h:mm A');
            const end = moment(endTime, 'h:mm A');
            const duration = end.diff(start, 'hours', true);
            
            // Update hours tracking display with current duration
            updateHoursTrackingDisplay();
            
            // Server-side validation will handle strict enforcement
        }
    }
    
    // ===== HOURS TRACKING FUNCTIONS =====
    
    /**
     * Fetch hours tracking data from server
     */
    function fetchHoursTracking() {
        const classId = $('#class_id').val();
        const subjectId = $('#subject_id').val();
        
        if (!classId || !subjectId) {
            $('#hours-tracking-container').hide();
            hoursTrackingData = null;
            return;
        }
        
        console.log('Fetching hours tracking for class:', classId, 'subject:', subjectId);
        
        $.ajax({
            url: '<?php echo e(route("admin.lessons.hours-tracking")); ?>',
            method: 'GET',
            data: {
                class_id: classId,
                subject_id: subjectId,
                exclude_lesson_id: <?php echo e($lesson->id); ?> // Exclude current lesson in edit mode
            },
            success: function(response) {
                if (response.success) {
                    hoursTrackingData = response;
                    console.log('Hours tracking data received:', hoursTrackingData);
                    updateHoursTrackingDisplay();
                    $('#hours-tracking-container').show();
                } else {
                    console.error('Hours tracking error:', response.error);
                    $('#hours-tracking-container').hide();
                    hoursTrackingData = null;
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch hours tracking:', error);
                $('#hours-tracking-container').hide();
                hoursTrackingData = null;
            }
        });
    }
    
    /**
     * Update hours tracking display with current data
     */
    function updateHoursTrackingDisplay() {
        if (!hoursTrackingData) {
            return;
        }
        
        const lectureData = hoursTrackingData.lecture_hours;
        const labData = hoursTrackingData.lab_hours;
        const schedulingMode = hoursTrackingData.scheduling_mode;
        
        // Conditionally show/hide sections based on scheduling mode
        if (schedulingMode === 'lab') {
            // Pure lab: Show only lab hours
            $('#lecture-hours-section').hide();
            $('#lab-hours-section').show();
        } else if (schedulingMode === 'lecture') {
            // Pure lecture: Show only lecture hours
            $('#lecture-hours-section').show();
            $('#lab-hours-section').hide();
        } else {
            // Flexible: Show both
            $('#lecture-hours-section').show();
            $('#lab-hours-section').show();
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
            updateProgressBar('lecture', lectureData, currentDuration, lessonType === 'lecture');
        }
        
        // Update lab hours display (if visible)
        if (schedulingMode === 'lab' || schedulingMode === 'flexible') {
            updateProgressBar('lab', labData, currentDuration, lessonType === 'laboratory');
        }
        
        // Check for errors and update submit button
        validateHoursAndUpdateSubmit(currentDuration, lessonType);
    }
    
    /**
     * Update individual progress bar
     */
    function updateProgressBar(type, data, currentDuration, isCurrentType) {
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
    function validateHoursAndUpdateSubmit(currentDuration, lessonType) {
        if (!hoursTrackingData || !lessonType || currentDuration <= 0) {
            $('#hours-error-message').hide();
            $('#hours-info-message').hide();
            return;
        }
        
        const data = lessonType === 'lecture' ? hoursTrackingData.lecture_hours : hoursTrackingData.lab_hours;
        const remaining = data.remaining;
        const total = data.total;
        const scheduled = data.scheduled;
        
        const $submitBtn = $('button[type="submit"]');
        
        // Check if duration exceeds remaining hours
        if (currentDuration > remaining) {
            const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
            const errorMsg = `This lesson (${currentDuration.toFixed(1)}h) exceeds remaining ${typeLabel.toLowerCase()} hours (${remaining.toFixed(1)}h). Scheduled: ${scheduled.toFixed(1)}h / Total: ${total}h`;
            
            $('#hours-error-text').text(errorMsg);
            $('#hours-error-message').show();
            $('#hours-info-message').hide();
            
            // Disable submit button
            $submitBtn.prop('disabled', true);
            console.log('Submit disabled: Duration exceeds remaining hours');
        } else if (remaining === 0) {
            const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
            const errorMsg = `No remaining ${typeLabel.toLowerCase()} hours for this class. All ${total}h have been scheduled.`;
            
            $('#hours-error-text').text(errorMsg);
            $('#hours-error-message').show();
            $('#hours-info-message').hide();
            
            // Disable submit button
            $submitBtn.prop('disabled', true);
            console.log('Submit disabled: No remaining hours');
        } else {
            // Valid duration
            $('#hours-error-message').hide();
            
            // Show info message about hours usage
            const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
            const newRemaining = remaining - currentDuration;
            const infoMsg = `This lesson will use ${currentDuration.toFixed(1)}h (${newRemaining.toFixed(1)}h ${typeLabel.toLowerCase()} hours remaining after)`;
            
            $('#hours-info-text').text(infoMsg);
            $('#hours-info-message').show();
            
            // Re-enable submit button (if no other errors)
            $submitBtn.prop('disabled', false);
            console.log('Submit enabled: Valid duration within remaining hours');
        }
    }
    
    // Fetch hours tracking when class or subject changes
    $('#class_id, #subject_id').on('change', function() {
        fetchHoursTracking();
    });
    
    // Update display when end_time changes
    $('#end_time').on('change dp.change', function() {
        updateHoursTrackingDisplay();
    });
    
    // Initial fetch if both class and subject are selected
    if ($('#class_id').val() && $('#subject_id').val()) {
        fetchHoursTracking();
    }
    
    // Enable lesson_type field before form submission so value is sent
    $('form').on('submit', function() {
        $('#lesson_type').prop('disabled', false);
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/lessons/edit.blade.php ENDPATH**/ ?>