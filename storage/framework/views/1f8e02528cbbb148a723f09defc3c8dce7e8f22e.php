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
            <a href="<?php echo e(route('admin.subjects.index')); ?>">
                <i class="fas fa-book"></i> Subjects
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('admin.subjects.show', $subject->id)); ?>">
                <i class="fas fa-eye"></i> <?php echo e($subject->name); ?>

            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-edit"></i> Edit
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-edit text-warning mr-2"></i>
                Edit <?php echo e($subject->name); ?>

            </h1>
            <p class="page-subtitle text-muted">
                Update subject information and settings
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
                <a href="<?php echo e(route('admin.subjects.show', $subject->id)); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Subject
                </a>
                <a href="<?php echo e(route('admin.subjects.index')); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> All Subjects
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle text-primary mr-2"></i>
            Subject Information
        </h3>
    </div>
    <div class="card-body">
        <!-- Error Messages -->
        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo e(route("admin.subjects.update", [$subject->id])); ?>" enctype="multipart/form-data">
            <?php echo method_field('PUT'); ?>
            <?php echo csrf_field(); ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="name">Subject Name</label>
                        <input class="form-control <?php echo e($errors->has('name') ? 'is-invalid' : ''); ?>" type="text" name="name" id="name" value="<?php echo e(old('name', $subject->name)); ?>" required>
                        <?php if($errors->has('name')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('name')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">Enter the full name of the subject (e.g., Computer Programming)</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="required" for="code">Subject Code</label>
                        <input class="form-control <?php echo e($errors->has('code') ? 'is-invalid' : ''); ?>" type="text" name="code" id="code" value="<?php echo e(old('code', $subject->code)); ?>" required>
                        <?php if($errors->has('code')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('code')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">Enter a unique code for the subject (e.g., COMPROG)</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="required" for="credits">Credits</label>
                        <input class="form-control <?php echo e($errors->has('credits') ? 'is-invalid' : ''); ?>" type="number" name="credits" id="credits" value="<?php echo e(old('credits', $subject->credits)); ?>" min="1" max="10" required>
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
                                <option value="<?php echo e($key); ?>" <?php echo e((old('type', $subject->type) == $key) ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($errors->has('type')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('type')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">Select the type of subject</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control <?php echo e($errors->has('description') ? 'is-invalid' : ''); ?>" name="description" id="description" rows="3"><?php echo e(old('description', $subject->description)); ?></textarea>
                        <?php if($errors->has('description')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('description')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">Optional description of the subject</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="equipment_requirements">Equipment Requirements</label>
                        <textarea class="form-control <?php echo e($errors->has('equipment_requirements') ? 'is-invalid' : ''); ?>" name="equipment_requirements" id="equipment_requirements" rows="2"><?php echo e(old('equipment_requirements', $subject->equipment_requirements)); ?></textarea>
                        <?php if($errors->has('equipment_requirements')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('equipment_requirements')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">List any specific equipment requirements</span>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="requires_lab" id="requires_lab" value="1" <?php echo e(old('requires_lab', $subject->requires_lab) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="requires_lab">
                                Requires Laboratory
                            </label>
                        </div>
                        <span class="help-block">Check if this subject requires laboratory facilities</span>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="requires_equipment" id="requires_equipment" value="1" <?php echo e(old('requires_equipment', $subject->requires_equipment) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="requires_equipment">
                                Requires Special Equipment
                            </label>
                        </div>
                        <span class="help-block">Check if this subject requires special equipment</span>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?php echo e(old('is_active', $subject->is_active) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <span class="help-block">Check if this subject is currently active</span>
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-4">
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-success btn-lg" type="submit">
                                <i class="fas fa-save mr-2"></i> Update Subject
                            </button>
                            <a href="<?php echo e(route('admin.subjects.show', $subject->id)); ?>" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="<?php echo e(route('admin.subjects.index')); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-list mr-2"></i> All Subjects
                            </a>
                            <a href="<?php echo e(route('admin.subjects.show', $subject->id)); ?>" class="btn btn-outline-info ml-2">
                                <i class="fas fa-eye mr-2"></i> View Subject
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/subjects/edit.blade.php ENDPATH**/ ?>