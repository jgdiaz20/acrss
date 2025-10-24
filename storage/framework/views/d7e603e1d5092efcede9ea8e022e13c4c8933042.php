<?php $__env->startSection('content'); ?>

<div class="card">
    <div class="card-header">
        Create Academic Program
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo e(route("admin.academic-programs.store")); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="name">Program Name</label>
                        <input class="form-control <?php echo e($errors->has('name') ? 'is-invalid' : ''); ?>" type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required>
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
                        <input class="form-control <?php echo e($errors->has('code') ? 'is-invalid' : ''); ?>" type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" required>
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
                            <option value="senior_high" data-duration="2" <?php echo e(old('type') == 'senior_high' ? 'selected' : ''); ?>>Senior High School (2 years)</option>
                            <option value="diploma" data-duration="3" <?php echo e(old('type') == 'diploma' ? 'selected' : ''); ?>>Diploma Program / TESDA (3 years)</option>
                            <option value="college" data-duration="4" <?php echo e(old('type') == 'college' ? 'selected' : ''); ?>>College (4 years)</option>
                        </select>
                        <?php if($errors->has('type')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('type')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">Duration will be automatically set based on program type</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="duration_years">Duration (Years)</label>
                        <input class="form-control <?php echo e($errors->has('duration_years') ? 'is-invalid' : ''); ?>" type="number" name="duration_years" id="duration_years" value="<?php echo e(old('duration_years')); ?>" min="1" max="10" required readonly>
                        <?php if($errors->has('duration_years')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('duration_years')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">Automatically set based on program type</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control <?php echo e($errors->has('description') ? 'is-invalid' : ''); ?>" name="description" id="description" rows="3"><?php echo e(old('description')); ?></textarea>
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
                            <option value="1" <?php echo e(old('is_active', '1') == '1' ? 'selected' : ''); ?>>Active</option>
                            <option value="0" <?php echo e(old('is_active') == '0' ? 'selected' : ''); ?>>Inactive</option>
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
        } else {
            $('#duration_years').val('');
        }
    });
    
    // Trigger on page load if type is already selected
    if ($('#type').val()) {
        $('#type').trigger('change');
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/academic-programs/create.blade.php ENDPATH**/ ?>