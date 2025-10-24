<?php $__env->startSection('content'); ?>

<?php if($academicProgram->type === 'diploma' && $weekendLessonCount > 0): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Weekend Lessons Detected</h5>
        <p class="mb-2">
            <strong><?php echo e($weekendLessonCount); ?></strong> lesson(s) are currently scheduled on weekends (Saturday/Sunday) for this Diploma Program.
        </p>
        <p class="mb-2">
            <strong>Important:</strong> If you change this program type to <strong>Senior High School</strong> or <strong>College</strong>, 
            you must first delete or reschedule these weekend lessons to weekdays (Monday-Friday).
        </p>
        <hr>
        <p class="mb-2"><strong>Weekend Lessons:</strong></p>
        <ul class="mb-2">
            <?php $__currentLoopData = $weekendLessons->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <strong><?php echo e($lesson->class->name ?? 'Unknown Class'); ?></strong> - 
                    <?php echo e($lesson->subject->name ?? 'Unknown Subject'); ?> 
                    (<?php echo e($lesson->weekday == 6 ? 'Saturday' : 'Sunday'); ?> 
                    <?php echo e($lesson->start_time); ?> - <?php echo e($lesson->end_time); ?>)
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($weekendLessonCount > 5): ?>
                <li><em>...and <?php echo e($weekendLessonCount - 5); ?> more</em></li>
            <?php endif; ?>
        </ul>
        <a href="<?php echo e(route('admin.lessons.index')); ?>?program_id=<?php echo e($academicProgram->id); ?>" class="btn btn-sm btn-warning">
            <i class="fas fa-calendar-alt"></i> View All Lessons
        </a>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Edit Academic Program
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo e(route("admin.academic-programs.update", [$academicProgram->id])); ?>" enctype="multipart/form-data">
            <?php echo method_field('PUT'); ?>
            <?php echo csrf_field(); ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="name">Program Name</label>
                        <input class="form-control <?php echo e($errors->has('name') ? 'is-invalid' : ''); ?>" type="text" name="name" id="name" value="<?php echo e(old('name', $academicProgram->name)); ?>" required>
                        <?php if($errors->has('name')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('name')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">e.g., Science, Technology, Engineering, and Mathematics</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="code">Program Code</label>
                        <input class="form-control <?php echo e($errors->has('code') ? 'is-invalid' : ''); ?>" type="text" name="code" id="code" value="<?php echo e(old('code', $academicProgram->code)); ?>" required>
                        <?php if($errors->has('code')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('code')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">e.g., STEM, ABM, BSIT</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="type">Program Type</label>
                        <select class="form-control <?php echo e($errors->has('type') ? 'is-invalid' : ''); ?>" name="type" id="type" required>
                            <option value="">Select Program Type</option>
                            <option value="senior_high" data-duration="2" <?php echo e(old('type', $academicProgram->type) == 'senior_high' ? 'selected' : ''); ?>>Senior High School (2 years)</option>
                            <option value="diploma" data-duration="3" <?php echo e(old('type', $academicProgram->type) == 'diploma' ? 'selected' : ''); ?>>Diploma Program / TESDA (3 years)</option>
                            <option value="college" data-duration="4" <?php echo e(old('type', $academicProgram->type) == 'college' ? 'selected' : ''); ?>>College (4 years)</option>
                        </select>
                        <?php if($errors->has('type')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('type')); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="duration_years">Duration (Years)</label>
                        <input class="form-control <?php echo e($errors->has('duration_years') ? 'is-invalid' : ''); ?>" type="number" name="duration_years" id="duration_years" value="<?php echo e(old('duration_years', $academicProgram->duration_years)); ?>" min="1" max="10" readonly required>
                        <?php if($errors->has('duration_years')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('duration_years')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block"><i class="fas fa-info-circle"></i> Duration is automatically set based on Program Type</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control <?php echo e($errors->has('description') ? 'is-invalid' : ''); ?>" name="description" id="description" rows="3"><?php echo e(old('description', $academicProgram->description)); ?></textarea>
                        <?php if($errors->has('description')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('description')); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <select class="form-control <?php echo e($errors->has('is_active') ? 'is-invalid' : ''); ?>" name="is_active" id="is_active">
                            <option value="1" <?php echo e(old('is_active', $academicProgram->is_active) == '1' ? 'selected' : ''); ?>>Active</option>
                            <option value="0" <?php echo e(old('is_active', $academicProgram->is_active) == '0' ? 'selected' : ''); ?>>Inactive</option>
                        </select>
                        <?php if($errors->has('is_active')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('is_active')); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    <?php echo e(trans('global.save')); ?>

                </button>
                <a href="<?php echo e(route('admin.academic-programs.index')); ?>" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    // Auto-fill duration based on program type
    $('#type').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var duration = selectedOption.data('duration');
        
        if (duration) {
            $('#duration_years').val(duration);
        }
    });
    
    // Trigger on page load if type is already selected
    if ($('#type').val()) {
        $('#type').trigger('change');
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/academic-programs/edit.blade.php ENDPATH**/ ?>