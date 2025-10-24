
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
            <a href="<?php echo e(route('admin.school-classes.index')); ?>">
                <i class="fas fa-school"></i> School Classes
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-<?php echo e($program->type == 'senior_high' ? 'graduation-cap' : 'university'); ?>"></i>
            <?php echo e($program->name); ?>

        </li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-<?php echo e($program->type == 'senior_high' ? 'graduation-cap' : 'university'); ?>"></i>
                    <?php echo e($program->name); ?> Classes
                </h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('admin.school-classes.create', ['program_id' => $program->id])); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Class
                    </a>
                    <a href="<?php echo e(route('admin.school-classes.index')); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Programs
                    </a>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="program-section mb-3">
                    <div class="program-header bg-light p-2 mb-2 rounded">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-graduation-cap mr-1"></i>
                            <?php echo e($program->name); ?> (<?php echo e($program->code); ?>)
                        </h5>
                        <small class="text-muted"><?php echo e($program->description); ?></small>
                    </div>
                    
                    <div class="row">
                        <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gradeLevel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $classesInGrade = $schoolClasses->get($gradeLevel->id, collect());
                            ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <div class="grade-card border rounded p-2 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 text-info">
                                            <i class="fas fa-layer-group mr-1"></i>
                                            <?php echo e($gradeLevel->level_name); ?>

                                        </h6>
                                        <a href="<?php echo e(route('admin.school-classes.program.grade', [$program->type, $gradeLevel->id])); ?>" 
                                           class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                    
                                    <?php if($classesInGrade->count() > 0): ?>
                                        <div class="classes-list">
                                            <?php $__currentLoopData = $classesInGrade->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="class-item mb-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="class-name text-truncate" style="max-width: 120px;" title="<?php echo e($class->name); ?>">
                                                            <?php echo e($class->name); ?>

                                                            <?php if($class->section): ?>
                                                                <small class="text-muted">- <?php echo e($class->section); ?></small>
                                                            <?php endif; ?>
                                                        </span>
                                                        <small class="badge badge-light">
                                                            <?php echo e($class->classUsers->count()); ?>/<?php echo e($class->max_students ?? '∞'); ?>

                                                        </small>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($classesInGrade->count() > 3): ?>
                                                <div class="text-center">
                                                    <small class="text-muted">
                                                        +<?php echo e($classesInGrade->count() - 3); ?> more classes
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-muted">
                                            <small><i class="fas fa-exclamation-triangle"></i> No classes</small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2">
                                        <a href="<?php echo e(route('admin.school-classes.program.grade', [$program->type, $gradeLevel->id])); ?>" 
                                           class="btn btn-sm btn-outline-info btn-block">
                                            Manage Classes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/school-classes/manage-program.blade.php ENDPATH**/ ?>