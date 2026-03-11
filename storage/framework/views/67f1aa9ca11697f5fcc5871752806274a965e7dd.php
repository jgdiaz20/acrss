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

                <div class="timetable-scroll-wrapper">
                    <div class="timetable-container-fixed">
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
                        <div class="timetable-day-column">
                            <?php if(isset($calendarData[$index]) && count($calendarData[$index]) > 0): ?>
                                <?php $__currentLoopData = $calendarData[$index]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="class-box teacher-timetable-class-box">
                                        <div class="class-subject"><?php echo e($lesson['subject_code']); ?></div>
                                        <div class="class-time"><?php echo e($lesson['start_time']); ?> - <?php echo e($lesson['end_time']); ?></div>
                                        <div class="class-instructor"><?php echo e($lesson['class_name']); ?></div>
                                        <div class="class-room"><?php echo e($lesson['room_name']); ?></div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <div class="not-scheduled-box">
                                    Available
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>

/* Teacher Calendar - Improved Timetable Only */
.timetable-day-header {
background: #f8f9fa;
padding: 15px 10px;
text-align: center;
font-weight: 600;
color: #495057;
border-right: 1px solid #e1e5e9;
border-bottom: 1px solid #e1e5e9;
}

/* Timetable Scroll Wrapper - Fixed width, horizontal scroll */
.timetable-scroll-wrapper {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}

.timetable-container-fixed {
    min-width: 1000px; /* Fixed minimum width to force horizontal scroll on mobile */
    width: 100%;
}

/* Teacher Timetable Class Box Styling - Fixed Dimensions for Consistency */
.teacher-timetable-class-box {
    background: white !important;
    border: 1px solid #d1d3d4 !important;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    width: 140px;
    height: 85px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    box-sizing: border-box;
}
/* Enhanced box shadow with better visual hierarchy */
.timetable-container {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06), 
                0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    background: white;
    overflow: hidden;
    margin-bottom: 20px; /* Add space below container */
}


.teacher-timetable-class-box .class-subject {
    color: #28a745 !important;
    font-weight: 600;
    font-size: 12px;
    margin-bottom: 3px;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.teacher-timetable-class-box .class-time {
    color: #495057 !important;
    font-size: 11px;
    font-weight: 500;
    margin-bottom: 2px;
    line-height: 1.1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.teacher-timetable-class-box .class-instructor {
    color: #6c757d !important;
    font-size: 10px;
    margin-bottom: 2px;
    line-height: 1.1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex-shrink: 0;
}

.teacher-timetable-class-box .class-room {
    color: #6c757d !important;
    font-size: 10px;
    font-weight: 500;
    line-height: 1.1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex-shrink: 0;
}

/* Scrollbar Styling */
.timetable-scroll-wrapper::-webkit-scrollbar {
    height: 8px;
}

.timetable-scroll-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.timetable-scroll-wrapper::-webkit-scrollbar-thumb {
    background: #28a745;
    border-radius: 4px;
}

.timetable-scroll-wrapper::-webkit-scrollbar-thumb:hover {
    background: #218838;
}

/* Mobile Responsiveness - Keep timetable scrollable */
@media (max-width: 768px) {
    /* Timetable remains fixed width and scrollable */
    .timetable-container-fixed {
        min-width: 1000px; /* Maintain fixed width on mobile */
    }
    .timetable-container {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin: 10px; /* Add margin on mobile */
    }
    .content .timetable-container .timetable-header h2 {
        font-size: 1.2rem !important;
        line-height: 1.2;
    }
}

</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/teacher/calendar.blade.php ENDPATH**/ ?>