<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3>Welcome, <?php echo e(auth()->user()->name); ?></h3>
                    <p class="text-muted">Your teaching schedule and upcoming classes</p>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Classes This Week</h5>
                                    <h2 class="card-text"><?php echo e($totalClasses); ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Today's Classes</h5>
                                    <h2 class="card-text"><?php echo e($todayClasses->count()); ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Upcoming Classes</h5>
                                    <h2 class="card-text"><?php echo e($upcomingClasses->flatten()->count()); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Today's Schedule</h5>
                                </div>
                                <div class="card-body">
                                    <?php if($todayClasses->count() > 0): ?>
                                        <?php $__currentLoopData = $todayClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="alert alert-info">
                                                <strong><?php echo e($lesson->class->name); ?></strong><br>
                                                <small>
                                                    Time: <?php echo e(\Carbon\Carbon::parse($lesson->start_time)->format('g:i A')); ?> - <?php echo e(\Carbon\Carbon::parse($lesson->end_time)->format('g:i A')); ?><br>
                                                    Room: <?php echo e($lesson->room->display_name ?? 'N/A'); ?>

                                                </small>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <p class="text-muted">No classes scheduled for today.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Upcoming Classes</h5>
                                </div>
                                <div class="card-body">
                                    <?php if($upcomingClasses->count() > 0): ?>
                                        <?php $__currentLoopData = $upcomingClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $classes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <h6 class="text-primary"><?php echo e($day); ?></h6>
                                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="alert alert-light">
                                                    <strong><?php echo e($lesson->class->name); ?></strong><br>
                                                    <small>
                                                        Time: <?php echo e(\Carbon\Carbon::parse($lesson->start_time)->format('g:i A')); ?> - <?php echo e(\Carbon\Carbon::parse($lesson->end_time)->format('g:i A')); ?><br>
                                                        Room: <?php echo e($lesson->room->display_name ?? 'N/A'); ?>

                                                    </small>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <p class="text-muted">No upcoming classes scheduled.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Embedded Timetable -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="timetable-container">
                                <div class="timetable-header">
                                    <h2>My Weekly Schedule</h2>
                                    <p style="margin: 10px 0 0 0; opacity: 0.9;">Your complete teaching timetable</p>
                                </div>

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
                                    
                                    <?php
                                        // Generate timetable data for the dashboard
                                        $teacher = auth()->user();
                                        $teacherLessons = App\Lesson::with(['class', 'room', 'subject'])
                                            ->where('teacher_id', $teacher->id)
                                            ->get();
                                        
                                        $dashboardCalendarData = [];
                                        foreach ($weekDays as $index => $day) {
                                            $dayLessons = $teacherLessons->where('weekday', $index)->sortBy(function($lesson) {
                                                return \Carbon\Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
                                            });
                                            
                                            if ($dayLessons->count() > 0) {
                                                $dashboardCalendarData[$index] = $dayLessons->map(function($lesson) {
                                                    return [
                                                        'class_name' => $lesson->class->name ?? 'Unknown Class',
                                                        'room_name' => $lesson->room->display_name ?? $lesson->room->name ?? 'No Room',
                                                        'subject_name' => $lesson->subject->name ?? 'No Subject',
                                                        'start_time' => $lesson->start_time,
                                                        'end_time' => $lesson->end_time,
                                                        'lesson_id' => $lesson->id
                                                    ];
                                                })->values();
                                            } else {
                                                $dashboardCalendarData[$index] = [];
                                            }
                                        }
                                    ?>
                                    
                                    <?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="timetable-day-column <?php echo e(($index == 6 || $index == 7) ? 'weekend' : ''); ?>">
                                            <?php if(isset($dashboardCalendarData[$index]) && count($dashboardCalendarData[$index]) > 0): ?>
                                                <?php $__currentLoopData = $dashboardCalendarData[$index]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/teacher/dashboard.blade.php ENDPATH**/ ?>