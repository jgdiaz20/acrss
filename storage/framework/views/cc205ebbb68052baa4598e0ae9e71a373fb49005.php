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
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-eye"></i> <?php echo e($subject->name); ?>

        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-book text-success mr-2"></i>
                <?php echo e($subject->name); ?>

            </h1>
            <p class="page-subtitle text-muted">
                Subject Details & Information
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
                <a href="<?php echo e(route('admin.subjects.index')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Subjects
                </a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('subject_edit')): ?>
                    <a href="<?php echo e(route('admin.subjects.edit', $subject->id)); ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('subject_edit')): ?>
                    <a href="<?php echo e(route('admin.subjects.assign-teachers', $subject->id)); ?>" class="btn btn-primary">
                        <i class="fas fa-users"></i> Teachers
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Subject Information -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle text-primary mr-2"></i>
                    Subject Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="info-label">Subject Name</label>
                            <div class="info-value"><?php echo e($subject->name); ?></div>
                        </div>
                        <div class="info-item mb-3">
                            <label class="info-label">Subject Code</label>
                            <div class="info-value">
                                <span class="badge badge-info"><?php echo e($subject->code); ?></span>
                            </div>
                        </div>
                        <div class="info-item mb-3">
                            <label class="info-label">Subject Type</label>
                            <div class="info-value">
                                <span class="badge badge-<?php echo e($subject->type === 'core' ? 'primary' : 'secondary'); ?>">
                                    <?php echo e(\App\Subject::SUBJECT_TYPES[$subject->type]); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="info-label">Credits</label>
                            <div class="info-value"><?php echo e($subject->credits); ?></div>
                        </div>
                        <div class="info-item mb-3">
                            <label class="info-label">Status</label>
                            <div class="info-value">
                                <span class="badge badge-<?php echo e($subject->is_active ? 'success' : 'danger'); ?>">
                                    <?php echo e($subject->is_active ? 'Active' : 'Inactive'); ?>

                                </span>
                            </div>
                        </div>
                        <div class="info-item mb-3">
                            <label class="info-label">Requirements</label>
                            <div class="info-value">
                                <?php if($subject->requires_lab): ?>
                                    <span class="badge badge-warning mr-1">Laboratory</span>
                                <?php endif; ?>
                                <?php if($subject->requires_equipment): ?>
                                    <span class="badge badge-info mr-1">Equipment</span>
                                <?php endif; ?>
                                <?php if(!$subject->requires_lab && !$subject->requires_equipment): ?>
                                    <span class="badge badge-success">No Special Requirements</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if($subject->description): ?>
                    <div class="info-item">
                        <label class="info-label">Description</label>
                        <div class="info-value"><?php echo e($subject->description); ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if($subject->equipment_requirements): ?>
                    <div class="info-item">
                        <label class="info-label">Equipment Requirements</label>
                        <div class="info-value"><?php echo e($subject->equipment_requirements); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar text-success mr-2"></i>
                    Statistics
                </h3>
            </div>
            <div class="card-body">
                <div class="stat-item text-center mb-3">
                    <div class="stat-number text-primary"><?php echo e($stats['total_lessons']); ?></div>
                    <div class="stat-label">Total Lessons</div>
                </div>
                <div class="stat-item text-center mb-3">
                    <div class="stat-number text-success"><?php echo e($stats['active_teachers']); ?></div>
                    <div class="stat-label">Active Teachers</div>
                </div>
                <div class="stat-item text-center">
                    <div class="stat-number text-info"><?php echo e(number_format($stats['weekly_hours'], 1)); ?></div>
                    <div class="stat-label">Weekly Hours</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($subject->teachers->count() > 0): ?>
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chalkboard-teacher text-warning mr-2"></i>
            Assigned Teachers
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Teacher Name</th>
                        <th>Role</th>
                        <th>Experience</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $subject->teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold"><?php echo e($teacher->name); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if($teacher->pivot->is_primary): ?>
                                    <span class="badge badge-primary">Primary</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Secondary</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($teacher->pivot->experience_years); ?> years</td>
                            <td>
                                <span class="badge badge-<?php echo e($teacher->pivot->is_active ? 'success' : 'danger'); ?>">
                                    <?php echo e($teacher->pivot->is_active ? 'Active' : 'Inactive'); ?>

                                </span>
                            </td>
                            <td><?php echo e($teacher->pivot->notes ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($subject->lessons->count() > 0): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt text-info mr-2"></i>
            Recent Lessons
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Class</th>
                        <th>Teacher</th>
                        <th>Room</th>
                        <th>Day</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $subject->lessons->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="badge badge-outline-primary"><?php echo e($lesson->class->name ?? 'N/A'); ?></span>
                            </td>
                            <td><?php echo e($lesson->teacher->name ?? 'N/A'); ?></td>
                            <td><?php echo e($lesson->room->name ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge badge-outline-secondary">
                                    <?php echo e(\App\Lesson::WEEK_DAYS[$lesson->weekday] ?? 'N/A'); ?>

                                </span>
                            </td>
                            <td>
                                <span class="text-muted"><?php echo e($lesson->start_time); ?> - <?php echo e($lesson->end_time); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php if($subject->lessons->count() > 10): ?>
            <div class="card-footer bg-light">
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Showing first 10 lessons. Total: <?php echo e($subject->lessons->count()); ?>

                </small>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/subjects/show.blade.php ENDPATH**/ ?>