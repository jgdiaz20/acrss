<?php $__env->startSection('content'); ?>

<div class="card">
    <div class="card-header">
        Create Subject
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo e(route("admin.subjects.store")); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label class="required" for="name">Subject Name</label>
                <input class="form-control <?php echo e($errors->has('name') ? 'is-invalid' : ''); ?>" type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required>
                <?php if($errors->has('name')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('name')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Enter the full name of the subject (e.g., Computer Programming)</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="code">Subject Code</label>
                <input class="form-control <?php echo e($errors->has('code') ? 'is-invalid' : ''); ?>" type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" required>
                <?php if($errors->has('code')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('code')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Enter a unique code for the subject (e.g., COMPROG)</span>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control <?php echo e($errors->has('description') ? 'is-invalid' : ''); ?>" name="description" id="description" rows="3"><?php echo e(old('description')); ?></textarea>
                <?php if($errors->has('description')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('description')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Optional description of the subject</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="credits">Credits</label>
                <input class="form-control <?php echo e($errors->has('credits') ? 'is-invalid' : ''); ?>" type="number" name="credits" id="credits" value="<?php echo e(old('credits', 3)); ?>" min="1" max="10" required>
                <?php if($errors->has('credits')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('credits')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Number of credit hours for this subject</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="type">Subject Type</label>
                <select class="form-control <?php echo e($errors->has('type') ? 'is-invalid' : ''); ?>" name="type" id="type" required>
                    <option value="">Select Type</option>
                    <?php $__currentLoopData = \App\Subject::SUBJECT_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('type') == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('type')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('type')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Select the type of subject</span>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="requires_lab" id="requires_lab" value="1" <?php echo e(old('requires_lab') ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="requires_lab">
                        Requires Laboratory
                    </label>
                </div>
                <span class="help-block">Check if this subject requires laboratory facilities</span>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="requires_equipment" id="requires_equipment" value="1" <?php echo e(old('requires_equipment') ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="requires_equipment">
                        Requires Special Equipment
                    </label>
                </div>
                <span class="help-block">Check if this subject requires special equipment</span>
            </div>
            
            <div class="form-group">
                <label for="equipment_requirements">Equipment Requirements</label>
                <textarea class="form-control <?php echo e($errors->has('equipment_requirements') ? 'is-invalid' : ''); ?>" name="equipment_requirements" id="equipment_requirements" rows="2"><?php echo e(old('equipment_requirements')); ?></textarea>
                <?php if($errors->has('equipment_requirements')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('equipment_requirements')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">List any specific equipment requirements</span>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
                <span class="help-block">Check if this subject is currently active</span>
            </div>
            
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> Create Subject
                </button>
                <a href="<?php echo e(route('admin.subjects.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/subjects/create.blade.php ENDPATH**/ ?>