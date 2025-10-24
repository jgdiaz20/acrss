<!-- Lesson Edit Modal -->
<div class="modal fade" id="lessonModal" tabindex="-1" role="dialog" aria-labelledby="lessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lessonModalTitle">Edit Lesson</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="lessonForm">
                    <input type="hidden" id="lessonId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class_id" class="required">Class</label>
                                <select class="form-control select2" id="class_id" name="class_id" required>
                                    <option value="">-- Select Class --</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                <span class="help-block">Select the class for this lesson schedule</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject_id" class="required">Subject</label>
                                <select class="form-control select2" id="subject_id" name="subject_id" required>
                                    <option value="">-- Select Subject --</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                <span class="help-block">Select the subject for this lesson schedule</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="teacher_id" class="required">Teacher</label>
                                <select class="form-control select2" id="teacher_id" name="teacher_id" required>
                                    <option value="">-- Select Teacher --</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                <span class="help-block">Select a teacher for this lesson schedule</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Room information display (auto-set) -->
                            <div class="form-group">
                                <label>Room</label>
                                <div class="alert alert-info" id="roomInfoDisplay">
                                    <i class="fas fa-door-open"></i>
                                    <strong>Selected Room:</strong> <span id="selectedRoomName">Loading...</span>
                                </div>
                                <!-- Hidden input for form submission -->
                                <input type="hidden" id="room_id" name="room_id">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_time" class="required">Start Time</label>
                                <input class="form-control lesson-timepicker <?php echo e($errors->has('start_time') ? 'is-invalid' : ''); ?>" 
                                       type="text" 
                                       name="start_time" 
                                       id="start_time" 
                                       required>
                                <div class="invalid-feedback"></div>
                                <span class="help-block">Select the start time for this lesson (7:00 AM - 9:00 PM)</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_time" class="required">End Time</label>
                                <input class="form-control lesson-timepicker <?php echo e($errors->has('end_time') ? 'is-invalid' : ''); ?>" 
                                       type="text" 
                                       name="end_time" 
                                       id="end_time" 
                                       required>
                                <div class="invalid-feedback"></div>
                                <span class="help-block">Select the end time for this lesson (7:00 AM - 9:00 PM)</span>
                            </div>
                            <?php $__env->startPush('styles'); ?>
    <link href="<?php echo e(asset('css/lesson-timepicker.css')); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Initialize time pickers when modal opens
    $('#lessonModal').on('show.bs.modal', function() {
        initializeTimePickers();
    });

    function initializeTimePickers() {
        $('.lesson-timepicker').datetimepicker({
            format: 'HH:mm',
            stepping: 30,
            enabledHours: [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21],
            icons: {
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                time: 'fas fa-clock'
            }
        }).on('change.datetimepicker', function(e) {
            validateTimes();
        });
    }

    function validateTimes() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();

        if (startTime && endTime) {
            const start = moment(startTime, 'HH:mm');
            const end = moment(endTime, 'HH:mm');
            const duration = moment.duration(end.diff(start));
            const minutes = duration.asMinutes();

            let errorMessage = '';
            if (end <= start) {
                errorMessage = 'End time must be after start time';
            } else if (minutes < 30) {
                errorMessage = 'Classes must be at least 30 minutes long';
            } else if (minutes > 180) {
                errorMessage = 'Classes cannot be longer than 3 hours';
            }

            // Show/hide error message
            $('#timeValidationError').remove();
            if (errorMessage) {
                const alert = $('<div id="timeValidationError" class="alert alert-danger mt-2"><i class="fas fa-exclamation-circle"></i> ' + errorMessage + '</div>');
                $('#end_time').closest('.form-group').append(alert);
                $('#saveLessonButton').prop('disabled', true);
            } else {
                $('#saveLessonButton').prop('disabled', false);
            }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
                            <?php $__env->startPush('scripts'); ?>
                            <link href="<?php echo e(asset('css/lesson-timepicker.css')); ?>" rel="stylesheet">
                            <script src="<?php echo e(asset('js/room-timetable-timepicker.js')); ?>"></script>
                            <?php $__env->stopPush(); ?>
                        </div>
                    </div>
                    
                    <!-- Hidden weekday field - will be set automatically -->
                    <input type="hidden" id="weekday" name="weekday">
                    
                    <!-- Day indicator -->
                    <div class="form-group">
                        <div class="alert alert-info" id="dayIndicator">
                            <i class="fas fa-calendar-day"></i>
                            <strong>Selected Day:</strong> <span id="selectedDayName">Not selected</span>
                        </div>
                    </div>
                    
                    <!-- Validation Errors -->
                    <div id="validationErrors" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Validation Error:</strong>
                        <ul id="validationErrorList"></ul>
                    </div>
                    
                    <!-- Conflict Warning -->
                    <div id="conflictWarning" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Conflicts Detected:</strong>
                        <ul id="conflictList"></ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelEditBtn">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteLessonBtn" style="display: none;">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-primary" id="saveLessonBtn">
                    <i class="fas fa-save"></i> Save Lesson
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Lesson Details Modal -->
<div class="modal fade" id="lessonDetailsModal" tabindex="-1" role="dialog" aria-labelledby="lessonDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lessonDetailsModalLabel">Lesson Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="lessonDetailsContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Enhancements */
.modal-lg {
    max-width: 800px;
}

.modal-body {
    padding: 2rem;
}

.form-group label.required::after {
    content: " *";
    color: #dc3545;
}

.invalid-feedback {
    display: none !important; /* Hidden by default */
    width: 100%;
    margin-top: 0.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 13px;
    font-weight: 500;
    color: #dc3545;
    background: #f8d7da;
    border-left: 3px solid #dc3545;
    border-radius: 4px;
}

.invalid-feedback.show {
    display: block !important; /* Show when has content */
}

.invalid-feedback:empty {
    display: none !important; /* Hide when empty */
}

.invalid-feedback::before {
    content: "\f06a";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 0.5rem;
    color: #dc3545;
}

/* Remove red border from fields - only show error message */
.form-control.is-invalid,
.select2-container--default .select2-selection--single.is-invalid {
    border-color: #ced4da; /* Keep normal border color */
}

/* Select2 Enhancements */
.select2-container {
    width: 100% !important;
}

.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

/* Time Picker Enhancements */
.lesson-timepicker {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none' stroke='%236b7280'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m4 4V3m-6 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    padding-right: 40px;
}

/* Validation Errors */
#validationErrors {
    margin-top: 1rem;
    animation: slideDown 0.3s ease-out;
}

#validationErrorList {
    margin-bottom: 0;
    padding-left: 1.5rem;
    margin-top: 0.5rem;
}

#validationErrorList li {
    margin-bottom: 0.25rem;
}

/* Conflict Warning */
#conflictWarning {
    margin-top: 1rem;
}

#conflictList {
    margin-bottom: 0;
    padding-left: 1.5rem;
}

/* Animation for error display */
@keyframes  slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Button Enhancements */
.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

/* Loading State */
.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-lg {
        max-width: 95%;
        margin: 1rem auto;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .row .col-md-6,
    .row .col-md-4 {
        margin-bottom: 1rem;
    }
}
</style>
<?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/partials/lesson-edit-modal.blade.php ENDPATH**/ ?>