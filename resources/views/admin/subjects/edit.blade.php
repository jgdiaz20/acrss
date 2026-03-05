@extends('layouts.admin')
@section('content')

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.home') }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.subjects.index') }}">
                <i class="fas fa-book"></i> Subjects
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.subjects.show', $subject->id) }}">
                <i class="fas fa-eye"></i> {{ $subject->name }}
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-edit"></i> Edit
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-edit text-warning mr-2"></i>
                Edit {{ $subject->name }}
            </h1>
            <p class="page-subtitle text-muted">
                Update subject information and settings
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Subject
                </a>
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> All Subjects
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle text-primary mr-2"></i>
            Subject Information
        </h3>
    </div>
    <div class="card-body">
        <!-- Lessons Warning -->
        @if($subject->lessons()->count() > 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Warning:</strong> This subject has <strong>{{ $subject->lessons()->count() }} existing lesson(s)</strong>. 
                Changing the scheduling mode may affect these lessons. Please review carefully before saving.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        <!-- Error Messages -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        <form method="POST" action="{{ route("admin.subjects.update", [$subject->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="name">Subject Name</label>
                        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $subject->name) }}" required>
                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                        <span class="help-block">Enter the full name of the subject (e.g., Computer Programming)</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="required" for="code">Subject Code</label>
                        <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code', $subject->code) }}" required>
                        @if($errors->has('code'))
                            <div class="invalid-feedback">
                                {{ $errors->first('code') }}
                            </div>
                        @endif
                        <span class="help-block">Enter a unique code for the subject (e.g., COMPROG)</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="required" for="credits">Total Credits</label>
                        <input class="form-control {{ $errors->has('credits') ? 'is-invalid' : '' }}" type="number" name="credits" id="credits" value="{{ old('credits', $subject->credits) }}" min="1" max="3" required>
                        @if($errors->has('credits'))
                            <div class="invalid-feedback">
                                {{ $errors->first('credits') }}
                            </div>
                        @endif
                        <span class="help-block" id="credits-help">Lab/Lecture mode: Enter credits (1-3) | Flexible mode: Auto-calculated (max 3)</span>
                    </div>

                    <div class="form-group">
                        <label class="required" for="scheduling_mode">Scheduling Mode</label>
                        <select class="form-control {{ $errors->has('scheduling_mode') ? 'is-invalid' : '' }}" name="scheduling_mode" id="scheduling_mode" required>
                            <option value="">Select Mode</option>
                            @foreach(\App\Subject::SCHEDULING_MODES as $key => $mode)
                                <option value="{{ $key }}" {{ old('scheduling_mode', $subject->scheduling_mode) == $key ? 'selected' : '' }}>{{ $mode }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('scheduling_mode'))
                            <div class="invalid-feedback">
                                {{ $errors->first('scheduling_mode') }}
                            </div>
                        @endif
                        
                    </div>

                    <div id="flexible-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lecture_units">Lecture Units</label>
                                    <input class="form-control {{ $errors->has('lecture_units') ? 'is-invalid' : '' }}" type="number" name="lecture_units" id="lecture_units" value="{{ old('lecture_units', $subject->lecture_units) }}" min="0" max="10">
                                    @if($errors->has('lecture_units'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('lecture_units') }}
                                        </div>
                                    @endif
                                    <span class="help-block">1 unit = 1 hour</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lab_units">Laboratory Units</label>
                                    <input class="form-control {{ $errors->has('lab_units') ? 'is-invalid' : '' }}" type="number" name="lab_units" id="lab_units" value="{{ old('lab_units', $subject->lab_units) }}" min="0" max="10">
                                    @if($errors->has('lab_units'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('lab_units') }}
                                        </div>
                                    @endif
                                    <span class="help-block">1 unit = 3 hours</span>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info" id="hours-summary" style="display: none;">
                            <strong>Total Hours:</strong> <span id="total-hours-display">0</span> hours
                            (<span id="lecture-hours-display">0</span> lecture + <span id="lab-hours-display">0</span> lab)
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="required" for="type">Subject Type</label>
                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                            <option value="">Select Type</option>
                            @foreach(\App\Subject::SUBJECT_TYPES as $key => $type)
                                <option value="{{ $key }}" {{ (old('type', $subject->type) == $key) ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('type'))
                            <div class="invalid-feedback">
                                {{ $errors->first('type') }}
                            </div>
                        @endif
                        <span class="help-block">Select the type of subject</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    
</div>
            </div>
            
            <div class="form-group mt-4">
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-success btn-lg" type="submit">
                                <i class="fas fa-save mr-2"></i> Update Subject
                            </button>
                            <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list mr-2"></i> All Subjects
                            </a>
                            <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-outline-info ml-2">
                                <i class="fas fa-eye mr-2"></i> View Subject
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Scheduling Mode Change Confirmation Modal -->
<div class="modal fade" id="schedulingModeChangeModal" tabindex="-1" role="dialog" aria-labelledby="schedulingModeChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="schedulingModeChangeModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Confirm Scheduling Mode Change
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-ban mr-2"></i>
                    <strong>Cannot Change Scheduling Mode</strong>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Current Lessons:</strong> This subject has <strong>{{ $subject->lessons()->count() }} existing lesson(s)</strong>.
                </div>
                
                <p><strong>Scheduling mode cannot be changed because this subject has active lessons.</strong></p>
                
                <p>Changing the scheduling mode would affect existing lessons:</p>
                <ul>
                    <li>Lessons may no longer match the new mode requirements</li>
                    <li>Lesson types may become invalid</li>
                    <li>Hours tracking would be recalculated</li>
                    <li>Data integrity would be compromised</li>
                </ul>
                
                <p class="mb-0"><strong>To change the scheduling mode, please delete all existing lessons first.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Credits Change Confirmation Modal -->
<div class="modal fade" id="creditsChangeModal" tabindex="-1" role="dialog" aria-labelledby="creditsChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="creditsChangeModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Confirm Credits Change
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-ban mr-2"></i>
                    <strong>Cannot Reduce Credits/Hours</strong>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Current Lessons:</strong> This subject has <strong>{{ $subject->lessons()->count() }} existing lesson(s)</strong>.
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Current Total Hours:</strong> <span id="currentTotalHours"></span><br>
                    <strong>Attempted New Hours:</strong> <span id="newTotalHours"></span><br>
                    <strong class="text-danger">⚠️ This would reduce total hours!</strong>
                </div>
                
                <p><strong>Credits/hours cannot be reduced because this subject has active lessons.</strong></p>
                
                <p><strong class="text-danger">Why this is blocked:</strong></p>
                <ul>
                    <li><strong>Existing lessons may exceed the new total hours limit</strong></li>
                    <li>Over-scheduling validation would fail</li>
                    <li>Data integrity would be compromised</li>
                    <li>Lessons would become invalid</li>
                </ul>
                
                <p class="mb-0"><strong>To reduce credits/hours, please delete or adjust existing lessons first.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const schedulingMode = document.getElementById('scheduling_mode');
    const flexibleFields = document.getElementById('flexible-fields');
    const lectureUnits = document.getElementById('lecture_units');
    const labUnits = document.getElementById('lab_units');
    const credits = document.getElementById('credits');
    const creditsHelp = document.getElementById('credits-help');
    const hoursSummary = document.getElementById('hours-summary');
    const totalHoursDisplay = document.getElementById('total-hours-display');
    const lectureHoursDisplay = document.getElementById('lecture-hours-display');
    const labHoursDisplay = document.getElementById('lab-hours-display');

    function updateDisplay() {
        const mode = schedulingMode.value;
        
        if (mode === 'lab') {
            // Lab mode: Pure laboratory
            flexibleFields.style.display = 'none';
            lectureUnits.value = 0;
            labUnits.value = credits.value || 3;
            lectureUnits.removeAttribute('required');
            labUnits.removeAttribute('required');
            credits.removeAttribute('readonly');
            creditsHelp.textContent = 'Lab mode: Enter credits 1-3 (1 credit = 3 lab hours)';
        } else if (mode === 'lecture') {
            // Lecture mode: Pure lecture
            flexibleFields.style.display = 'none';
            lectureUnits.value = credits.value || 3;
            labUnits.value = 0;
            lectureUnits.removeAttribute('required');
            labUnits.removeAttribute('required');
            credits.removeAttribute('readonly');
            creditsHelp.textContent = 'Lecture mode: Enter credits 1-3 (1 credit = 1 lecture hour)';
        } else if (mode === 'flexible') {
            // Flexible mode: Show breakdown fields
            flexibleFields.style.display = 'block';
            lectureUnits.setAttribute('required', 'required');
            labUnits.setAttribute('required', 'required');
            credits.setAttribute('readonly', 'readonly');
            creditsHelp.textContent = 'Flexible mode: Auto-calculated from lecture + lab units (max 3 total)';
        } else {
            flexibleFields.style.display = 'none';
            lectureUnits.removeAttribute('required');
            labUnits.removeAttribute('required');
            credits.removeAttribute('readonly');
            creditsHelp.textContent = 'Select a scheduling mode';
        }
        
        calculateTotals();
    }

    function calculateTotals() {
        const mode = schedulingMode.value;
        const lecture = parseInt(lectureUnits.value) || 0;
        const lab = parseInt(labUnits.value) || 0;
        
        if (mode === 'lab') {
            // Lab mode: Sync lab units with credits (credits is the source of truth)
            const creditsValue = parseInt(credits.value) || 0;
            labUnits.value = creditsValue;
        } else if (mode === 'lecture') {
            // Lecture mode: Sync lecture units with credits (credits is the source of truth)
            const creditsValue = parseInt(credits.value) || 0;
            lectureUnits.value = creditsValue;
        } else if (mode === 'flexible') {
            // Flexible mode: credits = lecture + lab (units are source of truth)
            const totalCredits = lecture + lab;
            credits.value = totalCredits;
            
            // Calculate hours
            const lectureHours = lecture * 1;
            const labHours = lab * 3;
            const totalHours = lectureHours + labHours;
            
            // Update display
            lectureHoursDisplay.textContent = lectureHours;
            labHoursDisplay.textContent = labHours;
            totalHoursDisplay.textContent = totalHours;
            hoursSummary.style.display = totalHours > 0 ? 'block' : 'none';
        }
    }

    // Track initial values and if subject has lessons
    const initialMode = schedulingMode.value;
    const initialCredits = parseInt(credits.value) || 0;
    const initialLectureUnits = parseInt(lectureUnits.value) || 0;
    const initialLabUnits = parseInt(labUnits.value) || 0;
    const hasLessons = {{ $subject->lessons()->count() > 0 ? 'true' : 'false' }};
    
    // Calculate initial total hours
    function calculateInitialTotalHours() {
        const mode = initialMode;
        if (mode === 'lab') {
            return initialCredits * 3; // Lab: 1 credit = 3 hours
        } else if (mode === 'lecture') {
            return initialCredits * 1; // Lecture: 1 credit = 1 hour
        } else if (mode === 'flexible') {
            return (initialLectureUnits * 1) + (initialLabUnits * 3);
        }
        return 0;
    }
    
    // Calculate new total hours based on current form values
    function calculateNewTotalHours() {
        const mode = schedulingMode.value;
        const lecture = parseInt(lectureUnits.value) || 0;
        const lab = parseInt(labUnits.value) || 0;
        const cred = parseInt(credits.value) || 0;
        
        if (mode === 'lab') {
            return cred * 3;
        } else if (mode === 'lecture') {
            return cred * 1;
        } else if (mode === 'flexible') {
            return (lecture * 1) + (lab * 3);
        }
        return 0;
    }
    
    // Check if total hours are being REDUCED (not just changed)
    function isTotalHoursReduced() {
        const currentHours = calculateInitialTotalHours();
        const newHours = calculateNewTotalHours();
        return newHours < currentHours;
    }
    
    // Event listeners
    schedulingMode.addEventListener('change', function(e) {
        const newMode = this.value;
        
        // If subject has lessons and mode is changing, BLOCK the change
        if (hasLessons && newMode !== initialMode) {
            e.preventDefault();
            // Revert to initial mode immediately
            schedulingMode.value = initialMode;
            // Show informational modal (no confirmation, just info)
            $('#schedulingModeChangeModal').modal('show');
            return;
        }
        
        updateDisplay();
    });
    
    // Always revert when modal closes
    $('#schedulingModeChangeModal').on('hidden.bs.modal', function() {
        schedulingMode.value = initialMode;
        updateDisplay();
    });
    
    // Credits change validation - BLOCK reducing hours when lessons exist
    credits.addEventListener('change', function(e) {
        const newValue = parseInt(this.value) || 0;
        const currentHours = calculateInitialTotalHours();
        const mode = schedulingMode.value;
        let newHours = 0;
        
        if (mode === 'lab') {
            newHours = newValue * 3;
        } else if (mode === 'lecture') {
            newHours = newValue * 1;
        }
        
        // BLOCK reduction if lessons exist
        if (hasLessons && newHours < currentHours) {
            e.preventDefault();
            // Revert to initial value immediately
            credits.value = initialCredits;
            // Show informational modal (no confirmation, just info)
            showCreditsChangeModal();
            return;
        }
        calculateTotals();
    });
    
    lectureUnits.addEventListener('input', function() {
        calculateTotals();
        // Check on blur for validation
    });
    
    labUnits.addEventListener('input', function() {
        calculateTotals();
        // Check on blur for validation
    });
    
    // Validate on blur for flexible mode units - BLOCK reducing hours when lessons exist
    lectureUnits.addEventListener('blur', function() {
        const newLecture = parseInt(this.value) || 0;
        const newLab = parseInt(labUnits.value) || 0;
        const currentHours = calculateInitialTotalHours();
        const newHours = (newLecture * 1) + (newLab * 3);
        
        // BLOCK reduction if lessons exist
        if (hasLessons && newHours < currentHours) {
            // Revert to initial values immediately
            lectureUnits.value = initialLectureUnits;
            labUnits.value = initialLabUnits;
            calculateTotals();
            // Show informational modal (no confirmation, just info)
            showCreditsChangeModal();
        }
    });
    
    labUnits.addEventListener('blur', function() {
        const newLecture = parseInt(lectureUnits.value) || 0;
        const newLab = parseInt(this.value) || 0;
        const currentHours = calculateInitialTotalHours();
        const newHours = (newLecture * 1) + (newLab * 3);
        
        // BLOCK reduction if lessons exist
        if (hasLessons && newHours < currentHours) {
            // Revert to initial values immediately
            lectureUnits.value = initialLectureUnits;
            labUnits.value = initialLabUnits;
            calculateTotals();
            // Show informational modal (no confirmation, just info)
            showCreditsChangeModal();
        }
    });
    
    function showCreditsChangeModal() {
        const currentHours = calculateInitialTotalHours();
        const newHours = calculateNewTotalHours();
        
        document.getElementById('currentTotalHours').textContent = currentHours + 'h';
        document.getElementById('newTotalHours').textContent = newHours + 'h';
        
        $('#creditsChangeModal').modal('show');
    }
    
    // Always revert when modal closes
    $('#creditsChangeModal').on('hidden.bs.modal', function() {
        // Ensure values are reverted
        credits.value = initialCredits;
        lectureUnits.value = initialLectureUnits;
        labUnits.value = initialLabUnits;
        calculateTotals();
    });

    // Initialize on page load
    updateDisplay();
});
</script>
@endsection
