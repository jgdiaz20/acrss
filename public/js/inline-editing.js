/**
 * Inline Editing System for Laravel School Timetable
 * Phase 3: Google Sheets-like editing experience
 */

class InlineEditingSystem {
    constructor() {
        this.isEditing = false;
        this.currentLesson = null;
        this.currentAction = null;
        this.currentData = null;
        this.conflictChecker = new ConflictChecker();
        this.clickTimeout = null;
        this.subjectsData = [];
        this.hoursTrackingData = null;
        this.endTimeManuallyEntered = false;
        
        // DOM cache for frequently accessed elements
        this.cache = {
            modal: null,
            saveBtn: null,
            deleteBtn: null,
            cancelBtn: null,
            lessonForm: null,
            fields: {}
        };
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeTimetable();
        this.setupTooltips();
        this.initializeCache();
    }
    
    initializeCache() {
        // Initialize DOM cache for frequently accessed elements
        this.cache.modal = $('#lessonModal');
        this.cache.saveBtn = $('#saveLessonBtn');
        this.cache.deleteBtn = $('#deleteLessonBtn');
        this.cache.cancelBtn = $('#cancelEditBtn');
        this.cache.lessonForm = $('#lessonForm');
        
        // Cache form fields
        this.cache.fields = {
            lessonId: $('#lessonId'),
            classId: $('#class_id'),
            subjectId: $('#subject_id'),
            teacherId: $('#teacher_id'),
            roomId: $('#room_id'),
            lessonType: $('#lesson_type'),
            weekday: $('#weekday'),
            startTime: $('#start_time'),
            endTime: $('#end_time')
        };
        
        console.log('DOM cache initialized');
    }

