{{-- Simple Conflict Notification Modal --}}
<div class="modal fade" id="validationModal" tabindex="-1" role="dialog" aria-labelledby="validationModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="validationModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span id="modalTitle">Scheduling Conflicts Detected</span>
                </h5>
            </div>
            
            <div class="modal-body">
                {{-- Conflict Summary --}}
                <div class="conflict-summary mb-4">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-info-circle mr-2"></i><span id="conflictCount">0</span> Conflicts Detected</h6>
                        <p class="mb-0" id="conflictDescription">The selected time slot conflicts with existing schedules. Please review the details below and adjust your schedule accordingly.</p>
                    </div>
                </div>

                {{-- Conflict Details --}}
                <div class="conflict-details">
                    <h6 class="text-danger mb-3">
                        <i class="fas fa-times-circle mr-2"></i>Conflict Details
                    </h6>
                    <div id="conflictsList" class="conflicts-list">
                        {{-- Conflicts will be populated by JavaScript --}}
                    </div>
                </div>

                {{-- Close Button --}}
                <div class="text-right mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-secondary btn-md" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Close
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
    padding: 2rem 2rem 1.5rem 2rem;
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
.validation-modal .conflict-item {
    border-left: 4px solid #dc3545;
    background: #f8f9fa;
    margin-bottom: 1rem;
    padding: 1.25rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.validation-modal .conflict-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #dc3545;
}

.validation-modal .conflict-type {
    font-weight: 700;
    color: #dc3545;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
}

.validation-modal .conflict-severity {
    font-size: 0.75rem;
    font-weight: 600;
}

.validation-modal .conflict-details {
    margin-top: 0.75rem;
    color: #6c757d;
    line-height: 1.5;
}

.validation-modal .conflicting-lesson {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-top: 0.75rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.validation-modal .conflicting-lesson .lesson-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.validation-modal .conflicting-lesson .lesson-details {
    font-size: 0.875rem;
    color: #6c757d;
    line-height: 1.6;
}

.validation-modal .conflicting-lesson .lesson-time {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

/* Button Improvements */
#validationModal .btn {
    border-radius: 0.375rem;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    transition: all 0.2s ease;
}

#validationModal .btn-lg {
    padding: 0.875rem 2rem;
    font-size: 1rem;
}

#validationModal .btn-secondary {
    background-color:rgb(244, 79, 53);
    border-color: #6c757d;
}

#validationModal .btn-secondary:hover {
    background-color:rgb(91, 74, 226);
    border-color: #545b62;
    transform: translateY(-1px);
}

#validationModal .btn-outline-primary {
    color: #007bff;
    border-color: #007bff;
}

#validationModal .btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    transform: translateY(-1px);
}

#validationModal .btn-outline-warning {
    color: #ffc107;
    border-color: #ffc107;
}

#validationModal .btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
    transform: translateY(-1px);
}

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
    
    .validation-modal .conflicting-lesson .lesson-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .validation-modal .conflict-item {
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
</style>