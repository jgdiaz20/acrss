/**
 * Enhanced Validation Modal System
 * Provides user-friendly conflict resolution for scheduling
 */

class EnhancedValidationModal {
    constructor() {
        this.modal = $('#validationModal');
        this.currentStep = 1;
        this.maxSteps = 3;
        this.conflicts = [];
        this.suggestions = [];
        this.originalFormData = {};
        this.resolvedData = {};
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupModal();
    }

    setupModal() {
        // Ensure modal is properly initialized
        if (!this.modal.length) {
            console.error('Validation modal not found in DOM');
            return;
        }

        // Set up modal event handlers
        this.modal.on('hidden.bs.modal', () => {
            this.resetModal();
        });

        this.modal.on('shown.bs.modal', () => {
            this.focusFirstElement();
        });
    }

    bindEvents() {
        // Navigation buttons
        this.modal.on('click', '#nextStepBtn', () => {
            this.nextStep();
        });

        this.modal.on('click', '#prevStepBtn', () => {
            this.prevStep();
        });

        // Resolution buttons
        this.modal.on('click', '#autoResolveBtn', () => {
            this.autoResolveConflicts();
        });

        this.modal.on('click', '#manualResolveBtn', () => {
            this.showManualResolveOptions();
        });

        this.modal.on('click', '#proceedAnywayBtn', () => {
            this.proceedWithConflicts();
        });


        // Suggestion click handlers
        this.modal.on('click', '.time-suggestion', (e) => {
            this.handleTimeSuggestion(e);
        });

        this.modal.on('click', '.room-suggestion', (e) => {
            this.handleRoomSuggestion(e);
        });

        this.modal.on('click', '.teacher-suggestion', (e) => {
            this.handleTeacherSuggestion(e);
        });

        // Keyboard navigation
        this.modal.on('keydown', (e) => {
            this.handleKeyboardNavigation(e);
        });
    }

    async show(conflicts, formData) {
        this.conflicts = conflicts;
        this.originalFormData = formData;
        this.resolvedData = { ...formData };
        
        // Render modal content
        this.renderModal();
        
        
        // Show modal with proper accessibility handling
        this.showModalWithAccessibility();
    }

    showModalWithAccessibility() {
        // Remove aria-hidden before showing
        this.modal.removeAttr('aria-hidden');
        
        // Show modal
        this.modal.modal('show');
        
        // Handle modal events for proper accessibility
        this.modal.on('shown.bs.modal', () => {
            // Set aria-hidden to false when shown
            this.modal.attr('aria-hidden', 'false');
            
            // Focus the first focusable element
            this.focusFirstElement();
        });
        
        this.modal.on('hidden.bs.modal', () => {
            // Set aria-hidden to true when hidden
            this.modal.attr('aria-hidden', 'true');
            
            // Clean up event listeners
            this.modal.off('shown.bs.modal hidden.bs.modal');
        });
    }

    focusFirstElement() {
        // Focus the first focusable element in the modal
        const focusableElements = this.modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (focusableElements.length > 0) {
            focusableElements.first().focus();
        }
    }

