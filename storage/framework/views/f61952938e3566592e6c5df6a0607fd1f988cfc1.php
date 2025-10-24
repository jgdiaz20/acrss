<?php $__env->startSection('content'); ?>

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('admin.home')); ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('admin.lessons.index')); ?>">
                <i class="fas fa-clock"></i> Lessons
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-plus"></i> Create Lesson
        </li>
    </ol>
</nav>

<?php
    // Check if there are scheduling conflict errors
    $hasConflictError = false;
    $conflictMessage = '';
    
    if ($errors->has('start_time')) {
        $errorMessage = $errors->first('start_time');
        if (str_contains($errorMessage, 'Scheduling conflict') || 
            str_contains($errorMessage, 'Conflict with') ||
            str_contains($errorMessage, 'already scheduled')) {
            $hasConflictError = true;
            $conflictMessage = $errorMessage;
        }
    }
?>

<?php if($hasConflictError): ?>
    <div class="alert alert-conflict alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-clock mr-3" style="font-size: 24px;"></i>
            <div>
                <strong style="font-size: 16px;">Scheduling Conflict Detected</strong>
                <p class="mb-0 mt-1"><?php echo e($conflictMessage); ?></p>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-plus mr-2"></i>
            <?php echo e(trans('global.create')); ?> <?php echo e(trans('cruds.lesson.title_singular')); ?>

        </h3>
    </div>

    <div class="card-body">
        <?php
            $prefillWeekday = request()->get('weekday');
            $prefillRoomId = request()->get('room_id');
            $prefillStart = request()->get('start_time');
        ?>
        <?php if($prefillWeekday !== null || $prefillRoomId || $prefillStart): ?>
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <div>
                Prefilled from master timetable
                <?php if($prefillWeekday !== null): ?>
                    • Day: <?php echo e(\App\Lesson::WEEK_DAYS[$prefillWeekday] ?? $prefillWeekday); ?>

                <?php endif; ?>
                <?php if($prefillRoomId): ?>
                    • Room: <?php echo e($rooms[$prefillRoomId] ?? ('ID '.$prefillRoomId)); ?>

                <?php endif; ?>
                <?php if($prefillStart): ?>
                    • Start: <?php echo e($prefillStart); ?>

                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route("admin.lessons.store")); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div id="clientValidationMessages" class="mb-3" style="display:none;"></div>
            <div class="form-group">
                <label class="required" for="class_id"><?php echo e(trans('cruds.lesson.fields.class')); ?></label>
                <select class="form-control select2 <?php echo e($errors->has('class') ? 'is-invalid' : ''); ?>" name="class_id" id="class_id" required>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php echo e(old('class_id') == $id ? 'selected' : ''); ?>><?php echo e($class); ?></option>
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
                        <option value="<?php echo e($id); ?>" <?php echo e(old('subject_id') == $id ? 'selected' : ''); ?>><?php echo e($subject); ?></option>
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
                <select class="form-control select2 <?php echo e($errors->has('teacher_id') ? 'is-invalid' : ''); ?>" name="teacher_id" id="teacher_id" required>
                    <option value="">-- Select Teacher --</option>
                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php echo e(old('teacher_id') == $id ? 'selected' : ''); ?>><?php echo e($teacher); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('teacher_id')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('teacher_id')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Select a teacher for this class schedule</span>
            </div>
            <div class="form-group">
                <label class="required" for="room_id">Room</label>
                <?php $isLockedRoom = request('room_id'); ?>
                <select class="form-control select2 <?php echo e($errors->has('room_id') ? 'is-invalid' : ''); ?>" name="room_id" id="room_id" <?php echo e($isLockedRoom ? 'disabled' : ''); ?> required>
                    <option value="">-- Select Room --</option>
                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php echo e(old('room_id', request('room_id')) == $id ? 'selected' : ''); ?>><?php echo e($room); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($isLockedRoom): ?>
                    <input type="hidden" name="room_id" value="<?php echo e(request('room_id')); ?>">
                    <small class="form-text text-muted">Room locked from master timetable selection.</small>
                <?php endif; ?>
                <?php if($errors->has('room_id')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('room_id')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Select a room for this class schedule</span>
            </div>
            <div class="form-group">
                <label class="required" for="weekday"><?php echo e(trans('cruds.lesson.fields.weekday')); ?></label>
                <select class="form-control <?php echo e($errors->has('weekday') ? 'is-invalid' : ''); ?>" name="weekday" id="weekday" required>
                    <option value="">Select Day</option>
                    <?php $__currentLoopData = \App\Lesson::WEEK_DAYS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" data-is-weekend="<?php echo e(in_array($key, [6, 7]) ? 'true' : 'false'); ?>" <?php echo e((string) old('weekday', request('weekday')) === (string) $key ? 'selected' : ''); ?>><?php echo e($day); ?></option>
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
                <input class="form-control lesson-timepicker <?php echo e($errors->has('start_time') ? 'is-invalid' : ''); ?>" type="text" name="start_time" id="start_time" value="<?php echo e(old('start_time', request('start_time'))); ?>" required>
                <?php if($errors->has('start_time')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('start_time')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block"><?php echo e(trans('cruds.lesson.fields.start_time_helper')); ?></span>
            </div>
            <div class="form-group">
                <label class="required" for="end_time"><?php echo e(trans('cruds.lesson.fields.end_time')); ?></label>
                <input class="form-control lesson-timepicker <?php echo e($errors->has('end_time') ? 'is-invalid' : ''); ?>" type="text" name="end_time" id="end_time" value="<?php echo e(old('end_time')); ?>" required>
                <?php if($errors->has('end_time')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('end_time')); ?>

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
                <a href="<?php echo e(route('admin.room-management.master-timetable.show', request('weekday', 1))); ?>" class="btn btn-light">
                    <i class="fas fa-th"></i> Back to Master Timetable
                </a>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    // When coming from master timetable, the room is locked (prefilled and disabled)
    const IS_ROOM_LOCKED = <?php echo e(request('room_id') ? 'true' : 'false'); ?>;
    
    // Dynamic weekday filtering based on program type
    function updateWeekdayOptions(programType) {
        const weekdaySelect = $('#weekday');
        const currentValue = weekdaySelect.val();
        const isCurrentWeekend = weekdaySelect.find('option:selected').data('is-weekend') === 'true';
        
        // Show/hide weekend options based on program type
        weekdaySelect.find('option').each(function() {
            const isWeekend = $(this).data('is-weekend') === 'true';
            
            if (isWeekend && programType !== 'diploma') {
                $(this).hide();
                // If weekend day is currently selected, clear it
                if ($(this).val() === currentValue) {
                    weekdaySelect.val('');
                }
            } else {
                $(this).show();
            }
        });
        
        // Clear weekend validation error if switching to Diploma with weekend selected
        if (programType === 'diploma' && isCurrentWeekend && currentValue) {
            weekdaySelect.removeClass('is-invalid');
            const $feedback = weekdaySelect.closest('.form-group').find('.invalid-feedback');
            if ($feedback.length) {
                $feedback.text('').hide().remove();
            }
        }
        
        // Also clear error if switching to Diploma (regardless of weekend selection)
        // This handles the case where error exists from previous submission
        if (programType === 'diploma') {
            weekdaySelect.removeClass('is-invalid');
            const $feedback = weekdaySelect.closest('.form-group').find('.invalid-feedback');
            if ($feedback.length && ($feedback.text().includes('Weekend') || $feedback.text().includes('weekend'))) {
                $feedback.text('').hide().remove();
            }
        }
        
        // Update help text
        if (programType === 'diploma') {
            $('#weekday-help').html('<span class="text-success"><i class="fas fa-check-circle"></i> Diploma programs can schedule classes on weekends (Saturday/Sunday)</span>');
        } else {
            $('#weekday-help').html('<?php echo e(trans('cruds.lesson.fields.weekday_helper')); ?>');
        }
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
        
        if (subjectId) {
            // Show loading indicators
            $('#teacher_id').html('<option value="">Loading teachers...</option>');
            if (!IS_ROOM_LOCKED) {
                $('#room_id').html('<option value="">Loading rooms...</option>');
            }
            
            // Update teachers based on subject
            $.get('<?php echo e(route("admin.lessons.get-teachers-for-subject")); ?>', {
                subject_id: subjectId
            }, function(data) {
                var teacherSelect = $('#teacher_id');
                teacherSelect.empty();
                
                // Check if no teachers are assigned to this subject
                if (data.teachers && data.teachers.no_teachers) {
                    teacherSelect.append('<option value="">' + data.teachers.no_teachers + '</option>');
                    teacherSelect.prop('disabled', true);
                    // Show a warning message
                    if ($('#teacher-warning').length === 0) {
                        teacherSelect.after('<div id="teacher-warning" class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle"></i> ' + data.teachers.no_teachers + '</div>');
                    }
                } else {
                    teacherSelect.prop('disabled', false);
                    $('#teacher-warning').remove(); // Remove warning if it exists
                    teacherSelect.append('<option value="">-- Select Teacher --</option>');
                    
                    $.each(data.teachers, function(id, name) {
                        teacherSelect.append('<option value="' + id + '">' + name + '</option>');
                    });
                }
                
                // Trigger change to update room options
                teacherSelect.trigger('change');
            }).fail(function() {
                $('#teacher_id').html('<option value="">Error loading teachers</option>');
                $('#teacher_id').prop('disabled', true);
            });
            
            // Update rooms list (no filtering regardless of subject)
            if (!IS_ROOM_LOCKED) {
                $.get('<?php echo e(route("admin.lessons.get-rooms-for-subject")); ?>', {
                    subject_id: subjectId
                }, function(data) {
                    var roomSelect = $('#room_id');
                    roomSelect.empty();
                    roomSelect.append('<option value="">-- Select Room --</option>');
                    
                    $.each(data.rooms, function(id, name) {
                        roomSelect.append('<option value="' + id + '">' + name + '</option>');
                    });
                }).fail(function() {
                    $('#room_id').html('<option value="">Error loading rooms</option>');
                });
            }
        } else {
            // Reset to all options if no subject selected
            var teacherSelect = $('#teacher_id');
            teacherSelect.empty();
            teacherSelect.prop('disabled', false);
            $('#teacher-warning').remove(); // Remove any warning message
            teacherSelect.append('<option value="">Select Teacher</option>');
            <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($id): ?>
                    teacherSelect.append('<option value="<?php echo e($id); ?>"><?php echo e($teacher); ?></option>');
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            if (!IS_ROOM_LOCKED) {
                var roomSelect = $('#room_id');
                roomSelect.empty();
                roomSelect.append('<option value="">Select Room</option>');
                <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($id): ?>
                        roomSelect.append('<option value="<?php echo e($id); ?>"><?php echo e($room); ?></option>');
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            }
        }
    });
    
    // Initialize with current subject if editing
    <?php if(old('subject_id')): ?>
        $('#subject_id').trigger('change');
    <?php endif; ?>

    // --- Conflict detection (live) ---
    const $form = $('form');
    const $messages = $('#clientValidationMessages');
    const $submitBtn = $form.find('button[type="submit"]');

    function showMessages(type, items) {
        if (!items || items.length === 0) {
            $messages.hide().empty();
            return;
        }
        const icon = (function(){
            if (type === 'error') return 'exclamation-triangle';
            if (type === 'success') return 'check-circle';
            if (type === 'warning') return 'exclamation-triangle';
            return 'info-circle';
        })();
        const klass = (function(){
            if (type === 'error') return 'alert-danger';
            if (type === 'success') return 'alert-success';
            if (type === 'warning') return 'alert-warning';
            return 'alert-info';
        })();
        const listItems = items.map(m => `<li>${m}</li>`).join('');
        $messages
            .removeClass('alert-danger alert-success alert-info alert-warning')
            .addClass(`alert ${klass}`)
            .html(`<i class="fas fa-${icon} mr-2"></i><ul class="mb-0">${listItems}</ul>`) 
            .show();
    }

    let conflictCheckTimer = null;
    function scheduleConflictCheck() {
        clearTimeout(conflictCheckTimer);
        conflictCheckTimer = setTimeout(runConflictCheck, 300);
    }

    function runConflictCheck() {
        const payload = {
            _token: '<?php echo e(csrf_token()); ?>',
            weekday: $('#weekday').val(),
            start_time: $('#start_time').val(),
            end_time: $('#end_time').val(),
            class_id: $('#class_id').val(),
            teacher_id: $('#teacher_id').val(),
            room_id: $('#room_id').val()
        };

        // Only run if required fields present
        if (!payload.weekday || !payload.start_time || !payload.end_time || !payload.class_id || !payload.teacher_id || !payload.room_id) {
            $submitBtn.prop('disabled', false);
            return;
        }

        $.post('<?php echo e(route('admin.lessons.check-conflicts')); ?>', payload)
            .done(function(resp) {
                if (resp && resp.conflicts && resp.conflicts.length > 0) {
                    const msgs = resp.conflicts.map(c => c.message || 'Conflict detected');
                    showMessages('error', msgs);
                    $submitBtn.prop('disabled', true);
                } else {
                    showMessages('success', ['No conflicts detected for the selected time and resources.']);
                    $submitBtn.prop('disabled', false);
                }
            })
            .fail(function() {
                showMessages('info', ['Could not verify conflicts at the moment. You can still try to save.']);
                $submitBtn.prop('disabled', false);
            });
    }

    $('#weekday, #start_time, #end_time, #class_id, #teacher_id, #room_id').on('change keyup', function() {
        // Clear server-side validation errors when user changes fields
        const $field = $(this);
        $field.removeClass('is-invalid');
        $field.siblings('.invalid-feedback').hide();
        
        // Hide the orange conflict banner if visible
        $('.alert-conflict').fadeOut(300);
        
        // Schedule conflict check
        scheduleConflictCheck();
    });

    // --- Room compatibility feedback for selected subject ---
    function checkRoomCompatibility() {
        // Compatibility check removed as all rooms are allowed regardless of subject requirements
        $submitBtn.prop('disabled', false);
    }

    $('#subject_id, #room_id').on('change', function() {
        checkRoomCompatibility();
        scheduleConflictCheck();
    });

    // Initial checks if prefilled; also lock weekday from master timetable if provided
    checkRoomCompatibility();
    scheduleConflictCheck();
    const prefillWeekday = '<?php echo e(request('weekday')); ?>';
    if (prefillWeekday) {
        $('#weekday').prop('disabled', true);
        $('<input>').attr({ type: 'hidden', name: 'weekday', value: prefillWeekday }).appendTo('form');
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/lessons/create.blade.php ENDPATH**/ ?>