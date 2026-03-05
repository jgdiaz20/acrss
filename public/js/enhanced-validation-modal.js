/**
 * Enhanced Validation Modal System
 * Provides user-friendly conflict resolution for scheduling
 */

class EnhancedValidationModal {
    constructor() {
        this.modal = $('#validationModal');
        this.conflicts = [];
        this.originalFormData = {};
        
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
        // Keyboard navigation
        this.modal.on('keydown', (e) => {
            this.handleKeyboardNavigation(e);
        });

        // Handle collapse events to update chevron icon
        this.modal.on('show.bs.collapse', '.collapse', function() {
            const header = $(this).prev('.conflict-header');
            header.attr('aria-expanded', 'true');
        });

        this.modal.on('hide.bs.collapse', '.collapse', function() {
            const header = $(this).prev('.conflict-header');
            header.attr('aria-expanded', 'false');
        });
    }

    async show(conflicts, formData) {
        this.conflicts = conflicts;
        this.originalFormData = formData;
        
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
        
        // Update conflict count badge
        $('#conflictCountBadge').text(conflictCount);
        
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
        const collapseId = `conflict-details-${index}`;
        
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
                <div class="conflict-header d-flex justify-content-between align-items-center" data-toggle="collapse" data-target="#${collapseId}" aria-expanded="true" aria-controls="${collapseId}" style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chevron-down mr-2 collapse-icon"></i>
                        <span class="conflict-type">${conflict.type.toUpperCase()} Conflict</span>
                    </div>
                    <span class="conflict-severity badge ${severityClass}">${severityText}</span>
                </div>
                <div id="${collapseId}" class="collapse show">
                    <div class="conflict-details">
                        <p class="mb-2"><strong>${conflict.message}</strong></p>
                        ${lessonsHtml}
                    </div>
                </div>
            </div>
        `;
    }


    // Utility methods

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


    resetModal() {
        this.conflicts = [];
        this.originalFormData = {};
    }

    focusFirstElement() {
        // Focus on the first interactive element
        const firstElement = this.modal.find('button, input, select, textarea').first();
        if (firstElement.length) {
            firstElement.focus();
        }
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

