<?php $__env->startSection('content'); ?>

<div class="row mb-3">
    <div class="col-lg-12">
        <a href="<?php echo e(route('admin.room-management.rooms.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Rooms
        </a>
        <button onclick="showQRCode(<?php echo e($room->id); ?>, '<?php echo e($room->name); ?>')" class="btn btn-success ml-2">
            <i class="fas fa-qrcode mr-1"></i> View QR Code
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Room Details
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>
                        ID
                    </th>
                    <td>
                        <?php echo e($room->id); ?>

                    </td>
                </tr>
                <tr>
                    <th>
                        Name
                    </th>
                    <td>
                        <?php echo e($room->name); ?>

                    </td>
                </tr>
                <tr>
                    <th>
                        Description
                    </th>
                    <td>
                        <?php echo e($room->description ?? 'N/A'); ?>

                    </td>
                </tr>
                <tr>
                    <th>
                        Capacity
                    </th>
                    <td>
                        <?php echo e($room->capacity ?? 'N/A'); ?> students
                    </td>
                </tr>
                <tr>
                    <th>
                        Room Type
                    </th>
                    <td>
                        <?php if($room->is_lab): ?>
                            <span class="badge badge-warning">
                                <i class="fas fa-flask"></i> Laboratory
                            </span>
                        <?php else: ?>
                            <span class="badge badge-primary">
                                <i class="fas fa-chalkboard"></i> Classroom
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Timetable
                    </th>
                    <td>
                        <a href="<?php echo e(route('admin.room-management.room-timetables.show', $room->id)); ?>" class="btn btn-info">
                            <i class="fas fa-calendar"></i> View Room Timetable
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Room Schedules
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Teacher</th>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $room->lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($lesson->class->name ?? ''); ?></td>
                            <td><?php echo e($lesson->teacher->name ?? ''); ?></td>
                            <td><?php echo e(\App\Lesson::WEEK_DAYS[$lesson->weekday] ?? ''); ?></td>
                            <td><?php echo e($lesson->start_time); ?></td>
                            <td><?php echo e($lesson->end_time); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('partials.qr-code-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/rooms/show.blade.php ENDPATH**/ ?>