
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
        <?php if($schoolClass->program): ?>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('admin.school-classes.program', $schoolClass->program->type)); ?>">
                    <i class="fas fa-<?php echo e($schoolClass->program->type == 'senior_high' ? 'graduation-cap' : 'university'); ?>"></i>
                    <?php echo e($schoolClass->program->type == 'senior_high' ? 'Senior High School' : 'College'); ?>

                </a>
            </li>
            <?php if($schoolClass->gradeLevel): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.school-classes.program.grade', [$schoolClass->program->type, $schoolClass->gradeLevel->id])); ?>">
                        <i class="fas fa-layer-group"></i> <?php echo e($schoolClass->gradeLevel->level_name); ?>

                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-eye"></i> <?php echo e($schoolClass->name); ?> Schedule
        </li>
    </ol>
</nav>

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header -->
            <div class="timetable-container school-class-timetable">
                <div class="timetable-header">
                    <div class="text-center mb-3">
                        <h2 class="timetable-title"><?php echo e($schoolClass->name); ?> - Class Schedule</h2>
                        <p class="timetable-info">
                            <?php echo e($schoolClass->program->name ?? 'N/A'); ?> - <?php echo e($schoolClass->gradeLevel->level_name ?? 'N/A'); ?>

                            <?php if($schoolClass->section): ?>
                                (Section <?php echo e($schoolClass->section); ?>)
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="text-center">
                        <a class="btn btn-secondary" href="<?php echo e(route('admin.school-classes.index')); ?>">
                            <i class="fas fa-arrow-left"></i> Back to Classes
                        </a>
                        <button onclick="printTimetable()" class="btn btn-success btn-print">
                            <i class="fas fa-print"></i> Print Timetable
                        </button>
                    </div>
                </div>

                <?php if(session('status')): ?>
                    <div class="alert alert-success m-3" role="alert">
                        <?php echo e(session('status')); ?>

                    </div>
                <?php endif; ?>

                <!-- Timetable Grid -->
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
                        <div class="timetable-day-column <?php echo e(($index == 6 || $index == 7) ? 'weekend' : ''); ?>" data-day="<?php echo e($index); ?>">
                            <?php if(isset($calendarData[$index]) && count($calendarData[$index]) > 0): ?>
                                <?php $__currentLoopData = $calendarData[$index]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="class-box school-class-lesson-box" 
                                         title="Lesson: <?php echo e($lesson['subject_code']); ?> with <?php echo e($lesson['teacher_name']); ?>">
                                        <div class="class-subject"><?php echo e($lesson['subject_code']); ?></div>
                                        <div class="class-time"><?php echo e($lesson['start_time']); ?> - <?php echo e($lesson['end_time']); ?></div>
                                        <div class="class-instructor"><?php echo e($lesson['teacher_name']); ?></div>
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

                <!-- Class Statistics -->
                <div class="row mt-4 justify-content-center">
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card statistics-card">
                            <div class="card-body text-center">
                                <h5 class="statistics-title">Total Lessons</h5>
                                <h3 class="statistics-number"><?php echo e($lessons->count()); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card statistics-card">
                            <div class="card-body text-center">
                                <h5 class="statistics-title">Subjects</h5>
                                <h3 class="statistics-number"><?php echo e($lessons->pluck('subject_id')->unique()->count()); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card statistics-card">
                            <div class="card-body text-center">
                                <h5 class="statistics-title">Teachers</h5>
                                <h3 class="statistics-number"><?php echo e($lessons->pluck('teacher_id')->unique()->count()); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card statistics-card">
                            <div class="card-body text-center">
                                <h5 class="statistics-title">Rooms Used</h5>
                                <h3 class="statistics-number"><?php echo e($lessons->pluck('room_id')->unique()->count()); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lesson Details Modal -->
<div class="modal fade" id="lessonDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lesson Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="lessonDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editLessonBtn">Edit Lesson</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>
<script>
$(document).ready(function() {
    // Section timetable is read-only - no editing functionality
});

function showLessonDetails(lessonId) {
    // Load lesson details via AJAX
    $.get('<?php echo e(route("admin.lessons.show", "")); ?>/' + lessonId)
        .done(function(data) {
            $('#lessonDetailsContent').html(data);
            $('#lessonDetailsModal').modal('show');
        })
        .fail(function() {
            alert('Failed to load lesson details');
        });
}
</script>


<script>
function printTimetable() {
    // Add printing class to body
    document.body.classList.add('printing');
    
    // Print the page
    window.print();
    
    // Remove printing class after print dialog closes
    setTimeout(function() {
        document.body.classList.remove('printing');
    }, 1000);
}

// Handle print events
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/school-classes/show.blade.php ENDPATH**/ ?>