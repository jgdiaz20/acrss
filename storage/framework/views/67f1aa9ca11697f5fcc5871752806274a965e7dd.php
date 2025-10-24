<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="timetable-container">
                <div class="timetable-header">
                    <h2>My Schedule - <?php echo e(auth()->user()->name); ?></h2>
                    <p style="margin: 10px 0 0 0; opacity: 0.9;">View your assigned classes and schedule</p>
                </div>

                <?php if(session('status')): ?>
                    <div class="alert alert-success m-3" role="alert">
                        <?php echo e(session('status')); ?>

                    </div>
                <?php endif; ?>

                <div class="timetable-grid">
                    <!-- Day headers -->
                    <div class="timetable-day-header"></div>
                    <?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="timetable-day-header"><?php echo e($day); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <!-- Day columns with lessons -->
                    <div class="timetable-time-column">
                        <!-- This column will be empty in the new design -->
                    </div>
                    
                    <?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="timetable-day-column <?php echo e(($index == 6 || $index == 7) ? 'weekend' : ''); ?>">
                            <?php if(isset($calendarData[$index]) && count($calendarData[$index]) > 0): ?>
                                <?php $__currentLoopData = $calendarData[$index]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="class-box">
                                        <div class="class-subject"><?php echo e($lesson['subject_name']); ?></div>
                                        <div class="class-time"><?php echo e($lesson['start_time']); ?> - <?php echo e($lesson['end_time']); ?></div>
                                        <div class="class-instructor"><?php echo e($lesson['class_name']); ?></div>
                                        <div class="class-room"><?php echo e($lesson['room_name']); ?></div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <div class="not-scheduled-box">
                                    <?php echo e(($index == 6 || $index == 7) ? 'Not Scheduled' : 'No Classes'); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="p-4 bg-light">
                    <h5>Schedule Summary</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Classes</h6>
                                    <h4>
                                        <?php
                                            $totalClasses = 0;
                                            foreach($calendarData as $dayLessons) {
                                                $totalClasses += count($dayLessons);
                                            }
                                        ?>
                                        <?php echo e($totalClasses); ?>

                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Classes</h6>
                                    <h4><?php echo e(collect($calendarData)->flatten(1)->pluck('class_name')->unique()->count()); ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Subjects</h6>
                                    <h4><?php echo e(collect($calendarData)->flatten(1)->pluck('subject_name')->unique()->count()); ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Rooms</h6>
                                    <h4><?php echo e(collect($calendarData)->flatten(1)->pluck('room_name')->unique()->count()); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/teacher/calendar.blade.php ENDPATH**/ ?>