/**
 * Inline Editing System for Laravel School Timetable
 * Phase 3: Google Sheets-like editing experience
 */

class InlineEditingSystem {
    constructor() {
        this.currentLesson = null;
        this.isEditing = false;
        this.conflictChecker = new ConflictChecker();
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeTimetable();
        this.setupTooltips();
    }

    bindEvents() {
        // Click events for timetable cells
        $(document).on('click', '.timetable-day-column', (e) => {
            if (!this.isEditing) {
                this.handleCellClick(e);
            }
        });

        // Click events for lesson boxes
        $(document).on('click', '.class-box', (e) => {
            e.stopPropagation();
            if (!this.isEditing) {
                this.handleLessonClick(e);
            }
        });

        // Double-click to edit
        $(document).on('dblclick', '.class-box', (e) => {
            e.stopPropagation();
            this.handleLessonEdit(e);
        });

        // Timetable cell events
        $(document).on('click', '.editable-cell', (e) => this.handleCellClick(e));
        $(document).on('click', '.editable-lesson', (e) => this.handleLessonClick(e));
        $(document).on('dblclick', '.editable-lesson', (e) => this.handleLessonEdit(e));
        $(document).on('click', '.edit-lesson', (e) => {
            e.stopPropagation();
            const lessonId = $(e.currentTarget).closest('.class-box').data('lesson-id');
            console.log('Edit button clicked, lesson ID:', lessonId);
            this.editLesson(lessonId);
        });
        
        $(document).on('click', '.delete-lesson', (e) => {
            e.stopPropagation();
            const lessonId = $(e.currentTarget).closest('.class-box').data('lesson-id');
            console.log('Delete button clicked, lesson ID:', lessonId);
            
            // Set the lesson ID in the hidden field for deletion
            $('#lessonId').val(lessonId);
            console.log('Set lesson ID for direct deletion:', lessonId);
            
            this.deleteLesson();
        });

        // Modal events
        $(document).on('click', '#saveLessonBtn', () => this.saveLesson());
        $(document).on('click', '#deleteLessonBtn', () => this.deleteLesson());
        $(document).on('click', '#cancelEditBtn', () => this.cancelEdit());
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
                
                // If no field errors remain, hide the validation error banner
                if (!hasFieldErrors) {
                    console.log('All errors cleared - hiding banner and enabling button');
                    $('#validationErrors').slideUp(300);
                    $('#validationErrorList').empty();
                    $('#saveLessonBtn').prop('disabled', false);
                } else {
                    console.log('Still has errors - keeping banner visible');
                }
            }
        });
    }

    initializeTimetable() {
        console.log('Initializing timetable...');
        console.log('Found not-scheduled-box elements:', $('.not-scheduled-box').length);
        console.log('Found class-box elements:', $('.class-box').length);
        console.log('Found add-lesson-btn elements:', $('.add-lesson-btn').length);
        
        // Add edit indicators to empty cells
        $('.not-scheduled-box').addClass('editable-cell');
        $('.not-scheduled-box').attr('data-action', 'create');
        
        // Add edit indicators to lesson boxes
        $('.class-box').addClass('editable-lesson');
        $('.class-box').attr('data-action', 'edit');
        
        // Initialize + buttons (they will be shown/hidden by edit mode)
        // Only hide if edit mode is not enabled
        if (!$('.timetable-day-column').hasClass('edit-mode')) {
            $('.add-lesson-btn').hide(); // Hidden by default
        }
        
        console.log('Editable cells after initialization:', $('.editable-cell').length);
        console.log('Editable lessons after initialization:', $('.editable-lesson').length);
        console.log('Add lesson buttons:', $('.add-lesson-btn').length);
        
        // Debug: Log all editable cells
        $('.editable-cell').each(function(index) {
            console.log(`Editable cell ${index}:`, this, 'Classes:', this.className);
        });
    }

    setupTooltips() {
        // Add tooltips for better UX
        $('.editable-cell').attr('title', 'Enable edit mode to add new lessons');
        $('.editable-lesson').attr('title', 'Click to view details, double-click to edit');
    }

    isEditModeEnabled() {
        // Check if edit mode is enabled by looking for the edit mode class
        return $('.timetable-day-column').hasClass('edit-mode');
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
            this.showLessonDetails(lessonId);
        }
    }

    handleLessonEdit(e) {
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

    async showCreateModal(dayNumber, roomId = null) {
        try {
            console.log('Creating modal for day:', dayNumber, 'room:', roomId);
            console.log('DayNumber type:', typeof dayNumber, 'RoomId type:', typeof roomId);
            
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
        
        this.isEditing = true;
        this.currentAction = action;
        this.currentData = data;
        
        // Populate modal content
        this.populateModal(data);
        
        // Show modal directly
        console.log('About to show modal...');
        $('#lessonModal').modal('show');
        console.log('Modal show() called');
        
        // Ensure modal stays open
        $('#lessonModal').off('hidden.bs.modal').on('hidden.bs.modal', () => {
            console.log('Modal was closed');
            this.isEditing = false;
            this.currentAction = null;
            this.currentData = null;
            // Clear all errors when modal is closed
            this.clearAllErrors();
        });
        
        // Focus first input
        setTimeout(() => {
            $('#lessonForm input:first').focus();
        }, 500);
    }

    editLesson(lessonId) {
        this.showEditModal(lessonId);
    }

    populateModal(data) {
        console.log('Populating modal with data:', data);
        
        // Reset form
        $('#lessonForm')[0].reset();
        
        // Clear all error displays
        this.clearAllErrors();
        
        // Populate fields
        if (data.id) {
            $('#lessonId').val(data.id);
        }
        
        if (data.class_id) {
            $('#class_id').val(data.class_id);
        }
        
        if (data.teacher_id) {
            $('#teacher_id').val(data.teacher_id);
        }
        
        if (data.room_id) {
            console.log('Setting room_id to:', data.room_id);
            $('#room_id').val(data.room_id);
            
            // Update room display with room name
            if (data.room_id && data.rooms) {
                const selectedRoom = data.rooms.find(room => room.id == data.room_id);
                if (selectedRoom) {
                    $('#selectedRoomName').text(selectedRoom.name);
                } else {
                    $('#selectedRoomName').text('Room ' + data.room_id);
                }
            }
        } else {
            console.log('No room_id provided in data');
        }
        
        if (data.subject_id) {
            $('#subject_id').val(data.subject_id);
        }
        
        if (data.weekday) {
            console.log('Setting weekday from data.weekday:', data.weekday);
            $('#weekday').val(data.weekday);
            this.updateDayIndicator(data.weekday);
        } else if (data.defaultDay) {
            console.log('Setting weekday from data.defaultDay:', data.defaultDay);
            $('#weekday').val(data.defaultDay);
            this.updateDayIndicator(data.defaultDay);
        } else {
            console.log('No weekday or defaultDay found in data');
        }
        
        if (data.start_time) {
            $('#start_time').val(data.start_time);
        }
        
        if (data.end_time) {
            $('#end_time').val(data.end_time);
        }
        
        // Populate dropdowns with data from form data
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
        
        if (data.rooms) {
            $('#room_id').empty().append('<option value="">-- Select Room --</option>');
            data.rooms.forEach(function(room) {
                $('#room_id').append(`<option value="${room.id}">${room.name}</option>`);
            });
        }
        
        if (data.subjects) {
            $('#subject_id').empty().append('<option value="">-- Select Subject --</option>');
            data.subjects.forEach(function(subject) {
                $('#subject_id').append(`<option value="${subject.id}">${subject.name} (${subject.code})</option>`);
            });
        }
        
        // Update modal title
        const title = this.currentAction === 'create' ? 'Create New Lesson' : 'Edit Lesson';
        $('#lessonModalTitle').text(title);
        
        // Update save button
        const saveText = this.currentAction === 'create' ? 'Create Lesson' : 'Update Lesson';
        $('#saveLessonBtn').text(saveText);
        
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
            }
        });
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
            
            // Success
            const actionText = this.currentAction === 'create' ? 'created' : 'updated';
            const lessonInfo = this.getLessonInfo(formData);
            this.showSuccess(`✅ Lesson ${actionText} successfully!${lessonInfo}`);
            this.closeModal();
            this.refreshTimetable();
            
        } catch (error) {
            console.error('Save lesson error:', error);
            this.showError(`Failed to ${this.currentAction} lesson: ${error.message}`);
        } finally {
            this.setLoading(false);
        }
    }

    getLessonInfo(formData) {
        const classText = $('#class_id option:selected').text();
        const teacherText = $('#teacher_id option:selected').text();
        const subjectText = $('#subject_id option:selected').text();
        const roomText = $('#selectedRoomName').text();
        const dayText = $('#selectedDayName').text();
        const timeText = `${formData.start_time} - ${formData.end_time}`;
        
        return `\n\n📋 Details:\n• Day: ${dayText}\n• Time: ${timeText}\n• Class: ${classText}\n• Teacher: ${teacherText}\n• Subject: ${subjectText}\n• Room: ${roomText}`;
    }

    showValidationErrors(errors) {
        // Clear previous errors
        this.clearAllErrors();
        
        // Collect all error messages
        const errorMessages = [];
        
        // Show field-specific errors
        Object.keys(errors).forEach(field => {
            const $field = $(`#${field}`);
            const $feedback = $field.siblings('.invalid-feedback');
            
            const errorMessage = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            
            if ($field.length) {
                $field.addClass('is-invalid');
                $feedback.text(errorMessage).addClass('show').show();
            }
            
            // Add to error messages list
            errorMessages.push(errorMessage);
        });
        
        // Display all errors in the validation error section
        if (errorMessages.length > 0) {
            errorMessages.forEach(msg => {
                $('#validationErrorList').append(`<li>${msg}</li>`);
            });
            $('#validationErrors').slideDown(300);
            
            // Scroll to error section
            $('#validationErrors')[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    clearAllErrors() {
        // Clear field-specific errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').removeClass('show').hide();
        
        // Hide and clear validation error section
        $('#validationErrors').hide();
        $('#validationErrorList').empty();
        
        // Hide and clear conflict warning section
        $('#conflictWarning').hide();
        $('#conflictList').empty();
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
        // Try to gather details from form; if not present, fetch
        let details = {
            id: lessonId,
            className: $('#class_id option:selected').text(),
            teacher: $('#teacher_id option:selected').text(),
            subject: $('#subject_id option:selected').text(),
            day: $('#selectedDayName').text(),
            time: `${$('#start_time').val()} - ${$('#end_time').val()}`,
            room: $('#selectedRoomName').text()
        };

        // If details seem empty (e.g., invoked outside edit modal), fetch lesson details
        if (!details.className || details.className === '-- Select Class --') {
            try {
                const lesson = await this.fetchLesson(lessonId);
                details = {
                    id: lessonId,
                    className: lesson.class_name,
                    teacher: lesson.teacher_name,
                    subject: lesson.subject_name,
                    day: lesson.weekday_name,
                    time: `${lesson.start_time} - ${lesson.end_time}`,
                    room: lesson.room_name
                };
            } catch (e) {
                console.warn('Could not fetch lesson for delete confirmation, proceeding with minimal details');
            }
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
        const formData = {
            id: $('#lessonId').val(),
            class_id: $('#class_id').val(),
            teacher_id: $('#teacher_id').val(),
            room_id: $('#room_id').val(),
            subject_id: $('#subject_id').val(),
            weekday: $('#weekday').val(),
            start_time: $('#start_time').val(),
            end_time: $('#end_time').val()
        };
        
        console.log('Form data being sent:', formData);
        return formData;
    }

    validateForm() {
        let isValid = true;
        const requiredFields = ['class_id', 'teacher_id', 'room_id', 'subject_id', 'start_time', 'end_time'];
        
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

    async refreshTimetable() {
        // Reload the page to refresh the timetable
        window.location.reload();
    }

    setLoading(loading) {
        if (loading) {
            $('#saveLessonBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            $('#deleteLessonBtn').prop('disabled', true);
        } else {
            const saveText = this.currentAction === 'create' ? 'Create Lesson' : 'Update Lesson';
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
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert
        $('.content').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }

    async showConflictWarning(conflicts) {
        console.log('showConflictWarning called with conflicts:', conflicts);
        console.log('window.enhancedValidationModal available:', !!window.enhancedValidationModal);
        
        // Use the enhanced validation modal instead of confirm()
        if (window.enhancedValidationModal) {
            try {
                console.log('Attempting to show enhanced validation modal...');
                await window.enhancedValidationModal.show(conflicts, this.getFormData());
                console.log('Enhanced validation modal shown successfully');
            } catch (error) {
                console.error('Error showing enhanced validation modal:', error);
                // Fallback to original method if enhanced modal fails
                this.showConflictWarningFallback(conflicts);
            }
        } else {
            console.warn('Enhanced validation modal not available, using fallback');
            // Fallback to original method if enhanced modal is not available
            this.showConflictWarningFallback(conflicts);
        }
    }

    showConflictWarningFallback(conflicts) {
        // Original implementation as fallback
        let message = '⚠️ SCHEDULING CONFLICTS DETECTED\n\n';
        
        conflicts.forEach(conflict => {
            message += `🔴 ${conflict.type.toUpperCase()}: ${conflict.message}\n`;
            
            if (conflict.conflicting_lessons && conflict.conflicting_lessons.length > 0) {
                conflict.conflicting_lessons.forEach(lesson => {
                    message += `   • ${lesson.class} - ${lesson.subject || 'Unknown Subject'}\n`;
                    message += `     ${lesson.time} (${lesson.teacher || 'Unknown Teacher'})\n`;
                    if (lesson.room) {
                        message += `     Room: ${lesson.room}\n`;
                    }
                });
            }
            message += '\n';
        });
        
        message += 'Do you want to proceed anyway?\n\n';
        message += '⚠️ WARNING: This may cause scheduling conflicts!';
        
        if (confirm(message)) {
            // Proceed with save but skip conflict check
            this.saveLessonWithoutConflictCheck();
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
            
            // Success
            const actionText = this.currentAction === 'create' ? 'created' : 'updated';
            const lessonInfo = this.getLessonInfo(formData);
            this.showSuccess(`✅ Lesson ${actionText} successfully!${lessonInfo}`);
            this.closeModal();
            this.refreshTimetable();
            
        } catch (error) {
            console.error('Save lesson error:', error);
            this.showError(`Failed to ${this.currentAction} lesson: ${error.message}`);
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
