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
            <i class="fas fa-<?php echo e($programType == 'senior_high' ? 'graduation-cap' : 'university'); ?>"></i>
            <?php echo e($programType == 'senior_high' ? 'Senior High School' : 'College'); ?> Classes
        </li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-<?php echo e($programType == 'senior_high' ? 'graduation-cap' : 'university'); ?>"></i>
                    <?php echo e($programType == 'senior_high' ? 'Senior High School Classes' : 'College Classes'); ?>

                </h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('admin.school-classes.index')); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Programs
                    </a>
                </div>
            </div>
            <div class="card-body p-2">
                <form method="GET" action="<?php echo e(route('admin.school-classes.program', $programType)); ?>" class="mb-3">
                    <div class="form-row align-items-end">
                        <div class="col-md-4 mb-2">
                            <label for="q" class="small text-muted">Search Classes</label>
                            <input type="text" name="q" id="q" value="<?php echo e($search ?? ''); ?>" class="form-control" placeholder="Search by class name or section">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="program_id" class="small text-muted">Program</label>
                            <select name="program_id" id="program_id" class="form-control" onchange="this.form.submit()">
                                <option value="">All</option>
                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>" <?php echo e((isset($programId) && (string)$programId === (string)$p->id) ? 'selected' : ''); ?>><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Search</button>
                        </div>
                    </div>
                </form>
                <?php if(!empty($noMatches)): ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-circle"></i> No matches for your search.
                    </div>
                <?php endif; ?>
                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="program-section mb-3">
                        <div class="program-header bg-light p-2 mb-2 rounded">
                            <h5 class="mb-0 text-primary">
                                <i class="fas fa-graduation-cap mr-1"></i>
                                <?php echo e($program->name); ?>

                            </h5>
                        </div>
                        
                        <div class="row">
                            <?php $__currentLoopData = $gradeLevels->where('program_id', $program->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gradeLevel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $classesInGrade = $schoolClasses->get($program->id, collect())->get($gradeLevel->id, collect());
                                ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                    <div class="grade-card border rounded p-2 h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 text-info">
                                                <i class="fas fa-layer-group mr-1"></i>
                                                <?php echo e($gradeLevel->level_name); ?>

                                            </h6>
                                            <a href="<?php echo e(route('admin.school-classes.program.grade', [$programType, $gradeLevel->id])); ?>" 
                                               class="btn btn-xs btn-outline-info">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                        
                                        <?php if($classesInGrade->count() > 0): ?>
                                            <div class="classes-list">
                                                <?php $__currentLoopData = $classesInGrade->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="class-item mb-2">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
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
                                            <a href="<?php echo e(route('admin.school-classes.program.grade', [$programType, $gradeLevel->id])); ?>" 
                                               class="btn btn-sm btn-outline-info btn-block">
                                                Manage Classes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/school-classes/by-program.blade.php ENDPATH**/ ?>