    handleKeyboardNavigation(e) {
        // Handle Escape key to close modal
        if (e.key === 'Escape') {
            e.preventDefault();
            this.modal.modal('hide');
            return;
        }

        // Handle Tab key for focus management
        if (e.key === 'Tab') {
            const focusableElements = this.modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            const firstElement = focusableElements.first();
            const lastElement = focusableElements.last();
            
            if (e.shiftKey) {
                // Shift + Tab (backwards)
                if (document.activeElement === firstElement[0]) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                // Tab (forwards)
                if (document.activeElement === lastElement[0]) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        }
    }

    renderModal() {
        // Update modal title and description
        this.updateModalHeader();
        
        // Render conflicts
        this.renderConflicts();
    }


    updateModalHeader() {
        const conflictCount = this.conflicts.length;
        const conflictTypes = this.conflicts.map(c => c.type);
        const hasTeacherConflict = conflictTypes.includes('teacher');
        const hasClassConflict = conflictTypes.includes('class');
        const hasRoomConflict = conflictTypes.includes('room');
        
        $('#conflictCount').text(conflictCount);
        
        let description = 'The selected time slot conflicts with existing schedules. ';
        
        if (hasTeacherConflict) {
            description += '⚠️ <strong>Teacher conflicts are critical</strong> - a teacher cannot be in two places at once. ';
        }
        if (hasClassConflict) {
            description += '⚠️ <strong>Class conflicts are high priority</strong> - a class cannot have two lessons simultaneously. ';
        }
        if (hasRoomConflict) {
            description += '⚠️ <strong>Room conflicts are medium priority</strong> - a room cannot be occupied by two classes at once. ';
        }
        
        description += 'Please review the details below and choose a resolution option.';
        
        $('#conflictDescription').html(description);
    }

    renderConflicts() {
        const conflictsList = $('#conflictsList');
        conflictsList.empty();

        this.conflicts.forEach((conflict, index) => {
            const conflictItem = this.createConflictItem(conflict, index);
            conflictsList.append(conflictItem);
        });
    }

    createConflictItem(conflict, index) {
        const severity = this.getConflictSeverity(conflict.type);
        const severityClass = this.getSeverityClass(severity);
        const severityText = this.getSeverityText(severity);
        
        const conflictingLessons = conflict.conflicting_lessons || [];
        
        let lessonsHtml = '';
        conflictingLessons.forEach(lesson => {
            lessonsHtml += `
                <div class="conflicting-lesson">
                    <div class="lesson-info">
                        <div class="lesson-details">
                            <i class="fas fa-clock mr-1"></i><span class="lesson-time">${lesson.time || 'Unknown Time'}</span>
                        </div>
                    </div>
                    <div class="lesson-details">
                        <i class="fas fa-user mr-1"></i>${lesson.teacher || 'Unknown Teacher'}
                        <i class="fas fa-book mr-1 ml-2"></i>${lesson.subject || 'Unknown Subject'}
                        <i class="fas fa-door-open mr-1 ml-2"></i>${lesson.room || 'Unknown Room'}
                        ${lesson.class ? `<i class="fas fa-users mr-1 ml-2"></i>${lesson.class}` : ''}
                    </div>
                </div>
            `;
        });

        return `
            <div class="conflict-item" data-conflict-index="${index}">
                <div class="conflict-header d-flex justify-content-between align-items-center">
                    <span class="conflict-type">${conflict.type.toUpperCase()} Conflict</span>
                    <span class="conflict-severity badge ${severityClass}">${severityText}</span>
                </div>
                <div class="conflict-details">
                    <p class="mb-2"><strong>${conflict.message}</strong></p>
                    ${lessonsHtml}
                </div>
            </div>
        `;
    }

    renderSuggestions() {
        this.renderTimeSuggestions();
        this.renderRoomSuggestions();
        this.renderTeacherSuggestions();
    }

    renderTimeSuggestions() {
        const timeSuggestions = this.suggestions.filter(s => s.type === 'time');
        const container = $('#timeSuggestions');
        container.empty();

        if (timeSuggestions.length === 0) {
            container.html('<p class="text-muted">No alternative time slots available.</p>');
            return;
        }

        timeSuggestions.forEach(suggestion => {
            const suggestionItem = this.createTimeSuggestionItem(suggestion);
            container.append(suggestionItem);
        });
    }

    createTimeSuggestionItem(suggestion) {
        const confidenceClass = this.getConfidenceClass(suggestion.confidence);
        const confidenceText = this.getConfidenceText(suggestion.confidence);
        
        return `
            <div class="time-suggestion" data-action="change-time" data-time="${suggestion.value}">
                <div class="suggestion-header">
                    <i class="fas fa-clock text-success mr-2"></i>
                    <strong>${suggestion.description}</strong>
                    <span class="badge ${confidenceClass} ml-2">${confidenceText}</span>
                </div>
                <div class="suggestion-details">
                    <small class="text-muted">Click to use this time slot</small>
                </div>
            </div>
        `;
    }

    renderRoomSuggestions() {
        const roomSuggestions = this.suggestions.filter(s => s.type === 'room');
        const container = $('#roomSuggestions');
        container.empty();

        if (roomSuggestions.length === 0) {
            container.html('<p class="text-muted">No alternative rooms available.</p>');
            return;
        }

        roomSuggestions.forEach(suggestion => {
            const suggestionItem = this.createRoomSuggestionItem(suggestion);
            container.append(suggestionItem);
        });
    }

    createRoomSuggestionItem(suggestion) {
        const confidenceClass = this.getConfidenceClass(suggestion.confidence);
        const confidenceText = this.getConfidenceText(suggestion.confidence);
        
        return `
            <div class="room-suggestion" data-action="change-room" data-room="${suggestion.value}">
                <div class="suggestion-header">
                    <i class="fas fa-door-open text-success mr-2"></i>
                    <strong>${suggestion.description}</strong>
                    <span class="badge ${confidenceClass} ml-2">${confidenceText}</span>
                </div>
                <div class="suggestion-details">
                    <small class="text-muted">Click to use this room</small>
                </div>
            </div>
        `;
    }

    renderTeacherSuggestions() {
        const teacherSuggestions = this.suggestions.filter(s => s.type === 'teacher');
        const container = $('#teacherSuggestions');
        container.empty();

        if (teacherSuggestions.length === 0) {
            container.html('<p class="text-muted">No alternative teachers available.</p>');
            return;
        }

        teacherSuggestions.forEach(suggestion => {
            const suggestionItem = this.createTeacherSuggestionItem(suggestion);
            container.append(suggestionItem);
        });
    }

    createTeacherSuggestionItem(suggestion) {
        const confidenceClass = this.getConfidenceClass(suggestion.confidence);
        const confidenceText = this.getConfidenceText(suggestion.confidence);
        
        return `
            <div class="teacher-suggestion" data-action="change-teacher" data-teacher="${suggestion.value}">
                <div class="suggestion-header">
                    <i class="fas fa-user text-success mr-2"></i>
                    <strong>${suggestion.description}</strong>
                    <span class="badge ${confidenceClass} ml-2">${confidenceText}</span>
                </div>
                <div class="suggestion-details">
                    <small class="text-muted">Click to use this teacher</small>
                </div>
            </div>
        `;
    }

    async generateSuggestions(conflicts, formData) {
        const suggestions = [];
        
        // Generate time suggestions
        const timeSuggestions = await this.generateTimeSuggestions(conflicts, formData);
        suggestions.push(...timeSuggestions);
        
        // Generate room suggestions
        const roomSuggestions = await this.generateRoomSuggestions(conflicts, formData);
        suggestions.push(...roomSuggestions);
        
        // Generate teacher suggestions
        const teacherSuggestions = await this.generateTeacherSuggestions(conflicts, formData);
        suggestions.push(...teacherSuggestions);
        
        return suggestions;
    }

    async generateTimeSuggestions(conflicts, formData) {
        const suggestions = [];
        const currentTime = formData.start_time;
        
        try {
            // Generate alternative time slots (15-minute intervals)
            const timeSlots = await this.generateTimeSlots(currentTime);
            
            if (Array.isArray(timeSlots)) {
                for (const slot of timeSlots) {
                    const isAvailable = await this.isTimeSlotAvailable(slot, formData);
                    if (isAvailable) {
                        suggestions.push({
                            type: 'time',
                            value: `${slot.start_time}-${slot.end_time}`,
                            description: `${slot.start_time} - ${slot.end_time}`,
                            confidence: this.calculateTimeConfidence(slot, formData)
                        });
                    }
                }
            }
        } catch (error) {
            console.error('Error generating time suggestions:', error);
        }
        
        return suggestions.slice(0, 3); // Return top 3 suggestions
    }

    async generateRoomSuggestions(conflicts, formData) {
        const suggestions = [];
        const currentRoom = formData.room_id;
        
        try {
            // Get available rooms for the time slot
            const availableRooms = await this.getAvailableRooms(formData);
            
            if (Array.isArray(availableRooms)) {
                for (const room of availableRooms) {
                    if (room.id !== currentRoom) {
                        suggestions.push({
                            type: 'room',
                            value: room.id,
                            description: `${room.name} (${room.capacity} capacity)`,
                            confidence: this.calculateRoomConfidence(room, formData)
                        });
                    }
                }
            }
        } catch (error) {
            console.error('Error generating room suggestions:', error);
        }
        
        return suggestions.slice(0, 2); // Return top 2 suggestions
    }

    async generateTeacherSuggestions(conflicts, formData) {
        const suggestions = [];
        const currentTeacher = formData.teacher_id;
        const subjectId = formData.subject_id;
        
        try {
            // Get teachers assigned to the same subject
            const availableTeachers = await this.getTeachersForSubject(subjectId);
            
            if (Array.isArray(availableTeachers)) {
                for (const teacher of availableTeachers) {
                    if (teacher.id !== currentTeacher) {
                        const isAvailable = await this.isTeacherAvailable(teacher, formData);
                        if (isAvailable) {
                            suggestions.push({
                                type: 'teacher',
                                value: teacher.id,
                                description: `${teacher.name} (Available)`,
                                confidence: this.calculateTeacherConfidence(teacher, formData)
                            });
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Error generating teacher suggestions:', error);
        }
        
        return suggestions.slice(0, 2); // Return top 2 suggestions
    }

    // Step navigation methods
    showStep(step) {
        // Hide all steps
        $('.validation-step').hide();
        
        // Show current step
        $(`.validation-step-${step}`).show();
        
        // Update progress indicator
        this.updateProgressIndicator(step);
        
        // Update navigation buttons
        this.updateNavigationButtons(step);
        
        // Update action buttons
        this.updateActionButtons();
        
        this.currentStep = step;
    }

    nextStep() {
        if (this.currentStep < this.maxSteps) {
            this.showStep(this.currentStep + 1);
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.showStep(this.currentStep - 1);
        }
    }

    updateProgressIndicator(step) {
        const progress = (step / this.maxSteps) * 100;
        $('#progressBar').css('width', `${progress}%`);
        $('#progressText').text(`Step ${step} of ${this.maxSteps}`);
    }

    updateNavigationButtons(step) {
        const prevBtn = $('#prevStepBtn');
        const nextBtn = $('#nextStepBtn');
        
        // Show/hide previous button
        if (step > 1) {
            prevBtn.show();
        } else {
            prevBtn.hide();
        }
        
        // Update next button text
        if (step === this.maxSteps) {
            nextBtn.hide();
        } else {
            nextBtn.show();
        }
    }

    updateActionButtons() {
        // Update action buttons based on current step and available suggestions
        const currentStep = this.currentStep;
        
        // Update resolution option buttons visibility
        if (currentStep === 3) {
            // Show resolution options in step 3
            $('.resolution-options').show();
            
            // Enable/disable auto-resolve button based on available suggestions
            const hasSuggestions = this.suggestions.length > 0;
            $('#autoResolveBtn').prop('disabled', !hasSuggestions);
            
            // Update button text based on suggestions
            if (hasSuggestions) {
                $('#autoResolveBtn').text('Auto-Resolve');
            } else {
                $('#autoResolveBtn').text('No Suggestions Available').prop('disabled', true);
            }
        } else {
            // Hide resolution options in other steps
            $('.resolution-options').hide();
        }
        
        // Update suggestion buttons based on current step
        if (currentStep === 2) {
            // Show suggestion buttons in step 2
            $('.suggestion-category').show();
        } else {
            // Hide suggestion buttons in other steps
            $('.suggestion-category').hide();
        }
    }

    // Suggestion handling methods
    handleTimeSuggestion(e) {
        const $suggestion = $(e.currentTarget);
        const newTime = $suggestion.data('time');
        
        // Update form with new time
        this.updateFormTime(newTime);
        
        // Show success message
        this.showSuggestionApplied('Time slot updated successfully!');
        
        // Re-validate if needed
        this.revalidateForm();
    }

    handleRoomSuggestion(e) {
        const $suggestion = $(e.currentTarget);
        const newRoom = $suggestion.data('room');
        
        // Update form with new room
        this.updateFormRoom(newRoom);
        
        // Show success message
        this.showSuggestionApplied('Room updated successfully!');
        
        // Re-validate if needed
        this.revalidateForm();
    }

    handleTeacherSuggestion(e) {
        const $suggestion = $(e.currentTarget);
        const newTeacher = $suggestion.data('teacher');
        
        // Update form with new teacher
        this.updateFormTeacher(newTeacher);
        
        // Show success message
        this.showSuggestionApplied('Teacher updated successfully!');
        
        // Re-validate if needed
        this.revalidateForm();
    }

    // Resolution methods
    autoResolveConflicts() {
        const bestSuggestion = this.getBestSuggestion();
        
        if (bestSuggestion) {
            this.applySuggestion(bestSuggestion);
            this.showSuggestionApplied('Conflicts resolved automatically!');
        } else {
            this.showError('Unable to automatically resolve conflicts. Please choose a manual option.');
        }
    }

    getBestSuggestion() {
        // Prioritize suggestions based on conflict severity and confidence
        const timeSuggestions = this.suggestions.filter(s => s.type === 'time');
        const roomSuggestions = this.suggestions.filter(s => s.type === 'room');
        const teacherSuggestions = this.suggestions.filter(s => s.type === 'teacher');
        
        // Prioritize time changes as they usually resolve most conflicts
        if (timeSuggestions.length > 0) {
            return timeSuggestions[0]; // Best time suggestion
        }
        
        // Then room changes
        if (roomSuggestions.length > 0) {
            return roomSuggestions[0];
        }
        
        // Finally teacher changes
        if (teacherSuggestions.length > 0) {
            return teacherSuggestions[0];
        }
        
        return null;
    }

    proceedWithConflicts() {
        // Close modal and proceed with original data
        this.modal.modal('hide');
        
        // Trigger the original save function with conflicts
        if (window.inlineEditing && window.inlineEditing.saveLessonWithoutConflictCheck) {
            window.inlineEditing.saveLessonWithoutConflictCheck();
        }
    }

    showManualResolveOptions() {
        // Show manual resolution options (step 3)
        this.showStep(3);
    }

    // Utility methods
    getSeverityLevel() {
        const hasClassConflict = this.conflicts.some(c => c.type === 'class');
        const hasTeacherConflict = this.conflicts.some(c => c.type === 'teacher');
        const hasRoomConflict = this.conflicts.some(c => c.type === 'room');
        
        if (hasClassConflict && hasTeacherConflict && hasRoomConflict) {
            return 'critical';
        } else if (hasClassConflict || hasTeacherConflict) {
            return 'high';
        } else if (hasRoomConflict) {
            return 'medium';
        }
        return 'low';
    }

    getConflictSeverity(conflictType) {
        switch (conflictType) {
            case 'teacher':
                return 'critical'; // Teacher conflicts are most critical
            case 'class':
                return 'high'; // Class conflicts are high priority
            case 'room':
                return 'medium'; // Room conflicts are medium priority
            case 'time':
                return 'low'; // Time conflicts are low priority
            default:
                return 'medium';
        }
    }

    getSeverityClass(severity) {
        switch (severity) {
            case 'critical': return 'badge-danger';
            case 'high': return 'badge-danger';
            case 'medium': return 'badge-warning';
            case 'low': return 'badge-info';
            default: return 'badge-secondary';
        }
    }

    getSeverityText(severity) {
        switch (severity) {
            case 'critical': return 'Critical - Must Resolve';
            case 'high': return 'High Priority';
            case 'medium': return 'Medium Priority';
            case 'low': return 'Low Priority';
            default: return 'Unknown';
        }
    }

    getConfidenceClass(confidence) {
        if (confidence >= 0.8) return 'badge-success';
        if (confidence >= 0.6) return 'badge-warning';
        return 'badge-danger';
    }

    getConfidenceText(confidence) {
        if (confidence >= 0.8) return 'High';
        if (confidence >= 0.6) return 'Medium';
        return 'Low';
    }

    showSuggestionApplied(message) {
        $('#successText').text(message);
        $('#successMessage').show();
        
        setTimeout(() => {
            $('#successMessage').fadeOut();
        }, 3000);
    }

    showError(message) {
        $('#errorText').text(message);
        $('#errorMessage').show();
        
        setTimeout(() => {
            $('#errorMessage').fadeOut();
        }, 5000);
    }

    resetModal() {
        this.currentStep = 1;
        this.conflicts = [];
        this.suggestions = [];
        this.originalFormData = {};
        this.resolvedData = {};
        
        // Hide all steps except first
        $('.validation-step').hide();
        $('.validation-step-1').show();
        
        // Reset progress
        this.updateProgressIndicator(1);
        this.updateNavigationButtons(1);
        
        // Clear messages
        $('#successMessage').hide();
        $('#errorMessage').hide();
    }

    focusFirstElement() {
        // Focus on the first interactive element
        const firstElement = this.modal.find('button, input, select, textarea').first();
        if (firstElement.length) {
            firstElement.focus();
        }
    }

    // Data retrieval methods using actual API endpoints
    async generateTimeSlots(currentTime) {
        try {
            const response = await fetch('/admin/validation/alternative-times', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    weekday: this.originalFormData.weekday,
                    class_id: this.originalFormData.class_id,
                    teacher_id: this.originalFormData.teacher_id,
                    room_id: this.originalFormData.room_id,
                    duration: 60
                })
            });

            const result = await response.json();
            return result.time_slots || [];
        } catch (error) {
            console.error('Error fetching time slots:', error);
            return [];
        }
    }

    async isTimeSlotAvailable(slot, formData) {
        try {
            const response = await fetch('/admin/validation/check-conflicts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    weekday: formData.weekday,
                    start_time: slot.start_time,
                    end_time: slot.end_time,
                    class_id: formData.class_id,
                    teacher_id: formData.teacher_id,
                    room_id: formData.room_id
                })
            });

            const result = await response.json();
            return !result.has_conflicts;
        } catch (error) {
            console.error('Error checking time slot availability:', error);
            return false;
        }
    }

    async getAvailableRooms(formData) {
        try {
            const response = await fetch('/admin/validation/available-rooms', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    weekday: formData.weekday,
                    start_time: formData.start_time,
                    end_time: formData.end_time
                })
            });

            const result = await response.json();
            return result.rooms || [];
        } catch (error) {
            console.error('Error fetching available rooms:', error);
            return [];
        }
    }

    async getTeachersForSubject(subjectId) {
        try {
            const response = await fetch(`/admin/validation/subjects/${subjectId}/teachers`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const result = await response.json();
            return result.teachers || [];
        } catch (error) {
            console.error('Error fetching teachers for subject:', error);
            return [];
        }
    }

    async isTeacherAvailable(teacher, formData) {
        try {
            const response = await fetch('/admin/validation/teacher-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    teacher_id: teacher.id,
                    weekday: formData.weekday,
                    start_time: formData.start_time,
                    end_time: formData.end_time
                })
            });

            const result = await response.json();
            return result.available;
        } catch (error) {
            console.error('Error checking teacher availability:', error);
            return false;
        }
    }

    calculateTimeConfidence(slot, formData) {
        return slot.confidence || 0.8;
    }

    calculateRoomConfidence(room, formData) {
        // Simple confidence calculation based on room capacity
        const capacity = parseInt(room.capacity) || 0;
        if (capacity >= 50) return 0.9;
        if (capacity >= 30) return 0.8;
        if (capacity >= 20) return 0.7;
        return 0.6;
    }

    calculateTeacherConfidence(teacher, formData) {
        // Simple confidence calculation - could be enhanced with more data
        return 0.8;
    }

    updateFormTime(newTime) {
        const [startTime, endTime] = newTime.split('-');
        
        // Update form fields
        $('#start_time').val(this.formatTimeForForm(startTime));
        $('#end_time').val(this.formatTimeForForm(endTime));
        
        // Update resolved data
        this.resolvedData.start_time = this.formatTimeForForm(startTime);
        this.resolvedData.end_time = this.formatTimeForForm(endTime);
        
        // Trigger change events
        $('#start_time, #end_time').trigger('change');
    }

    updateFormRoom(newRoom) {
        // Update form field
        $('#room_id').val(newRoom);
        
        // Update resolved data
        this.resolvedData.room_id = newRoom;
        
        // Trigger change event
        $('#room_id').trigger('change');
    }

    updateFormTeacher(newTeacher) {
        // Update form field
        $('#teacher_id').val(newTeacher);
        
        // Update resolved data
        this.resolvedData.teacher_id = newTeacher;
        
        // Trigger change event
        $('#teacher_id').trigger('change');
    }

    revalidateForm() {
        // Re-validate form after changes
        if (window.inlineEditing && window.inlineEditing.validateForm) {
            return window.inlineEditing.validateForm();
        }
        return true;
    }

    applySuggestion(suggestion) {
        // Apply the suggestion to the form
        switch (suggestion.type) {
            case 'time':
                this.updateFormTime(suggestion.value);
                break;
            case 'room':
                this.updateFormRoom(suggestion.value);
                break;
            case 'teacher':
                this.updateFormTeacher(suggestion.value);
                break;
        }
    }

    formatTimeForForm(time24) {
        // Convert 24-hour format to form format (e.g., "09:00" to "9:00 AM")
        const [hours, minutes] = time24.split(':');
        const hour12 = hours % 12 || 12;
        const ampm = hours >= 12 ? 'PM' : 'AM';
        return `${hour12}:${minutes} ${ampm}`;
    }

}

// Initialize the enhanced validation modal when DOM is ready
$(document).ready(function() {
    console.log('Initializing Enhanced Validation Modal...');
    try {
        window.enhancedValidationModal = new EnhancedValidationModal();
        console.log('Enhanced Validation Modal initialized successfully');
    } catch (error) {
        console.error('Error initializing Enhanced Validation Modal:', error);
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedValidationModal;
}

