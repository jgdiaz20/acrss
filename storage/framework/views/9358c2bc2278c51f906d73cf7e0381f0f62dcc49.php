<?php $__env->startSection('styles'); ?>
    <link href="<?php echo e(asset('css/lesson-timepicker.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="timetable-container admin-room-timetable">
                <div class="timetable-header">
                    <h2 class="timetable-title"><?php echo e($room->name); ?> Timetable</h2>
                    <?php if($room->description): ?>
                        <p class="timetable-info"><?php echo e($room->description); ?></p>
                    <?php endif; ?>
                    <div class="print-only" style="margin-top: 10px; font-size: 12px; opacity: 0.8;">
                        <p>Printed on: <?php echo e(date('F j, Y \a\t g:i A')); ?></p>
                        <?php if($room->capacity): ?>
                            <p>Capacity: <?php echo e($room->capacity); ?> students</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if(session('status')): ?>
                    <div class="alert alert-success m-3" role="alert">
                        <?php echo e(session('status')); ?>

                    </div>
                <?php endif; ?>

                <div class="mb-3 p-3 bg-light no-print">
                    <a href="<?php echo e(route('admin.room-management.room-timetables.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Room List
                    </a>
                    <a href="<?php echo e(route('admin.room-management.rooms.show', $room->id)); ?>" class="btn btn-info">
                        <i class="fas fa-info-circle"></i> Room Details
                    </a>
                    <button onclick="printTimetable()" class="btn btn-success btn-print">
                        <i class="fas fa-print"></i> Print Timetable
                    </button>
                    <div class="btn-group ml-2" role="group">
                        <button type="button" class="btn btn-primary" id="editModeToggle">
                            <i class="fas fa-edit"></i> <span id="editModeText">Enable Edit Mode</span>
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="refreshTimetable" style="display: none;">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
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
                    
                    <?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayNumber => $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="timetable-day-column <?php echo e(($dayNumber == 6 || $dayNumber == 7) ? 'weekend' : ''); ?>" data-day="<?php echo e($dayNumber); ?>">
                            <?php if(isset($calendarData[$dayNumber]) && count($calendarData[$dayNumber]) > 0): ?>
                                <?php $__currentLoopData = $calendarData[$dayNumber]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lessonIndex => $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="lesson-container">
                                        <div class="class-box editable-lesson" 
                                             data-lesson-id="<?php echo e($lesson['id'] ?? ''); ?>"
                                             data-action="edit"
                                             title="Click to view details, double-click to edit">
                                            <div class="class-subject"><?php echo e($lesson['subject_name']); ?></div>
                                            <div class="class-time"><?php echo e($lesson['start_time']); ?> - <?php echo e($lesson['end_time']); ?></div>
                                            <div class="class-instructor"><?php echo e($lesson['teacher_name']); ?></div>
                                            <div class="class-room"><?php echo e($lesson['class_name']); ?></div>
                                            <div class="lesson-actions" style="display: none;">
                                                <button class="btn btn-sm btn-outline-primary edit-lesson" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-lesson" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                
                                <!-- Add + button at the end of the day's lessons (only in edit mode) -->
                                <div class="add-lesson-btn add-at-end" 
                                     data-room-id="<?php echo e($room->id); ?>"
                                     data-day="<?php echo e($dayNumber); ?>"
                                     title="Add lesson for this day"
                                     style="display: none;">
                                    <button class="btn btn-sm btn-success add-lesson-button">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="not-scheduled-box editable-cell" 
                                     data-action="create"
                                     data-room-id="<?php echo e($room->id); ?>"
                                     data-day="<?php echo e($dayNumber); ?>"
                                     title="Enable edit mode to add lessons">
                                    <?php echo e(($dayNumber == 6 || $dayNumber == 7) ? 'Not Scheduled' : 'Available'); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <!-- Print-only footer -->
                <div class="print-only" style="margin-top: 20px; padding: 10px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ccc;">
                    <p><?php echo e($room->name); ?> Timetable - Generated by Laravel School Timetable Calendar</p>
                    <p>For more information, contact the school administration</p>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Include Lesson Edit Modal -->
