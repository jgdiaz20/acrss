<?php $__env->startSection('content'); ?>

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
        <?php echo e(trans('global.edit')); ?> <?php echo e(trans('cruds.lesson.title_singular')); ?>

    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo e(route("admin.lessons.update", [$lesson->id])); ?>" enctype="multipart/form-data">
            <?php echo method_field('PUT'); ?>
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label class="required" for="class_id"><?php echo e(trans('cruds.lesson.fields.class')); ?></label>
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
                <span class="help-block">Select the room for this lesson</span>
            </div>
            <div class="form-group">
                <label class="required" for="weekday"><?php echo e(trans('cruds.lesson.fields.weekday')); ?></label>
                <select class="form-control <?php echo e($errors->has('weekday') ? 'is-invalid' : ''); ?>" name="weekday" id="weekday" required>
                    <option value="">Select Day</option>
                    <?php $__currentLoopData = \App\Lesson::WEEK_DAYS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" data-is-weekend="<?php echo e(in_array($key, [6, 7]) ? 'true' : 'false'); ?>" <?php echo e((old('weekday', $lesson->weekday) == $key) ? 'selected' : ''); ?>><?php echo e($day); ?></option>
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
                <input class="form-control lesson-timepicker <?php echo e($errors->has('end_time') ? 'is-invalid' : ''); ?>" type="text" name="end_time" id="end_time" value="<?php echo e(old('end_time', $lesson->end_time)); ?>" required>
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
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
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
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/lessons/edit.blade.php ENDPATH**/ ?>