    bindEvents() {
        // Consolidated event handlers - .editable-lesson is used in room timetable
        // Note: .class-box and .timetable-day-column are old classes no longer in use
        $(document).on('click', '.editable-lesson', (e) => {
            if (!this.isEditing) {
                this.handleLessonClick(e);
            }
        });

        $(document).on('dblclick', '.editable-lesson', (e) => {
            e.stopPropagation();
            this.handleLessonEdit(e);
        });
        $(document).on('click', '.edit-lesson', (e) => {
            e.stopPropagation();
            const lessonId = $(e.currentTarget).closest('.editable-lesson').data('lesson-id');
            console.log('Edit button clicked, lesson ID:', lessonId);
            this.editLesson(lessonId);
        });
        
        $(document).on('click', '.delete-lesson', (e) => {
            e.stopPropagation();
            const lessonId = $(e.currentTarget).closest('.editable-lesson').data('lesson-id');
            console.log('Delete button clicked, lesson ID:', lessonId);
            
            // Set the lesson ID in the hidden field for deletion
            $('#lessonId').val(lessonId);
            console.log('Set lesson ID for direct deletion:', lessonId);
            
            this.deleteLesson();
        });

        // Modal events - using delegated events to ensure they work
        $(document).on('click', '#saveLessonBtn', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Save button clicked');
            this.saveLesson();
        });
        $(document).on('click', '#deleteLessonBtn', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Delete button clicked');
            this.deleteLesson();
        });
        $(document).on('click', '#cancelEditBtn', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Cancel button clicked');
            this.cancelEdit();
        });
        
        // Prevent form submission (Enter key)
        $(document).on('submit', '#lessonForm', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Form submit prevented');
            return false;
        });
        
        // Prevent Enter key from submitting form
        $(document).on('keypress', '#lessonForm input', (e) => {
            if (e.which === 13 || e.keyCode === 13) {
                e.preventDefault();
                console.log('Enter key prevented on input field');
                return false;
            }
        });
        
        // Toggle chevron icon when hours tracking section is collapsed/expanded
        $(document).on('shown.bs.collapse', '#hoursTrackingSection', () => {
            $('#hoursTrackingIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        });
        $(document).on('hidden.bs.collapse', '#hoursTrackingSection', () => {
            $('#hoursTrackingIcon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        });
        
        // Remove backdrop click handler to prevent accidental closing
        // $(document).on('click', '.modal-backdrop', () => this.cancelEdit());

        // Real-time error clearing when user types/selects
        $(document).on('input change', '#lessonForm input, #lessonForm select', function() {
            const $field = $(this);
            const fieldValue = $field.val();
            
            // If field has value and is currently showing error, clear it
            if (fieldValue && fieldValue.trim() !== '') {
                // Remove is-invalid class from field
                $field.removeClass('is-invalid');
                
                // Find and clear error message (check both siblings and within form-group)
                let $feedback = $field.siblings('.invalid-feedback');
                if ($feedback.length === 0) {
                    $feedback = $field.closest('.form-group').find('.invalid-feedback');
                }
                $feedback.text('').removeClass('show').hide();
                
                // Check if all field errors are cleared
                const hasFieldErrors = $('#lessonForm .form-control.is-invalid').length > 0;
                
                console.log('Field changed:', $field.attr('id'), 'Value:', fieldValue);
                console.log('Remaining errors:', hasFieldErrors);
                
                // If no field errors remain, enable save button
                if (!hasFieldErrors) {
                    console.log('All errors cleared - enabling save button');
                    $('#saveLessonBtn').prop('disabled', false);
                } else {
                    console.log('Still has errors - keeping save button disabled');
                }
            }
        });
    }

    initializeTimetable() {
        console.log('Initializing timetable...');
        console.log('Editable lessons found:', $('.editable-lesson').length);
    }

    setupTooltips() {
        // Add tooltips for better UX
        $('.editable-lesson').attr('title', 'Click to view details, double-click to edit');
    }

    isEditModeEnabled() {
        // Check if edit mode is enabled - room timetable uses global editMode variable
        return typeof editMode !== 'undefined' && editMode === true;
    }

    isUserAuthenticated() {
        // Check if CSRF token exists (indicates user is logged in)
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        return csrfToken && csrfToken.length > 0;
    }

    closeAllModals() {
        // Close all modals except details modal when showing details
        if (this.currentAction !== 'details') {
            // Use a more gentle approach to close modals
            $('.modal').each(function() {
                if ($(this).hasClass('show')) {
                    $(this).modal('hide');
                }
            });
            
            // Clean up any lingering backdrop
            setTimeout(() => {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            }, 300);
        }
    }

    handleCellClick(e) {
        // Check if the click originated from a + button
        if ($(e.target).hasClass('add-lesson-button') || $(e.target).closest('.add-lesson-button').length > 0) {
            console.log('Click originated from + button, ignoring cell click handler');
            return;
        }
        
        console.log('Cell clicked:', e.currentTarget);
        console.log('Event target classes:', e.currentTarget.className);
        const $cell = $(e.currentTarget);
        
        // Get day number from data-day attribute instead of DOM index
        const dayNumber = parseInt($cell.data('day'));
        const roomId = parseInt($cell.data('room-id'));
        
        console.log('Day number from data-day:', dayNumber, 'Room ID:', roomId, 'Is editable:', $cell.hasClass('editable-cell'));
        console.log('Cell data:', $cell.data());
        
        if ($cell.hasClass('editable-cell')) {
            // Check if edit mode is enabled
            if (!this.isEditModeEnabled()) {
                console.log('Edit mode not enabled, ignoring click');
                this.showError('Please enable edit mode to add new lessons');
                return;
            }
            
            console.log('Opening create modal for day:', dayNumber, 'room:', roomId);
            this.showCreateModal(dayNumber, roomId);
        } else {
            console.log('Cell is not editable');
        }
    }

    handleLessonClick(e) {
        // Check if this is a click on the edit or delete button
        if ($(e.target).hasClass('edit-lesson') || $(e.target).hasClass('delete-lesson') || 
            $(e.target).closest('.edit-lesson').length > 0 || $(e.target).closest('.delete-lesson').length > 0) {
            return; // Let the button handle it
        }
        
        // Prevent rapid clicking only if we're in edit mode (showing edit modal)
        if (this.isEditing && this.currentAction === 'edit') {
            return;
        }
        
        const $lessonBox = $(e.currentTarget);
        const lessonId = $lessonBox.data('lesson-id');
        
        if (lessonId) {
            // Add a small delay to check if this is part of a double-click
            // If double-click happens, this will be cancelled
            clearTimeout(this.clickTimeout);
            this.clickTimeout = setTimeout(() => {
                this.showLessonDetails(lessonId);
            }, 250); // 250ms delay to detect double-click
        }
    }

    handleLessonEdit(e) {
        // Cancel the single-click timeout to prevent details modal from showing
        clearTimeout(this.clickTimeout);
        
        const $lessonBox = $(e.currentTarget);
        const lessonId = $lessonBox.data('lesson-id');
        
        // Check if edit mode is enabled
        if (!this.isEditModeEnabled()) {
            console.log('Edit mode not enabled, ignoring edit click');
            this.showError('Please enable edit mode to edit lessons');
            return;
        }
        
        if (lessonId) {
            this.showEditModal(lessonId);
        }
    }

    // Public method for edit button clicks
    editLesson(lessonId) {
        console.log('editLesson called with ID:', lessonId);
        
        // Check if edit mode is enabled
        if (!this.isEditModeEnabled()) {
            console.log('Edit mode not enabled, ignoring edit');
            this.showError('Please enable edit mode to edit lessons');
            return;
        }
        
        if (lessonId) {
            console.log('Calling showEditModal...');
            this.showEditModal(lessonId);
        } else {
            console.error('No lesson ID provided to editLesson');
        }
    }

    async showCreateModal(dayNumber, roomId = null, startTime = null) {
        try {
            console.log('Creating modal for day:', dayNumber, 'room:', roomId, 'startTime:', startTime);
            console.log('DayNumber type:', typeof dayNumber, 'RoomId type:', typeof roomId, 'StartTime type:', typeof startTime);
            
            // Check if user is authenticated
            if (!this.isUserAuthenticated()) {
                this.showError('Please log in to continue.');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
                return;
            }
            
            // Show loading state
            this.setLoading(true);
            
            // Get available data for the form
            const data = await this.fetchFormData();
            console.log('Form data loaded:', data);
            
            // Set default day (dayNumber is already 1-based)
            data.defaultDay = parseInt(dayNumber);
            console.log('Set data.defaultDay to:', data.defaultDay, 'type:', typeof data.defaultDay);
            
            // Set room ID if provided, or try to get it from the page
            if (roomId && roomId !== 'undefined' && roomId !== null) {
                console.log('Setting room_id in data to:', roomId);
                data.room_id = roomId;
            } else {
                console.log('No roomId provided to showCreateModal, trying to get from page');
                // Try to get room ID from the page URL or other sources
                const urlParts = window.location.pathname.split('/');
                const roomIdFromUrl = urlParts[urlParts.length - 1];
                if (roomIdFromUrl && !isNaN(roomIdFromUrl)) {
                    console.log('Found room ID from URL:', roomIdFromUrl);
                    data.room_id = parseInt(roomIdFromUrl);
                } else {
                    console.log('Could not determine room ID');
                }
            }
            
            // Set start time if provided (from time slot click)
            if (startTime && startTime !== 'undefined' && startTime !== null) {
                console.log('Setting start_time in data to:', startTime);
                data.start_time = startTime;
            }
            
            // Show modal
            this.showModal('create', data);
            
            // Hide loading state
            this.setLoading(false);
        } catch (error) {
            console.error('Error creating modal:', error);
            this.setLoading(false);
            
            // Show specific error message
            if (error.message.includes('Authentication required') || error.message.includes('401')) {
                this.showError('Please log in again to continue.');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else if (error.message.includes('Access denied') || error.message.includes('403')) {
                this.showError('You do not have permission to perform this action.');
            } else {
                this.showError('Failed to load form data: ' + error.message);
            }
        }
    }

    async showEditModal(lessonId) {
        try {
            console.log('showEditModal called with ID:', lessonId);
            
            // Check if user is authenticated
            if (!this.isUserAuthenticated()) {
                this.showError('Please log in to continue.');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
                return;
            }
            
            console.log('Fetching lesson data...');
            const lesson = await this.fetchLesson(lessonId);
            console.log('Lesson data:', lesson);
            
            console.log('Fetching form data...');
            const data = await this.fetchFormData();
            console.log('Form data:', data);
            
            // Merge lesson data with form data
            Object.assign(data, lesson);
            
            // Show modal
            console.log('Calling showModal with edit action...');
            this.showModal('edit', data);
            console.log('Modal should be showing now');
        } catch (error) {
            console.error('Error in showEditModal:', error);
            
            // Show specific error message
            if (error.message.includes('Authentication required') || error.message.includes('401')) {
                this.showError('Please log in again to continue.');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else if (error.message.includes('Access denied') || error.message.includes('403')) {
                this.showError('You do not have permission to edit this lesson.');
            } else if (error.message.includes('not found') || error.message.includes('404')) {
                this.showError('Lesson not found. It may have been deleted.');
                this.refreshTimetable();
            } else {
                this.showError('Failed to load lesson data: ' + error.message);
            }
        }
    }

    async showLessonDetails(lessonId) {
        try {
            const lesson = await this.fetchLesson(lessonId);
            this.showDetailsModal(lesson);
        } catch (error) {
            this.showError('Failed to load lesson details: ' + error.message);
        }
    }

    showDetailsModal(lesson) {
        console.log('Showing details modal for lesson:', lesson);
        
        // Set current action to details
        this.currentAction = 'details';
        
        // Set the lesson ID for deletion
        $('#lessonId').val(lesson.id);
        console.log('Set lesson ID for details modal:', lesson.id);
        
        // Close any existing modals first
        this.closeAllModals();
        
        // Wait a moment to ensure modals are closed
        setTimeout(() => {
            // Create details modal content
            const content = `
                <div class="lesson-details">
                    <h5>${lesson.subject_name || 'No Subject'}</h5>
                    <p><strong>Class:</strong> ${lesson.class_name}</p>
                    <p><strong>Teacher:</strong> ${lesson.teacher_name}</p>
                    <p><strong>Room:</strong> ${lesson.room_name}</p>
                    <p><strong>Time:</strong> ${lesson.start_time} - ${lesson.end_time}</p>
                    <p><strong>Day:</strong> ${lesson.weekday_name}</p>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Enable edit mode to modify this lesson
                        </small>
                    </div>
                </div>
            `;
            
            $('#lessonDetailsContent').html(content);
            
            // Show the modal
            $('#lessonDetailsModal').modal({
                backdrop: 'static',
                keyboard: true,
                show: true
            });
            
            // Add event listener for when modal is closed
            $('#lessonDetailsModal').off('hidden.bs.modal').on('hidden.bs.modal', () => {
                this.currentAction = null;
                console.log('Details modal closed');
            });
            
            console.log('Details modal should be visible now');
        }, 100);
    }

    showModal(action, data) {
        console.log('showModal called with action:', action, 'data:', data);
        console.log('Data contains - room_id:', data.room_id, 'weekday:', data.weekday, 'defaultDay:', data.defaultDay);
        
        this.isEditing = true;
        this.currentAction = action;
        this.currentData = data;
        
        // Initialize manual tracking flag
        // In edit mode, existing end_time is considered "manually set" to prevent override
        this.endTimeManuallyEntered = (action === 'edit' && data.end_time) ? true : false;
        console.log('Manual tracking initialized:', this.endTimeManuallyEntered);
        
        // Populate modal content
        this.populateModal(data);
        
        // Fetch hours tracking after populating modal
        if (data.class_id && data.subject_id) {
            this.fetchHoursTracking();
        }
        
        // Verify that hidden fields were set correctly after population
        setTimeout(() => {
            const roomIdAfter = $('#room_id').val();
            const weekdayAfter = $('#weekday').val();
            console.log('After populateModal - room_id field:', roomIdAfter, 'weekday field:', weekdayAfter);
            
            // If values are missing but were in data, try to set them again
            if (data.room_id && !roomIdAfter) {
                console.warn('room_id was lost! Re-setting from data...');
                $('#room_id').val(parseInt(data.room_id));
            }
            if ((data.weekday || data.defaultDay) && !weekdayAfter) {
                console.warn('weekday was lost! Re-setting from data...');
                $('#weekday').val(parseInt(data.weekday || data.defaultDay));
                this.updateDayIndicator(parseInt(data.weekday || data.defaultDay));
            }
        }, 100);
        
        // Show modal with configuration to prevent accidental closing
        console.log('About to show modal...');
        $('#lessonModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
        console.log('Modal show() called');
        
        // Prevent click events inside modal from bubbling to timetable cells
        // BUT allow button clicks to work properly
        $('#lessonModal .modal-content').off('click.preventBubble').on('click.preventBubble', (e) => {
            // Don't stop propagation for buttons - they need to work!
            if (!$(e.target).is('button') && !$(e.target).closest('button').length) {
                e.stopPropagation();
            }
        });
        
        // Ensure modal stays open
        $('#lessonModal').off('hidden.bs.modal').on('hidden.bs.modal', () => {
            console.log('Modal was closed');
            this.isEditing = false;
            this.currentAction = null;
            this.currentData = null;
            // Reset hours tracking
            this.hoursTrackingData = null;
            $('#modal-hours-tracking-container').hide();
            // Reset collapse state
            $('#modal-hours-tracking-collapse').collapse('hide');
            // Clear all errors when modal is closed
            this.clearAllErrors();
        });
        
        // Setup hours tracking toggle handlers
        this.setupHoursTrackingToggle();
        
        // Focus first input
        setTimeout(() => {
            $('#lessonForm input:first').focus();
        }, 500);
    }

    setupHoursTrackingToggle() {
        // Remove existing handlers to prevent duplicates
        $('#modal-hours-tracking-collapse').off('show.bs.collapse hide.bs.collapse shown.bs.collapse hidden.bs.collapse');
        
        // Handle collapse show event (expanding)
        $('#modal-hours-tracking-collapse').on('show.bs.collapse', () => {
            $('#modal-hours-tracking-toggle-text').text('Hide Hours Tracking');
            $('#modal-hours-tracking-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $('#modal-hours-tracking-toggle').attr('aria-expanded', 'true');
            console.log('Hours tracking expanded');
        });
        
        // Handle collapse hide event (collapsing)
        $('#modal-hours-tracking-collapse').on('hide.bs.collapse', () => {
            $('#modal-hours-tracking-toggle-text').text('Show Hours Tracking');
            $('#modal-hours-tracking-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $('#modal-hours-tracking-toggle').attr('aria-expanded', 'false');
            console.log('Hours tracking collapsed');
        });
        
        // After collapse animation completes, close any open Select2 dropdowns and force repositioning
        $('#modal-hours-tracking-collapse').on('shown.bs.collapse hidden.bs.collapse', () => {
            // Close any open Select2 dropdowns - target only the actual select elements
            $('#class_id, #subject_id, #teacher_id').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('close');
                }
            });
            console.log('Select2 dropdowns closed after collapse animation');
        });
    }

    populateModal(data) {
        console.log('Populating modal with data:', data);
        
        // Clear all error displays FIRST
        this.clearAllErrors();
        
        // Populate dropdowns with data from form data FIRST (before setting values)
        if (data.classes) {
            $('#class_id').empty().append('<option value="">-- Select Class --</option>');
            data.classes.forEach(function(cls) {
                $('#class_id').append(`<option value="${cls.id}">${cls.name}</option>`);
            });
        }
        
        if (data.teachers) {
            $('#teacher_id').empty().append('<option value="">-- Select Teacher --</option>');
            data.teachers.forEach(function(teacher) {
                $('#teacher_id').append(`<option value="${teacher.id}">${teacher.name}</option>`);
            });
        }
        
        // Store all teachers for later filtering
        this.allTeachersData = data.teachers || [];
        
        if (data.subjects) {
            $('#subject_id').empty().append('<option value="">-- Select Subject --</option>');
            data.subjects.forEach(function(subject) {
                $('#subject_id').append(`<option value="${subject.id}">${subject.name} (${subject.code})</option>`);
            });
        }
        
        // NOW populate field values AFTER dropdowns are ready
        if (data.id) {
            $('#lessonId').val(data.id);
        }
        
        if (data.class_id) {
            $('#class_id').val(data.class_id);
        }
        
        if (data.subject_id) {
            $('#subject_id').val(data.subject_id);
        }
        
        if (data.teacher_id) {
            $('#teacher_id').val(data.teacher_id);
        }
        
        // Room is a HIDDEN field, not a dropdown - just set the value
        if (data.room_id) {
            console.log('Setting room_id to:', data.room_id, 'Type:', typeof data.room_id);
            const roomIdValue = parseInt(data.room_id);
            $('#room_id').val(roomIdValue);
            console.log('room_id field value after setting:', $('#room_id').val());
            
            // Update room display with room name
            if (data.room_name) {
                $('#selectedRoomName').text(data.room_name);
            } else if (data.rooms) {
                const selectedRoom = data.rooms.find(room => room.id == roomIdValue);
                if (selectedRoom) {
                    $('#selectedRoomName').text(selectedRoom.name);
                } else {
                    $('#selectedRoomName').text('Room ' + roomIdValue);
                }
            } else {
                $('#selectedRoomName').text('Room ' + roomIdValue);
            }
        } else {
            console.log('No room_id provided in data');
            $('#selectedRoomName').text('Not assigned');
            $('#room_id').val(''); // Explicitly clear if no room
        }
        
        // Weekday is also a HIDDEN field
        if (data.weekday) {
            console.log('Setting weekday from data.weekday:', data.weekday, 'Type:', typeof data.weekday);
            const weekdayValue = parseInt(data.weekday);
            $('#weekday').val(weekdayValue);
            console.log('weekday field value after setting:', $('#weekday').val());
            this.updateDayIndicator(weekdayValue);
        } else if (data.defaultDay) {
            console.log('Setting weekday from data.defaultDay:', data.defaultDay, 'Type:', typeof data.defaultDay);
            const weekdayValue = parseInt(data.defaultDay);
            $('#weekday').val(weekdayValue);
            console.log('weekday field value after setting:', $('#weekday').val());
            this.updateDayIndicator(weekdayValue);
        } else {
            console.log('No weekday or defaultDay found in data');
            $('#selectedDayName').text('Not selected');
            $('#weekday').val(''); // Explicitly clear if no day
        }
        
        // Populate times based on mode
        if (this.currentAction === 'edit') {
            // EDIT mode - populate both start and end times
            if (data.start_time) {
                $('#start_time').val(data.start_time);
            }
            
            if (data.end_time) {
                $('#end_time').val(data.end_time);
            }
        } else {
            // CREATE mode - prefill start_time if provided (from time slot click), clear end_time
            if (data.start_time) {
                console.log('Prefilling start_time in CREATE mode:', data.start_time);
                $('#start_time').val(data.start_time);
            } else {
                $('#start_time').val('');
            }
            $('#end_time').val('');
        }
        
        // Reset lesson_type field to default state FIRST
        $('#lesson_type').val('').prop('disabled', false);
        $('#lesson-type-help').text('Select whether this is a lecture or laboratory session');
        
        // Then populate lesson_type if provided (for edit mode)
        if (data.lesson_type) {
            $('#lesson_type').val(data.lesson_type);
        }
        
        // Update modal title
        const title = this.currentAction === 'create' ? 'Create New Class Schedule' : 'Edit Class Schedule';
        $('#lessonModalTitle').text(title);
        
        // Update save button with icon
        const saveText = this.currentAction === 'create' ? '<i class="fas fa-save"></i> Create Schedule' : '<i class="fas fa-save"></i> Update Schedule';
        $('#saveLessonBtn').html(saveText);
        
        // Show/hide delete button
        if (this.currentAction === 'create') {
            $('#deleteLessonBtn').hide();
        } else {
            $('#deleteLessonBtn').show();
        }
        
        // Reinitialize Select2
        $('.select2').select2({
            dropdownParent: $('#lessonModal')
        });
        
        // Add subject change handlers for filtering teachers and rooms
        this.attachSubjectChangeHandlers();
        
        // Add lesson type change handler
        this.attachLessonTypeHandlers();
        
        // Add time change handlers for duration validation
        this.attachTimeChangeHandlers();
        
        // Store subjects data for later use
        this.subjectsData = data.subjects || [];
        
        // Update lesson_type field state based on subject scheduling mode
        if (data.subject_id) {
            this.updateLessonTypeField(data.subject_id);
            // Also filter teachers for the selected subject
            this.filterTeachersForSubject(data.subject_id, data.teacher_id);
        }
        
        // Trigger duration suggestion if start_time was prefilled (from time slot click)
        // This ensures end_time gets auto-calculated for pure lab/lecture subjects
        if (this.currentAction === 'create' && data.start_time) {
            console.log('Start time was prefilled, triggering duration suggestion after modal setup');
            // Use setTimeout to ensure all handlers are attached and hours tracking is loaded
            setTimeout(() => {
                this.suggestDuration();
            }, 300);
        }
    }
    
    async filterTeachersForSubject(subjectId, selectedTeacherId = null) {
        try {
            console.log('Filtering teachers for subject:', subjectId, 'Selected teacher:', selectedTeacherId);
            
            const response = await fetch(`/admin/lessons/get-teachers-for-subject?subject_id=${subjectId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Failed to fetch teachers for subject:', response.status, errorText);
                throw new Error(`Failed to fetch teachers: ${response.status}`);
            }
            
            const teachersData = await response.json();
            console.log('Filtered teachers for subject:', teachersData.teachers);
            
            // Update teacher dropdown with filtered list
            this.updateDropdown('#teacher_id', teachersData.teachers, '-- Select Teacher --');
            
            // Restore selected teacher if provided
            if (selectedTeacherId) {
                $('#teacher_id').val(selectedTeacherId);
            }
        } catch (error) {
            console.error('Error filtering teachers for subject:', error);
            this.showError('Failed to load teachers. Please try again.');
        }
    }

    updateDayIndicator(weekday) {
        console.log('updateDayIndicator called with weekday:', weekday, 'type:', typeof weekday);
        
        const dayNames = {
            1: 'Monday',
            2: 'Tuesday', 
            3: 'Wednesday',
            4: 'Thursday',
            5: 'Friday',
            6: 'Saturday',
            7: 'Sunday'
        };
        
        const dayName = dayNames[weekday] || 'Unknown';
        console.log('Day name resolved to:', dayName);
        console.log('Setting selectedDayName element text to:', dayName);
        $('#selectedDayName').text(dayName);
        console.log('selectedDayName element text is now:', $('#selectedDayName').text());
    }

    attachSubjectChangeHandlers() {
        // Remove existing handlers to prevent duplicates
        $('#subject_id').off('change.subjectFilter');
        $('#class_id').off('change.classFilter');
        
        // Add event handlers for class/subject changes to refresh hours tracking
        $('#class_id, #subject_id').off('change.hoursTracking').on('change.hoursTracking', () => {
            this.fetchHoursTracking();
        });
        
        // Add subject change handler
        $('#subject_id').on('change.subjectFilter', async (e) => {
            const subjectId = $(e.target).val();
            console.log('Subject changed to:', subjectId);
            
            if (subjectId) {
                try {
                    // Show loading state
                    $('#teacher_id').prop('disabled', true);
                    
                    // Show loading indicators
                    $('#teacher_id').html('<option value="">Loading teachers...</option>');
                    
                    // Always fetch filtered teachers (room is always auto-set now)
                    const teachersResponse = await fetch(`/admin/lessons/get-teachers-for-subject?subject_id=${subjectId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    if (!teachersResponse.ok) {
                        throw new Error(`Failed to fetch teachers: ${teachersResponse.status}`);
                    }
                    
                    const teachersData = await teachersResponse.json();
                    console.log('Filtered teachers:', teachersData.teachers);
                    
                    // Update teacher dropdown
                    this.updateDropdown('#teacher_id', teachersData.teachers, '-- Select Teacher --');
                    
                    console.log('Room is auto-set, skipping room filtering');
                    
                    // Auto-select lesson type based on subject scheduling mode
                    this.updateLessonTypeField(subjectId);
                    
                } catch (error) {
                    console.error('Error filtering teachers:', error);
                    
                    // Show error messages in dropdowns
                    $('#teacher_id').html('<option value="">Error loading teachers</option>');
                    
                    this.showError('Failed to filter teachers. Please try again.');
                } finally {
                    // Re-enable teacher dropdown
                    $('#teacher_id').prop('disabled', false);
                }
            } else {
                // If no subject selected, reset to all options
                this.resetDropdownsToAll();
                $('#lesson_type').val('').prop('disabled', false);
            }
        });
        
        // Hours tracking display removed from inline modal
    }

    updateDropdown(selector, options, placeholder) {
        const $select = $(selector);
        const currentValue = $select.val();
        
        // Clear existing options
        $select.empty();
        $select.append(`<option value="">${placeholder}</option>`);
        
        // Add new options
        if (options && typeof options === 'object') {
            if (options.no_teachers) {
                // Special case for no teachers message
                $select.append(`<option value="" disabled>${options.no_teachers}</option>`);
            } else {
                Object.entries(options).forEach(([value, text]) => {
                    $select.append(`<option value="${value}">${text}</option>`);
                });
            }
        }
        
        // Try to restore previous selection if it still exists
        if (currentValue && $select.find(`option[value="${currentValue}"]`).length > 0) {
            $select.val(currentValue);
        }
        
        // Trigger Select2 update
        $select.trigger('change');
    }

    resetDropdownsToAll() {
        // Reset teacher dropdown to all teachers
        const allTeachers = this.currentData?.teachers || {};
        this.updateDropdown('#teacher_id', allTeachers, '-- Select Teacher --');
        
        console.log('Room is auto-set, skipping room reset');
    }

    async saveLesson() {
        if (!this.validateForm()) {
            return;
        }
        
        try {
            const formData = this.getFormData();
            const url = this.currentAction === 'create' 
                ? '/admin/lessons/inline' 
                : `/admin/lessons/${formData.id}/inline`;
            
            const method = this.currentAction === 'create' ? 'POST' : 'PUT';
            
            // Check for conflicts
            const conflicts = await this.conflictChecker.checkConflicts(formData);
            if (conflicts.length > 0) {
                await this.showConflictWarning(conflicts);
                return;
            }
            
            // Show loading
            this.setLoading(true);
            
            // Send request
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                // Handle validation errors
                if (response.status === 422 && result.messages) {
                    this.showValidationErrors(result.messages);
                    return;
                }
                
                // Handle conflict errors
                if (response.status === 409 && result.conflicts) {
                    await this.showConflictWarning(result.conflicts);
                    return;
                }
                
                // Handle other errors
                throw new Error(result.message || `Server error: ${response.status}`);
            }
            
            // Success - use server message directly
            this.showSuccess(result.message || 'Schedule saved successfully!');
            this.closeModal();
            
            // Delay reload slightly so user can see the success message
            setTimeout(() => {
                this.refreshTimetable();
            }, 1500); // 1.5 second delay
            
        } catch (error) {
            console.error('Save lesson error:', error);
            this.showError(`Failed to ${this.currentAction} class schedule: ${error.message}`);
        } finally {
            this.setLoading(false);
        }
    }


    showValidationErrors(errors) {
        // Clear previous errors
        this.clearAllErrors();
        
        let firstErrorField = null;
        
        // Show field-specific errors ONLY (inline below each field)
        Object.keys(errors).forEach(field => {
            let $field = $(`#${field}`);
            let $feedback = $field.siblings('.invalid-feedback');
            
            // Special handling for duration_hours - show error on end_time field
            if (field === 'duration_hours' && $field.length === 0) {
                $field = $('#end_time');
                $feedback = $field.siblings('.invalid-feedback');
            }
            
            const errorMessage = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            
            if ($field.length) {
                $field.addClass('is-invalid');
                $feedback.text(errorMessage).addClass('show').show();
                
                // Track first error field for scrolling
                if (!firstErrorField) {
                    firstErrorField = $field;
                }
            }
        });
        
        // Scroll to first error field for better UX
        if (firstErrorField) {
            firstErrorField[0].scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            // Focus on first error field
            setTimeout(() => {
                firstErrorField.focus();
            }, 300);
        }
    }

    clearAllErrors() {
        // Clear field-specific errors (inline errors only)
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').removeClass('show').hide();
        
        // Hide and clear conflict warning section
        $('#conflictWarning').hide();
        $('#conflictList').empty();
        
        // Reset hours tracking section
        $('#hours-tracking-modal').empty();
        $('#hoursTrackingSection').collapse('hide');
        $('#hoursTrackingIcon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        $('#hoursTrackingToggle').attr('aria-expanded', 'false');
    }

    buildOrGetDeleteModal() {
        if ($('#inlineDeleteModal').length) return $('#inlineDeleteModal');
        const modalHtml = `
<div class="modal fade" id="inlineDeleteModal" tabindex="-1" role="dialog" aria-labelledby="inlineDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="inlineDeleteModalLabel"><i class="fas fa-trash-alt mr-1"></i> Confirm Delete Class Schedule</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>You're about to delete the following class schedule:</p>
        <ul class="list-unstyled mb-0">
          <li><strong>ID:</strong> <span id="inlineDeleteLessonId"></span></li>
          <li><strong>Class:</strong> <span id="inlineDeleteLessonClass"></span></li>
          <li><strong>Teacher:</strong> <span id="inlineDeleteLessonTeacher"></span></li>
          <li><strong>Subject:</strong> <span id="inlineDeleteLessonSubject"></span></li>
          <li><strong>Room:</strong> <span id="inlineDeleteLessonRoom"></span></li>
          <li><strong>When:</strong> <span id="inlineDeleteLessonWhen"></span></li>
        </ul>
        <div class="alert alert-warning mt-3">
          <i class="fas fa-exclamation-triangle mr-1"></i> This action cannot be undone.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
        <button type="button" class="btn btn-danger" id="inlineConfirmDeleteBtn"><i class="fas fa-trash-alt"></i> Delete</button>
      </div>
    </div>
  </div>
</div>`;
        $('body').append(modalHtml);
        return $('#inlineDeleteModal');
    }

    async showDeleteConfirmation(details) {
        const $modal = this.buildOrGetDeleteModal();
        $('#inlineDeleteLessonId').text(details.id || '');
        $('#inlineDeleteLessonClass').text(details.className || 'No Class');
        $('#inlineDeleteLessonTeacher').text(details.teacher || 'No Teacher');
        $('#inlineDeleteLessonSubject').text(details.subject || 'No Subject');
        $('#inlineDeleteLessonRoom').text(details.room || 'No Room');
        $('#inlineDeleteLessonWhen').text((details.day ? details.day + ' ' : '') + (details.time || ''));

        return new Promise((resolve) => {
            let resolved = false;
            $modal.modal('show');
            $('#inlineConfirmDeleteBtn').off('click.inlineConfirm').one('click.inlineConfirm', () => {
                if (resolved) return;
                resolved = true;
                $modal.modal('hide');
                resolve(true);
            });
            $modal.off('hidden.bs.modal.inline').one('hidden.bs.modal.inline', () => {
                if (resolved) return;
                resolved = true;
                resolve(false);
            });
        });
    }

    async deleteLesson() {
        const lessonId = $('#lessonId').val();
        console.log('deleteLesson called with lessonId:', lessonId);
        
        // Always fetch lesson details to ensure accurate data in delete confirmation
        let details = null;
        
        try {
            console.log('Fetching lesson details for delete confirmation...');
            const lesson = await this.fetchLesson(lessonId);
            console.log('Fetched lesson data:', lesson);
            
            details = {
                id: lessonId,
                className: lesson.class_name || 'No Class',
                teacher: lesson.teacher_name || 'No Teacher',
                subject: lesson.subject_name || 'No Subject',
                day: lesson.weekday_name || '',
                time: `${lesson.start_time || ''} - ${lesson.end_time || ''}`,
                room: lesson.room_name || 'No Room'
            };
            
            console.log('Formatted details for modal:', details);
        } catch (e) {
            console.error('Failed to fetch lesson details:', e);
            this.showError('Failed to load lesson details. Please try again.');
            return;
        }

        const confirmed = await this.showDeleteConfirmation(details);
        if (!confirmed) return;
        
        try {
            // Show loading
            this.setLoading(true);
            
            const deleteUrl = `/admin/lessons/${lessonId}/inline`;
            console.log('Sending DELETE request to:', deleteUrl);
            console.log('Lesson ID type:', typeof lessonId, 'Value:', lessonId);
            console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
            
            // Send delete request
            const response = await fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                credentials: 'same-origin'
            });
            
            console.log('Response status:', response.status);
            console.log('Response status text:', response.statusText);
            console.log('Response headers:', response.headers);
            console.log('Response URL:', response.url);
            
            const result = await response.json();
            console.log('Response result:', result);
            
            if (!response.ok) {
                console.error('Delete request failed:', result);
                
                // Handle specific error types
                if (response.status === 403) {
                    throw new Error('Permission denied: You do not have permission to delete lessons');
                } else if (response.status === 404) {
                    // Treat as already deleted; show success and refresh
                    this.showSuccess('Lesson was already removed. Timetable will be refreshed.');
                    this.closeModal();
                    this.refreshTimetable();
                    return;
                } else {
                    throw new Error(result.message || `Server error: ${response.status}`);
                }
            }
            
            // Success - use previously prepared details to avoid undefined vars
            const info = `(${details.day || ''}, ${details.time || ''}, ${details.className || ''}, ${details.teacher || ''}, ${details.subject || ''})`;
            this.showSuccess(`Lesson deleted successfully! ${info}`);
            this.closeModal();
            this.refreshTimetable();
            
        } catch (error) {
            console.error('Delete lesson error:', error);
            this.showError(`Failed to delete lesson: ${error.message}`);
        } finally {
            this.setLoading(false);
        }
    }

    cancelEdit() {
        this.closeModal();
    }

    closeDetailsModal() {
        $('#lessonDetailsModal').modal('hide');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    }

    closeModal() {
        this.isEditing = false;
        this.currentLesson = null;
        this.currentAction = null;
        this.currentData = null;
        
        // Close all modals properly
        this.closeAllModals();
    }

    getFormData() {
        // Enable lesson_type field before getting value (in case it's disabled)
        const $lessonType = $('#lesson_type');
        const wasDisabled = $lessonType.prop('disabled');
        $lessonType.prop('disabled', false);
        
        const formData = {
            id: $('#lessonId').val(),
            class_id: $('#class_id').val(),
            teacher_id: $('#teacher_id').val(),
            room_id: $('#room_id').val(),
            subject_id: $('#subject_id').val(),
            lesson_type: $lessonType.val(),
            weekday: $('#weekday').val(),
            start_time: $('#start_time').val(),
            end_time: $('#end_time').val()
        };
        
        // Restore disabled state
        if (wasDisabled) {
            $lessonType.prop('disabled', true);
        }
        
        console.log('Form data being sent:', formData);
        return formData;
    }

    validateForm() {
        let isValid = true;
        const requiredFields = ['class_id', 'teacher_id', 'room_id', 'subject_id', 'lesson_type', 'start_time', 'end_time'];
        
        // Clear previous validation errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        requiredFields.forEach(field => {
            const $field = $(`#${field}`);
            const $feedback = $field.siblings('.invalid-feedback');
            
            if (!$field.val()) {
                $field.addClass('is-invalid');
                $feedback.text(this.getFieldErrorMessage(field)).addClass('show').show();
                isValid = false;
            } else {
                $field.removeClass('is-invalid');
                $feedback.text('').removeClass('show').hide();
            }
        });
        
        // Validate weekday separately since it's hidden
        const weekday = $('#weekday').val();
        if (!weekday) {
            this.showError('Day is required. Please click on a valid day column.');
            isValid = false;
        }
        
        // Time validation
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        
        if (startTime && endTime) {
            const start = new Date(`2000-01-01 ${startTime}`);
            const end = new Date(`2000-01-01 ${endTime}`);
            
            if (start >= end) {
                $('#end_time').addClass('is-invalid');
                $('#end_time').siblings('.invalid-feedback').text('End time must be after start time').addClass('show').show();
                this.showError('End time must be after start time');
                isValid = false;
            } else {
                $('#end_time').removeClass('is-invalid');
                $('#end_time').siblings('.invalid-feedback').text('').removeClass('show').hide();
            }
        }
        
        return isValid;
    }

    getFieldErrorMessage(field) {
        const messages = {
            'class_id': 'Please select a class',
            'teacher_id': 'Please select a teacher',
            'room_id': 'Please select a room',
            'subject_id': 'Please select a subject',
            'start_time': 'Please enter a start time',
            'end_time': 'Please enter an end time'
        };
        return messages[field] || 'This field is required';
    }

    async fetchFormData() {
        try {
            console.log('Fetching form data...');
            const response = await fetch('/admin/lessons/form-data', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                credentials: 'same-origin'
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Form data fetch failed:', response.status, errorText);
                
                if (response.status === 401) {
                    throw new Error('Authentication required. Please log in again.');
                } else if (response.status === 403) {
                    throw new Error('Access denied. You do not have permission to access this resource.');
                } else {
                    throw new Error(`Failed to fetch form data: ${response.status} ${response.statusText}`);
                }
            }
            
            const data = await response.json();
            console.log('Form data received:', data);
            return data;
        } catch (error) {
            console.error('Error in fetchFormData:', error);
            throw error;
        }
    }

    async fetchLesson(lessonId) {
        try {
            const response = await fetch(`/admin/lessons/${lessonId}/details`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Lesson fetch failed:', response.status, errorText);
                
                if (response.status === 401) {
                    throw new Error('Authentication required. Please log in again.');
                } else if (response.status === 403) {
                    throw new Error('Access denied. You do not have permission to view this lesson.');
                } else if (response.status === 404) {
                    throw new Error('Lesson not found. It may have been deleted.');
                } else {
                    throw new Error(`Failed to fetch lesson: ${response.status} ${response.statusText}`);
                }
            }
            
            return await response.json();
        } catch (error) {
            console.error('Error in fetchLesson:', error);
            throw error;
        }
    }

    updateLessonTypeField(subjectId) {
        const subject = this.subjectsData.find(s => s.id == subjectId);
        const $lessonTypeField = $('#lesson_type');
        
        if (!subject || !subject.scheduling_mode) {
            // No subject or no scheduling mode - reset to default
            $lessonTypeField.val('').prop('disabled', false);
            $('#lesson-type-help').text('Select whether this is a lecture or laboratory session');
            return;
        }
        
        if (subject.scheduling_mode === 'lab') {
            // Pure Lab - auto-select laboratory and lock
            // IMPORTANT: Enable field first, change value, then disable for proper UI update
            $lessonTypeField.prop('disabled', false);
            $lessonTypeField.val('laboratory');
            $lessonTypeField.prop('disabled', true);
            $('#lesson-type-help').html('<strong>Laboratory:</strong> Duration between 3 to 5 hours. <span class="text-info">This subject is Pure Laboratory. Lesson type is automatically set.</span>');
            
            // Trigger duration suggestion for auto-filling end_time
            this.suggestDuration();
        } else if (subject.scheduling_mode === 'lecture') {
            // Pure Lecture - auto-select lecture and lock
            // IMPORTANT: Enable field first, change value, then disable for proper UI update
            $lessonTypeField.prop('disabled', false);
            $lessonTypeField.val('lecture');
            $lessonTypeField.prop('disabled', true);
            $('#lesson-type-help').html('<strong>Lecture:</strong> Duration between 1 to 3 hours (30-minute intervals). <span class="text-info">This subject is Pure Lecture. Lesson type is automatically set.</span>');
            
            // Trigger duration suggestion for auto-filling end_time
            this.suggestDuration();
        } else if (subject.scheduling_mode === 'flexible') {
            // Flexible - enable field but keep existing value (for edit mode)
            $lessonTypeField.prop('disabled', false);
            $('#lesson-type-help').text('Select whether this is a lecture or laboratory session');
            // Note: Don't change the value here - it's already set in populateModal for edit mode
        }
    }
    
    renderHoursTracking(data) {
        console.log('renderHoursTracking called with data:', data);
        
        // Ensure progress is capped at 100% for display, but can exceed for over-scheduling detection
        const actualProgress = data.progress || 0;
        const displayProgress = Math.min(actualProgress, 100);
        const scheduledHours = parseFloat(data.scheduled_hours || 0).toFixed(1);
        const remainingHours = data.remaining_hours !== undefined ? parseFloat(data.remaining_hours).toFixed(1) : parseFloat(data.total_hours).toFixed(1);
        const totalHours = parseFloat(data.total_hours || 0).toFixed(1);
        
        // Determine status based on progress and remaining hours
        let progressBarClass = 'bg-warning';
        let statusIcon = 'fa-clock';
        let statusText = 'In Progress';
        let statusClass = 'text-warning';
        
        if (remainingHours < 0 || actualProgress > 100) {
            progressBarClass = 'bg-danger';
            statusIcon = 'fa-exclamation-triangle';
            statusText = 'Over-scheduled';
            statusClass = 'text-danger';
        } else if (actualProgress >= 100) {
            progressBarClass = 'bg-success';
            statusIcon = 'fa-check-circle';
            statusText = 'Complete';
            statusClass = 'text-success';
        } else if (actualProgress >= 75) {
            progressBarClass = 'bg-info';
            statusIcon = 'fa-tasks';
            statusText = 'Near Complete';
            statusClass = 'text-info';
        } else if (actualProgress === 0) {
            progressBarClass = 'bg-secondary';
            statusIcon = 'fa-hourglass-start';
            statusText = 'Not Started';
            statusClass = 'text-secondary';
        }
        
        // Build lecture/lab breakdown if available
        let breakdownHtml = '';
        if (data.lecture_hours || data.lab_hours) {
            const schedulingMode = data.scheduling_mode || 'flexible';
            
            if (schedulingMode === 'flexible' || schedulingMode === 'lecture') {
                const lectureTotal = parseFloat(data.lecture_hours?.total || 0).toFixed(1);
                const lectureScheduled = parseFloat(data.lecture_hours?.scheduled || 0).toFixed(1);
                const lectureRemaining = parseFloat(data.lecture_hours?.remaining || 0).toFixed(1);
                
                if (lectureTotal > 0) {
                    breakdownHtml += `
                        <div class="col-6">
                            <small class="text-muted d-block"><i class="fas fa-chalkboard-teacher mr-1"></i>Lecture</small>
                            <small class="font-weight-bold">${lectureScheduled}h / ${lectureTotal}h</small>
                            <small class="text-muted d-block">(${lectureRemaining}h remaining)</small>
                        </div>
                    `;
                }
            }
            
            if (schedulingMode === 'flexible' || schedulingMode === 'lab') {
                const labTotal = parseFloat(data.lab_hours?.total || 0).toFixed(1);
                const labScheduled = parseFloat(data.lab_hours?.scheduled || 0).toFixed(1);
                const labRemaining = parseFloat(data.lab_hours?.remaining || 0).toFixed(1);
                
                if (labTotal > 0) {
                    breakdownHtml += `
                        <div class="col-6">
                            <small class="text-muted d-block"><i class="fas fa-flask mr-1"></i>Laboratory</small>
                            <small class="font-weight-bold">${labScheduled}h / ${labTotal}h</small>
                            <small class="text-muted d-block">(${labRemaining}h remaining)</small>
                        </div>
                    `;
                }
            }
            
            if (breakdownHtml) {
                breakdownHtml = `
                    <hr class="my-2">
                    <div class="row">
                        ${breakdownHtml}
                    </div>
                `;
            }
        }
        
        const html = `
            <div class="alert alert-info" style="animation: fadeIn 0.3s ease-in;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-chart-line mr-2"></i>Hours Tracking for Selected Class
                    </h6>
                    <span class="badge badge-pill ${statusClass}">
                        <i class="fas ${statusIcon} mr-1"></i>${statusText}
                    </span>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4">
                        <small class="text-muted d-block">Total Required</small>
                        <h6 class="mb-0 font-weight-bold text-primary">${totalHours}h</h6>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">Scheduled</small>
                        <h6 class="mb-0 font-weight-bold text-info">${scheduledHours}h</h6>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">Remaining</small>
                        <h6 class="mb-0 font-weight-bold ${remainingHours < 0 ? 'text-danger' : 'text-success'}">
                            ${remainingHours}h
                        </h6>
                    </div>
                </div>
                
                <div class="progress" style="height: 20px; position: relative;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated ${progressBarClass}" 
                         role="progressbar" 
                         style="width: ${displayProgress}%; transition: width 0.6s ease;" 
                         aria-valuenow="${displayProgress}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <span class="font-weight-bold">${actualProgress.toFixed(1)}%</span>
                    </div>
                </div>
                
                ${breakdownHtml}
            </div>
        `;
        
        // Update the hours tracking content
        $('#hours-tracking-modal').html(html).show();
        
        // Automatically expand the collapse section to show hours tracking
        $('#hoursTrackingSection').collapse('show');
        
        // Update chevron icon to point up
        $('#hoursTrackingIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        $('#hoursTrackingToggle').attr('aria-expanded', 'true');
        
        console.log('Hours tracking rendered and section expanded');
    }
    
    attachLessonTypeHandlers() {
        $('#lesson_type').off('change.lessonType').on('change.lessonType', () => {
            this.updateLessonTypeHelp();
            // Always try to suggest duration when lesson type changes
            this.suggestDuration();
            this.validateDuration();
        });
    }
    
    updateLessonTypeHelp() {
        const lessonType = $('#lesson_type').val();
        let helpText = 'Select whether this is a lecture or laboratory session';
        
        if (lessonType === 'laboratory') {
            helpText = '<strong>Laboratory:</strong> Duration between 3 to 5 hours. Default 3-hour session is advised.';
        } else if (lessonType === 'lecture') {
            helpText = '<strong>Lecture:</strong> Duration between 1 to 3 hours (30-minute intervals). Default 1-hour session is advised.';
        }
        
        $('#lesson-type-help').html(helpText);
    }
    
    attachTimeChangeHandlers() {
        // Debounced validation to reduce excessive calls during rapid changes
        const debouncedValidation = this.debounce(() => {
            this.validateDuration();
        }, 300);
        
        const debouncedHoursTracking = this.debounce(() => {
            this.updateHoursTrackingDisplay();
        }, 300);
        
        $('#start_time, #end_time').off('change.timeValidation').on('change.timeValidation', debouncedValidation);
        
        // Add handler for start_time to trigger duration suggestion
        $('#start_time').off('change.durationSuggest').on('change.durationSuggest', () => {
            this.suggestDuration();
        });
        
        // Handle datetimepicker change events (dp.change is triggered by the picker widget)
        $('#start_time').off('dp.change.durationSuggest').on('dp.change.durationSuggest', () => {
            console.log('Start time changed via datetimepicker');
            this.suggestDuration();
            debouncedValidation();
        });
        
        $('#end_time').off('dp.change.validation').on('dp.change.validation', () => {
            console.log('End time changed via datetimepicker');
            debouncedValidation();
        });
        
        // Update hours tracking when end_time changes (debounced)
        $('#end_time').off('change.hoursTracking dp.change.hoursTracking').on('change.hoursTracking dp.change.hoursTracking', debouncedHoursTracking);
        
        // Track manual changes to end_time
        $('#end_time').off('input.manualTracking').on('input.manualTracking', () => {
            // Mark as manually entered if user types directly
            this.endTimeManuallyEntered = true;
            console.log('End time marked as manually entered');
        });
    }
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    suggestDuration() {
        const lessonType = $('#lesson_type').val();
        const startTime = $('#start_time').val();
        const currentEndTime = $('#end_time').val();

        // ENHANCED BEHAVIOR WITH INTELLIGENT CAPPING:
        // 1. Only suggest if start_time is populated
        // 2. Recalculate if end_time was auto-filled (not manually entered)
        // 3. Don't override manually entered end_time
        // 4. Cap duration based on remaining hours
        // 5. Don't auto-fill if no hours remaining
        
        if (!startTime) {
            console.log('Duration suggestion skipped: start_time not populated');
            return;
        }
        
        if (!lessonType) {
            console.log('Duration suggestion skipped: lesson_type not selected');
            return;
        }
        
        // Check if end_time was manually entered
        if (currentEndTime && this.endTimeManuallyEntered) {
            console.log('Duration suggestion skipped: end_time was manually entered by user');
            return;
        }
        
        // Get remaining hours from hours tracking data
        let remainingHours = null;
        if (this.hoursTrackingData) {
            if (lessonType === 'lecture') {
                remainingHours = this.hoursTrackingData.lecture_hours.remaining;
            } else if (lessonType === 'laboratory') {
                remainingHours = this.hoursTrackingData.lab_hours.remaining;
            }
        }
        
        // If no hours remaining, don't auto-fill
        if (remainingHours !== null && remainingHours === 0) {
            console.log('Duration suggestion skipped: No remaining hours for this lesson type');
            $('#end_time').val('');
            this.updateHoursTrackingDisplay();
            return;
        }
        
        // Determine default duration
        let defaultDuration = lessonType === 'laboratory' ? 3 : 1;
        
        // Apply intelligent capping
        let suggestedDuration = defaultDuration;
        if (remainingHours !== null && remainingHours < defaultDuration) {
            // Cap to remaining hours
            suggestedDuration = remainingHours;
            console.log(`Duration capped to remaining hours: ${suggestedDuration}h (default was ${defaultDuration}h)`);
        }
        
        try {
            // Calculate end time
            const start = moment(startTime, 'h:mm A');
            const suggestedEnd = start.clone().add(suggestedDuration, 'hours');
            $('#end_time').val(suggestedEnd.format('h:mm A'));
            this.endTimeManuallyEntered = false; // Mark as auto-filled
            
            const typeLabel = lessonType === 'laboratory' ? 'Laboratory' : 'Lecture';
            console.log(`${typeLabel}: Auto-suggested ${suggestedDuration}-hour duration (start + ${suggestedDuration}h)`);
            
            // Update hours tracking display
            this.updateHoursTrackingDisplay();
            
            // Trigger validation after auto-filling
            this.validateDuration();
        } catch (error) {
            console.error('Duration suggestion error:', error);
        }
    }
    
    /**
     * Fetch hours tracking data from server
     */
    fetchHoursTracking() {
        const classId = $('#class_id').val();
        const subjectId = $('#subject_id').val();
        
        if (!classId || !subjectId) {
            $('#modal-hours-tracking-container').hide();
            this.hoursTrackingData = null;
            return;
        }
        
        console.log('Fetching hours tracking for class:', classId, 'subject:', subjectId);
        
        // Determine exclude_lesson_id for edit mode
        const excludeLessonId = this.currentAction === 'edit' && this.currentData ? this.currentData.id : null;
        
        $.ajax({
            url: '/admin/lessons/hours-tracking',
            method: 'GET',
            data: {
                class_id: classId,
                subject_id: subjectId,
                exclude_lesson_id: excludeLessonId
            },
            success: (response) => {
                if (response.success) {
                    this.hoursTrackingData = response;
                    console.log('Hours tracking data received:', this.hoursTrackingData);
                    this.updateHoursTrackingDisplay();
                    $('#modal-hours-tracking-container').show();
                } else {
                    console.error('Hours tracking error:', response.error);
                    $('#modal-hours-tracking-container').hide();
                    this.hoursTrackingData = null;
                }
            },
            error: (xhr, status, error) => {
                console.error('Failed to fetch hours tracking:', error);
                $('#modal-hours-tracking-container').hide();
                this.hoursTrackingData = null;
            }
        });
    }
    
    /**
     * Update hours tracking display with current data
     */
    updateHoursTrackingDisplay() {
        if (!this.hoursTrackingData) {
            return;
        }
        
        const lectureData = this.hoursTrackingData.lecture_hours;
        const labData = this.hoursTrackingData.lab_hours;
        const schedulingMode = this.hoursTrackingData.scheduling_mode;
        
        // Conditionally show/hide sections based on scheduling mode
        if (schedulingMode === 'lab') {
            // Pure lab: Show only lab hours
            $('#modal-lecture-hours-section').hide();
            $('#modal-lab-hours-section').show();
        } else if (schedulingMode === 'lecture') {
            // Pure lecture: Show only lecture hours
            $('#modal-lecture-hours-section').show();
            $('#modal-lab-hours-section').hide();
        } else {
            // Flexible: Show both
            $('#modal-lecture-hours-section').show();
            $('#modal-lab-hours-section').show();
        }
        
        // Calculate current lesson duration
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        let currentDuration = 0;
        
        if (startTime && endTime) {
            const start = moment(startTime, 'h:mm A');
            const end = moment(endTime, 'h:mm A');
            currentDuration = end.diff(start, 'hours', true);
        }
        
        const lessonType = $('#lesson_type').val();
        
        // Update lecture hours display (if visible)
        if (schedulingMode === 'lecture' || schedulingMode === 'flexible') {
            this.updateProgressBar('modal-lecture', lectureData, currentDuration, lessonType === 'lecture');
        }
        
        // Update lab hours display (if visible)
        if (schedulingMode === 'lab' || schedulingMode === 'flexible') {
            this.updateProgressBar('modal-lab', labData, currentDuration, lessonType === 'laboratory');
        }
        
        // Check for errors and update submit button
        this.validateHoursAndUpdateSubmit(currentDuration, lessonType);
        
        // UPDATE TOGGLE BUTTON COLOR based on remaining hours
        const $toggleBtn = $('#modal-hours-tracking-toggle');
        const data = lessonType === 'lecture' ? lectureData : labData;
        
        if (data && data.remaining <= 0) {
            // Hours exceeded or fully scheduled - turn RED
            if (!$toggleBtn.hasClass('hours-exceeded')) {
                console.log('Toggle button: Adding hours-exceeded class (remaining hours: ' + data.remaining + ')');
                $toggleBtn.removeClass('btn-outline-info').addClass('btn-outline-danger hours-exceeded');
            }
        } else {
            // Hours available - keep BLUE
            if ($toggleBtn.hasClass('hours-exceeded')) {
                console.log('Toggle button: Removing hours-exceeded class (remaining hours: ' + data.remaining + ')');
                $toggleBtn.removeClass('btn-outline-danger hours-exceeded').addClass('btn-outline-info');
            }
        }
    }
    
    /**
     * Update individual progress bar
     */
    updateProgressBar(type, data, currentDuration, isCurrentType) {
        const total = data.total;
        const scheduled = data.scheduled;
        const remaining = data.remaining;
        
        // Calculate what the new scheduled would be if this lesson is added
        const projectedScheduled = isCurrentType ? scheduled + currentDuration : scheduled;
        const projectedRemaining = Math.max(0, total - projectedScheduled);
        const progress = total > 0 ? Math.min(100, (projectedScheduled / total) * 100) : 0;
        
        // Update text displays
        $(`#${type}-hours-text`).text(`${projectedScheduled.toFixed(1)}h / ${total}h`);
        $(`#${type}-remaining-text`).text(`${projectedRemaining.toFixed(1)}h remaining`);
        $(`#${type}-progress-text`).text(`${progress.toFixed(0)}%`);
        
        // Update progress bar
        const $progressBar = $(`#${type}-progress-bar`);
        $progressBar.css('width', `${progress}%`);
        $progressBar.attr('aria-valuenow', progress);
        
        // Color coding based on remaining percentage
        $progressBar.removeClass('bg-success bg-warning bg-danger');
        const remainingPercent = total > 0 ? (projectedRemaining / total) * 100 : 0;
        
        if (remainingPercent > 50) {
            $progressBar.addClass('bg-success');
        } else if (remainingPercent >= 20) {
            $progressBar.addClass('bg-warning');
        } else {
            $progressBar.addClass('bg-danger');
        }
        
        console.log(`${type} hours - Total: ${total}, Scheduled: ${scheduled}, Current: ${currentDuration}, Projected: ${projectedScheduled}, Remaining: ${projectedRemaining}`);
    }
    
    /**
     * Validate hours and update submit button state
     */
    validateHoursAndUpdateSubmit(currentDuration, lessonType) {
        if (!this.hoursTrackingData || !lessonType || currentDuration <= 0) {
            $('#modal-hours-error-message').hide();
            $('#modal-hours-info-message').hide();
            return;
        }
        
        const data = lessonType === 'lecture' ? this.hoursTrackingData.lecture_hours : this.hoursTrackingData.lab_hours;
        const remaining = data.remaining;
        const total = data.total;
        const scheduled = data.scheduled;
        
        const $submitBtn = $('#saveLessonBtn');
        
        // Check if duration exceeds remaining hours
        if (currentDuration > remaining) {
            const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
            const errorMsg = `This lesson (${currentDuration.toFixed(1)}h) exceeds remaining ${typeLabel.toLowerCase()} hours (${remaining.toFixed(1)}h). Scheduled: ${scheduled.toFixed(1)}h / Total: ${total}h`;
            
            $('#modal-hours-error-text').text(errorMsg);
            $('#modal-hours-error-message').show();
            $('#modal-hours-info-message').hide();
            
            // Disable submit button
            $submitBtn.prop('disabled', true);
            console.log('Submit disabled: Duration exceeds remaining hours');
        } else if (remaining === 0) {
            const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
            const errorMsg = `No remaining ${typeLabel.toLowerCase()} hours for this class. All ${total}h have been scheduled.`;
            
            $('#modal-hours-error-text').text(errorMsg);
            $('#modal-hours-error-message').show();
            $('#modal-hours-info-message').hide();
            
            // Disable submit button
            $submitBtn.prop('disabled', true);
            console.log('Submit disabled: No remaining hours');
        } else {
            // Valid duration
            $('#modal-hours-error-message').hide();
            
            // Show info message about hours usage
            const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
            const newRemaining = remaining - currentDuration;
            const infoMsg = `This lesson will use ${currentDuration.toFixed(1)}h (${newRemaining.toFixed(1)}h ${typeLabel.toLowerCase()} hours remaining after)`;
            
            $('#modal-hours-info-text').text(infoMsg);
            $('#modal-hours-info-message').show();
            
            // Re-enable submit button (if no other errors)
            $submitBtn.prop('disabled', false);
            console.log('Submit enabled: Valid duration within remaining hours');
        }
    }
    
    validateDuration() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        const lessonType = $('#lesson_type').val();
        
        // Clear previous duration errors
        $('#duration_error').remove();
        $('#end_time').removeClass('is-invalid');
        
        if (!startTime || !endTime || !lessonType) {
            return true;
        }
        
        try {
            const start = moment(startTime, 'h:mm A');
            const end = moment(endTime, 'h:mm A');
            
            if (!start.isValid() || !end.isValid()) {
                return true;
            }
            
            const durationHours = end.diff(start, 'hours', true);
            const durationMinutes = end.diff(start, 'minutes');
            
            let errorMessage = '';
            
            if (lessonType === 'laboratory') {
                if (durationHours < 3) {
                    errorMessage = 'Laboratory lessons must be at least 3 hours long.';
                } else if (durationHours > 5) {
                    errorMessage = 'Laboratory lessons cannot exceed 5 hours.';
                }
            } else if (lessonType === 'lecture') {
                if (durationHours < 1) {
                    errorMessage = 'Lecture lessons must be at least 1 hour long.';
                } else if (durationHours > 3) {
                    errorMessage = 'Lecture lessons cannot exceed 3 hours.';
                } else if (durationMinutes % 30 !== 0) {
                    errorMessage = 'Lecture lessons must be in 30-minute intervals (e.g., 1h, 1.5h, 2h, 2.5h, 3h).';
                }
            }
            
            if (errorMessage) {
                $('#end_time').addClass('is-invalid');
                const $feedback = $('#end_time').siblings('.invalid-feedback');
                $feedback.text(errorMessage).addClass('show').show();
                return false;
            }
            
            return true;
        } catch (error) {
            console.error('Duration validation error:', error);
            return true;
        }
    }

    async refreshTimetable() {
        // Reload the page to refresh the timetable
        window.location.reload();
    }

    setLoading(loading) {
        if (loading) {
            $('#saveLessonBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            $('#deleteLessonBtn').prop('disabled', true);
        } else {
            const saveText = this.currentAction === 'create' ? '<i class="fas fa-save"></i> Create Schedule' : '<i class="fas fa-save"></i> Update Schedule';
            $('#saveLessonBtn').prop('disabled', false).html(saveText);
            $('#deleteLessonBtn').prop('disabled', false);
        }
    }

    showSuccess(message) {
        this.showAlert(message, 'success');
    }

    showError(message) {
        this.showAlert(message, 'danger');
    }

    showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show inline-editing-alert" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        // Remove only inline-editing alerts (not all alerts on the page)
        $('.inline-editing-alert').remove();
        
        // Add new alert to content area (not inside modal)
        $('.content').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.inline-editing-alert').fadeOut(() => {
                $('.inline-editing-alert').remove();
            });
        }, 5000);
    }

    async showConflictWarning(conflicts) {
        console.log('showConflictWarning called with conflicts:', conflicts);
        
        // Enhanced validation modal is always loaded
        try {
            console.log('Showing enhanced validation modal...');
            await window.enhancedValidationModal.show(conflicts, this.getFormData());
            console.log('Enhanced validation modal shown successfully');
        } catch (error) {
            console.error('Error showing enhanced validation modal:', error);
            this.showError('Failed to display conflict warning. Please try again.');
        }
    }

    async saveLessonWithoutConflictCheck() {
        if (!this.validateForm()) {
            return;
        }
        
        try {
            const formData = this.getFormData();
            const url = this.currentAction === 'create' 
                ? '/admin/lessons/inline' 
                : `/admin/lessons/${formData.id}/inline`;
            
            const method = this.currentAction === 'create' ? 'POST' : 'PUT';
            
            // Show loading
            this.setLoading(true);
            
            // Send request (skip conflict check)
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                // Handle validation errors
                if (response.status === 422 && result.messages) {
                    this.showValidationErrors(result.messages);
                    return;
                }
                
                // Handle other errors
                throw new Error(result.message || `Server error: ${response.status}`);
            }
            
            // Success - use server message directly
            this.showSuccess(result.message || 'Schedule saved successfully!');
            this.closeModal();
            
            // Delay reload slightly so user can see the success message
            setTimeout(() => {
                this.refreshTimetable();
            }, 1500); // 1.5 second delay
            
        } catch (error) {
            console.error('Save lesson error:', error);
            this.showError(`Failed to ${this.currentAction} class schedule: ${error.message}`);
        } finally {
            this.setLoading(false);
        }
    }
}