<?php echo $__env->make('partials.lesson-edit-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>
<!-- Include Inline Editing JavaScript -->
<script src="<?php echo e(asset('js/inline-editing.js')); ?>"></script>

<script>
// Global function for handling + button clicks (defined outside DOMContentLoaded)
window.handleAddLessonClick = function(e, $btn) {
    e.stopPropagation();
    e.preventDefault();
    console.log('+ button clicked!');
    console.log('Event target:', e.target);
    console.log('Event currentTarget:', e.currentTarget);
    
    const $container = $btn.closest('.add-lesson-btn');
    const dayNumber = parseInt($container.data('day'));
    const roomId = parseInt($container.data('room-id'));
    
    console.log('Day:', dayNumber, 'Room:', roomId, 'Day type:', typeof dayNumber);
    console.log('Container data:', $container.data());
    
    if (typeof inlineEditing !== 'undefined' && inlineEditing !== null) {
        // Prevent duplicate calls
        if (inlineEditing.isEditing) {
            console.log('Already editing, ignoring duplicate call');
            return;
        }
        
        console.log('Calling showCreateModal...');
        try {
            inlineEditing.showCreateModal(dayNumber, roomId);
            console.log('showCreateModal called successfully');
        } catch (error) {
            console.error('Error calling showCreateModal:', error);
        }
    } else {
        console.error('Inline editing system not available');
    }
};

// Enhanced print functionality
function printTimetable() {
    // Add a temporary class to indicate printing
    document.body.classList.add('printing');
    
    // Trigger print dialog
    window.print();
    
    // Remove the class after printing
    setTimeout(() => {
        document.body.classList.remove('printing');
    }, 1000);
}

// Edit mode toggle functionality
let editMode = false;

document.addEventListener('DOMContentLoaded', function() {
    // Add a print-friendly title to the page
    const timetableTitle = '<?php echo e($room->name); ?> Timetable - <?php echo e(date("Y-m-d")); ?>';
    document.title = timetableTitle;
    
    // Add a print event listener
    window.addEventListener('beforeprint', function() {
        // Update page title for print
        document.title = timetableTitle;
    });
    
    window.addEventListener('afterprint', function() {
        // Restore original title
        document.title = '<?php echo e($room->name); ?> Timetable';
    });
    
    // Edit mode toggle
    $('#editModeToggle').click(function() {
        editMode = !editMode;
        
        if (editMode) {
            enableEditMode();
        } else {
            disableEditMode();
        }
    });
    
    // Refresh timetable
    $('#refreshTimetable').click(function() {
        window.location.reload();
    });
    
    
    
    // Initialize Select2 for modals
    $('.select2').select2({
        dropdownParent: $('#lessonModal')
    });
    
    // Initialize time pickers
    $('.lesson-timepicker').timepicker({
        timeFormat: 'h:mm p',
        interval: 30,
        minTime: '7:00am',
        maxTime: '9:00pm',
        defaultTime: '8:00am',
        startTime: '7:00am',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });
    
});

function enableEditMode() {
    editMode = true;
    $('#editModeText').text('Disable Edit Mode');
    $('#editModeToggle').removeClass('btn-primary').addClass('btn-warning');
    $('#refreshTimetable').show();
    
    // Add edit mode styling - lesson actions will show on hover via CSS
    $('.timetable-day-column').addClass('edit-mode editable-cell');
    $('.editable-cell').addClass('edit-mode-active');
    $('.editable-lesson').addClass('edit-mode-active');
    
    // Show + buttons for adding lessons
    console.log('Found add-lesson-btn elements:', $('.add-lesson-btn').length);
    $('.add-lesson-btn').each(function(index) {
        console.log('Button', index, ':', this, 'Current display:', $(this).css('display'));
    });
    $('.add-lesson-btn').show();
    console.log('Add lesson buttons should now be visible');
    
    // Attach event handlers to + buttons now that they're visible
    console.log('Attaching + button click handlers in edit mode');
    console.log('Number of .add-lesson-button elements:', $('.add-lesson-button').length);
    console.log('Number of .add-lesson-btn elements:', $('.add-lesson-btn').length);
    
    // Remove any existing handlers to prevent duplicates
    $('.add-lesson-button').off('click');
    
    // Attach new handlers
    $('.add-lesson-button').on('click', function(e) {
        console.log('+ button clicked via direct binding in edit mode!');
        window.handleAddLessonClick(e, $(this));
    });
    
    // Also attach document delegation as backup
    $(document).off('click', '.add-lesson-button').on('click', '.add-lesson-button', function(e) {
        console.log('+ button clicked via document delegation in edit mode!');
        window.handleAddLessonClick(e, $(this));
    });
    
    // Double-check visibility after show()
    setTimeout(() => {
        $('.add-lesson-btn').each(function(index) {
            console.log('Button', index, 'after show():', $(this).css('display'));
        });
    }, 100);
    
    // Show tooltips
    $('.editable-cell').attr('title', 'Click to add new lesson');
    $('.editable-lesson').attr('title', 'Click to view details, double-click to edit');
    
    // Reinitialize the inline editing system
    if (typeof inlineEditing !== 'undefined') {
        inlineEditing.initializeTimetable();
        console.log('Reinitialized inline editing for edit mode');
    }
}

// Global function for edit button clicks
function editLesson(lessonId) {
    if (typeof inlineEditing !== 'undefined') {
        inlineEditing.editLesson(lessonId);
    } else {
        console.error('Inline editing system not initialized');
    }
}

function disableEditMode() {
    editMode = false;
    $('#editModeText').text('Enable Edit Mode');
    $('#editModeToggle').removeClass('btn-warning').addClass('btn-primary');
    $('#refreshTimetable').hide();
    
    // Hide lesson actions
    $('.lesson-actions').hide();
    
    // Hide + buttons for adding lessons
    $('.add-lesson-btn').hide();
    
    // Remove event handlers when disabling edit mode
    console.log('Removing + button click handlers in disable edit mode');
    $('.add-lesson-button').off('click');
    $(document).off('click', '.add-lesson-button');
    
    // Remove edit mode styling
    $('.timetable-day-column').removeClass('edit-mode editable-cell');
    $('.editable-cell').removeClass('edit-mode-active');
    $('.editable-lesson').removeClass('edit-mode-active');
    
    // Update tooltips
    $('.editable-cell').attr('title', 'Enable edit mode to add lessons');
    $('.editable-lesson').attr('title', 'Enable edit mode to edit lessons');
}
</script>

<style>
/* Timetable Header Styling */
.timetable-header {
    background: white;
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timetable-title {
    color: #28a745;
    font-weight: bold;
    font-size: 24px;
    margin: 0 0 10px 0;
    align-items:center;
}

.timetable-info {
    color: #495057;
    font-size: 14px;
    margin: 0;
    font-weight: 500;
}

/* Edit Mode Styling */
.edit-mode .editable-cell {
    background: #e3f2fd;
    border: 2px dashed #2196f3;
    cursor: pointer;
    transition: all 0.3s ease;
}

.edit-mode .editable-cell:hover {
    background: #bbdefb;
    border-color: #1976d2;
}

.edit-mode .editable-lesson {
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.edit-mode .editable-lesson:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.edit-mode .editable-lesson:hover .lesson-actions {
    display: block !important;
}

.lesson-actions {
    position: absolute;
    top: 5px;
    right: 5px;
    z-index: 10;
    display: none; /* Hidden by default */
}

.lesson-actions .btn {
    padding: 2px 6px;
    margin-left: 2px;
    font-size: 10px;
}

.class-subject-name {
    color: #6c757d;
    font-size: 10px;
    font-style: italic;
    margin-top: 2px;
    
}

/* Lesson Container and + Button Styling */
.lesson-container {
    position: relative;
    margin-bottom: 5px;
    
}

.add-lesson-btn.add-at-end {
    margin-top: 8px;
    padding: 4px;
    border-top: 1px dashed #dee2e6;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
}

.add-lesson-btn.add-at-end .add-lesson-button {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 50%;
    font-size: 12px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    margin: 0 auto;
}

.add-lesson-btn.add-at-end .add-lesson-button i {
    margin: 0;
    padding: 0;
    line-height: 1;
    vertical-align: middle;
}

.add-lesson-btn.add-at-end .add-lesson-button:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Print styles */
@media  print {
    .no-print, .lesson-actions, .btn-group, .add-lesson-btn {
        display: none !important;
    }
    
    .edit-mode .editable-cell,
    .edit-mode .editable-lesson {
        background: transparent !important;
        border: 1px solid #dee2e6 !important;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/room-timetable/show.blade.php ENDPATH**/ ?>