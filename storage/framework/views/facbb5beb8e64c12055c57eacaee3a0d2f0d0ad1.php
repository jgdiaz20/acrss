<?php $__env->startSection('content'); ?>

<!-- Welcome Section -->
<div class="dashboard-welcome">
    <div class="welcome-content">
        <h1 class="welcome-title">
            <i class="fas fa-graduation-cap"></i>
            Asian College Room Scheduling System
        </h1>
        <p class="welcome-subtitle">Welcome back, <?php echo e(auth()->user()->name); ?>! Manage your school's schedule and resources efficiently.</p>
    </div>
</div>

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card stat-card-primary">
            <div class="stat-icon">
                <i class="fas fa-door-open"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?php echo e($totalRooms); ?></h3>
                <p class="stat-label">Total Rooms</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card stat-card-success">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?php echo e($totalLessons ?? 0); ?></h3>
                <p class="stat-label">Scheduled Lessons</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card stat-card-info">
            <div class="stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?php echo e($activeTeachers ?? 0); ?></h3>
                <p class="stat-label">Active Teachers</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card stat-card-warning">
            <div class="stat-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?php echo e(\App\SchoolClass::count()); ?></h3>
                <p class="stat-label">Total Sections</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="row">
    <!-- Room Management Section -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="header-content">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i>
                        Room Timetables
                    </h3>
                    <p class="card-subtitle">Select a room to view and manage its schedule</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo e(route('admin.room-management.room-timetables.index')); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-list mr-1"></i>
                        View All Rooms
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="rooms-grid">
                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="room-item" onclick="window.location.href='<?php echo e(route('admin.room-management.room-timetables.show', $room->id)); ?>'">
                            <div class="room-header">
                                <div class="room-icon <?php echo e($room->is_lab ? 'lab-icon' : 'classroom-icon'); ?>">
                                    <i class="fas fa-<?php echo e($room->is_lab ? 'flask' : 'door-open'); ?>"></i>
                                </div>
                                <div class="room-type">
                                    <?php if($room->is_lab): ?>
                                        <span class="badge badge-info">Laboratory</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary">Classroom</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="room-content">
                                <h5 class="room-name"><?php echo e($room->name); ?></h5>
                                <?php if($room->description): ?>
                                    <p class="room-description"><?php echo e($room->description); ?></p>
                                <?php endif; ?>
                                <div class="room-details">
                                    <?php if($room->capacity): ?>
                                        <span class="detail-item">
                                            <i class="fas fa-users"></i>
                                            <?php echo e($room->capacity); ?> students
                                        </span>
                                    <?php endif; ?>
                                    <?php if($room->has_equipment): ?>
                                        <span class="detail-item">
                                            <i class="fas fa-tools"></i>
                                            Equipment Available
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="room-action">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <?php if(\App\Room::count() > 5): ?>
                    <div class="rooms-footer">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle mr-2"></i>
                            Showing 5 of <?php echo e(\App\Room::count()); ?> total rooms. 
                            <a href="<?php echo e(route('admin.room-management.room-timetables.index')); ?>" class="alert-link">
                                <strong>View all rooms</strong>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Management -->
    <div class="col-lg-4 mb-4">
        <!-- Quick Actions -->
        <div class="dashboard-card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="<?php echo e(route('admin.school-classes.index')); ?>" class="quick-action-item">
                        <div class="action-icon action-primary">
                            <i class="fas fa-school"></i>
                        </div>
                        <div class="action-content">
                            <h6>School Classes</h6>
                            <small>Manage academic programs</small>
                        </div>
                    </a>
                    <a href="<?php echo e(route('admin.lessons.index')); ?>" class="quick-action-item">
                        <div class="action-icon action-success">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="action-content">
                            <h6>Lessons</h6>
                            <small>Schedule & manage lessons</small>
                        </div>
                    </a>
                    <a href="<?php echo e(route('admin.room-management.rooms.index')); ?>" class="quick-action-item">
                        <div class="action-icon action-info">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="action-content">
                            <h6>Rooms</h6>
                            <small>Manage room resources</small>
                        </div>
                    </a>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="quick-action-item">
                        <div class="action-icon action-warning">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="action-content">
                            <h6>Teachers</h6>
                            <small>Manage staff & teachers</small>
                        </div>
                    </a>
                    <a href="<?php echo e(route('admin.room-management.master-timetable.index')); ?>" class="quick-action-item">
                        <div class="action-icon action-primary">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <div class="action-content">
                            <h6>Master Timetable</h6>
                            <small>View all schedules at once</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    System Overview
                </h3>
            </div>
            <div class="card-body">
                <div class="system-stats">
                    <div class="system-stat-item">
                        <div class="stat-info">
                            <i class="fas fa-database text-primary"></i>
                            <span>Database Status</span>
                        </div>
                        <span class="status-badge status-success">Online</span>
                    </div>
                    <div class="system-stat-item">
                        <div class="stat-info">
                            <i class="fas fa-calendar-alt text-success"></i>
                            <span>Active Schedules</span>
                        </div>
                        <span class="status-badge status-success"><?php echo e($totalLessons ?? 0); ?></span>
                    </div>
                    <div class="system-stat-item">
                        <div class="stat-info">
                            <i class="fas fa-users text-info"></i>
                            <span>Active Users</span>
                        </div>
                        <span class="status-badge status-success"><?php echo e($activeTeachers ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/home.blade.php ENDPATH**/ ?>