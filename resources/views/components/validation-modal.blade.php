aaaaaaaaaa{{-- Simple Conflict Notification Modal --}}
<div class="modal fade" id="validationModal" tabindex="-1" role="dialog" aria-labelledby="validationModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content validation-modal">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="validationModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span id="modalTitle">Scheduling Conflicts Detected</span>
                    <span class="badge badge-danger ml-2" id="conflictCountBadge">0</span>
                </h5>
            </div>
            
            <div class="modal-body">
                {{-- Conflict Summary --}}
                <div class="conflict-summary mb-3">
                    <p class="mb-0" id="conflictDescription">The selected time slot conflicts with existing schedules. Please review the details below.</p>
                </div>

                {{-- Conflict Details --}}
                <div class="conflict-details">
                    <div id="conflictsList" class="conflicts-list">
                        {{-- Conflicts will be populated by JavaScript --}}
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="text-right mt-3 pt-3 border-top">
                    <a href="{{ route('admin.room-management.master-timetable.show', request('weekday', 1)) }}" class="btn btn-outline-primary btn-md" target="_blank">
                        <i class="fas fa-th mr-1"></i> Master Timetable
                    </a>
                    <button type="button" class="btn btn-secondary btn-md ml-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Close
                    </button>
                </div>
                
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Container Improvements */
#validationModal .modal-dialog {
    max-width: 900px;
    margin: 1.75rem auto;
}

#validationModal .modal-content {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

#validationModal .modal-header {
    border-bottom: 2px solid #ffc107;
    border-radius: 0.5rem 0.5rem 0 0;
    padding: 1.5rem;
}

#validationModal .modal-body {
    padding: 1.5rem;
    max-height: 70vh;
    overflow-y: auto;
}

/* Close Button Container */
#validationModal .text-right {
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
    margin-top: 1.5rem;
}

/* Conflict Item Styling */
#validationModal .conflict-item {
    border-left: 4px solid #dc3545;
    background: #f8f9fa;
    margin-bottom: 1.25rem;
    padding: 0;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    position: relative;
}

/* Add separator line after each conflict except the last one */
#validationModal .conflict-item:not(:last-child)::after {
    content: '';
    position: absolute;
    bottom: -0.625rem;
    left: 50%;
    transform: translateX(-50%);
    width: 90%;
    height: 2px;
    background: linear-gradient(to right, transparent, #dee2e6 20%, #dee2e6 80%, transparent);
}

#validationModal .conflict-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #dc3545;
}

/* Conflict Header (Clickable) */
#validationModal .conflict-header {
    padding: 1rem 1.25rem;
    border-radius: 0.375rem 0.375rem 0 0;
    transition: background-color 0.2s ease;
}

#validationModal .conflict-header:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Collapse Icon Animation */
#validationModal .collapse-icon {
    transition: transform 0.3s ease;
    color: #6c757d;
}

#validationModal .conflict-header[aria-expanded="false"] .collapse-icon {
    transform: rotate(-90deg);
}

#validationModal .conflict-header[aria-expanded="true"] .collapse-icon {
    transform: rotate(0deg);
}

#validationModal .conflict-type {
    font-weight: 700;
    color: #dc3545;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
}

#validationModal .conflict-severity {
    font-size: 0.75rem;
    font-weight: 600;
}

#validationModal .conflict-details {
    padding: 0 1.25rem 1.25rem 1.25rem;
    color: #6c757d;
    line-height: 1.5;
}

#validationModal .conflicting-lesson {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-top: 0.75rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

#validationModal .conflicting-lesson .lesson-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

#validationModal .conflicting-lesson .lesson-details {
    font-size: 0.875rem;
    color: #6c757d;
    line-height: 1.6;
}

#validationModal .conflicting-lesson .lesson-time {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

/* Button Improvements */

/* Alert Improvements */
#validationModal .alert {
    border-radius: 0.5rem;
    border: none;
    padding: 1.25rem;
}

#validationModal .alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border-left: 4px solid #ffc107;
}

/* Responsive Design */
@media (max-width: 768px) {
    #validationModal .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    #validationModal .modal-body {
        padding: 1.5rem;
        max-height: 60vh;
    }
    
    #validationModal .text-right {
        text-align: center !important;
        padding-top: 1rem;
    }
    
    #validationModal .text-right .btn-secondary {
        width: 100%;
        max-width: 200px;
    }
    
    #validationModal .conflicting-lesson .lesson-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    #validationModal .conflict-item {
        padding: 1rem;
    }
}

/* Animation for modal appearance */
#validationModal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

#validationModal.show .modal-dialog {
    transform: translate(0, 0);
}

/* Focus improvements */
#validationModal .btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

#validationModal .btn:focus-visible {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/* Ensure modal is properly accessible when shown */
#validationModal.show {
    aria-hidden: false;
}

#validationModal:not(.show) {
    aria-hidden: true;
}

/* Icon improvements */
#validationModal .fas {
    font-size: 0.9em;
}

/* Conflict Count Badge */
#conflictCountBadge {
    font-size: 0.875rem;
    font-weight: 700;
    padding: 0.35rem 0.65rem;
    border-radius: 50%;
    min-width: 2rem;
    text-align: center;
    background-color: #dc3545 !important;
    color: #fff !important;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
    animation: badgePulse 2s ease-in-out infinite;
}

@keyframes badgePulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.5);
    }
}
</style>