/**
 * Conflict Checker Class
 */
class ConflictChecker {
    async checkConflicts(lessonData) {
        const conflicts = [];
        
        try {
            const response = await fetch('/admin/lessons/check-conflicts', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify(lessonData)
            });
            
            if (response.ok) {
                const result = await response.json();
                return result.conflicts || [];
            } else {
                const errorText = await response.text();
                console.error('Conflict check failed:', response.status, errorText);
                
                if (response.status === 401) {
                    throw new Error('Authentication required. Please log in again.');
                } else if (response.status === 403) {
                    throw new Error('Access denied. You do not have permission to check conflicts.');
                } else {
                    throw new Error(`Failed to check conflicts: ${response.status} ${response.statusText}`);
                }
            }
        } catch (error) {
            console.error('Conflict check failed:', error);
            // Return empty conflicts array on error to allow saving to proceed
            // The server-side validation will catch any real conflicts
            return [];
        }
    }
}

// Initialize the inline editing system
let inlineEditing;
$(document).ready(function() {
    console.log('Document ready, initializing inline editing system...');
    inlineEditing = new InlineEditingSystem();
    
    // Initialize timetable after a short delay to ensure DOM is fully loaded
    setTimeout(() => {
        inlineEditing.initializeTimetable();
        console.log('Inline editing system initialized');
    }, 100);
});