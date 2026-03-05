@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Create Subject
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.subjects.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">Subject Name</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">Enter the full name of the subject (e.g., Computer Programming)</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="code">Subject Code</label>
                <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code') }}" required>
                @if($errors->has('code'))
                    <div class="invalid-feedback">
                        {{ $errors->first('code') }}
                    </div>
                @endif
                <span class="help-block">Enter a unique code for the subject (e.g., COMPROG)</span>
            </div>
            
            
            <div class="form-group">
                <label class="required" for="credits">Total Credits</label>
                <input class="form-control {{ $errors->has('credits') ? 'is-invalid' : '' }}" type="number" name="credits" id="credits" value="{{ old('credits', 3) }}" min="1" max="3" required>
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
                        <option value="{{ $key }}" {{ old('scheduling_mode') == $key ? 'selected' : '' }}>{{ $mode }}</option>
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
                            <input class="form-control {{ $errors->has('lecture_units') ? 'is-invalid' : '' }}" type="number" name="lecture_units" id="lecture_units" value="{{ old('lecture_units', 0) }}" min="0" max="10">
                            @if($errors->has('lecture_units'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('lecture_units') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lab_units">Laboratory Units</label>
                            <input class="form-control {{ $errors->has('lab_units') ? 'is-invalid' : '' }}" type="number" name="lab_units" id="lab_units" value="{{ old('lab_units', 0) }}" min="0" max="10">
                            @if($errors->has('lab_units'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('lab_units') }}
                                </div>
                            @endif
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
                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </div>
                @endif
                <span class="help-block">Select the type of subject</span>
            </div>
            
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> Create Subject
                </button>
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const schedulingMode = document.getElementById('scheduling_mode');
    const credits = document.getElementById('credits');
    const creditsHelp = document.getElementById('credits-help');
    const lectureUnits = document.getElementById('lecture_units');
    const labUnits = document.getElementById('lab_units');
    const flexibleFields = document.getElementById('flexible-fields');
    const lectureHoursDisplay = document.getElementById('lecture-hours-display');
    const labHoursDisplay = document.getElementById('lab-hours-display');
    const totalHoursDisplay = document.getElementById('total-hours-display');
    const hoursSummary = document.getElementById('hours-summary');

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
            // Lab mode: credits = lab units
            const totalCredits = lab;
            credits.value = totalCredits;
        } else if (mode === 'lecture') {
            // Lecture mode: credits = lecture units
            const totalCredits = lecture;
            credits.value = totalCredits;
        } else if (mode === 'flexible') {
            // Flexible mode: credits = lecture + lab
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

    // Event listeners
    schedulingMode.addEventListener('change', updateDisplay);
    lectureUnits.addEventListener('input', calculateTotals);
    labUnits.addEventListener('input', calculateTotals);

    // Initialize on page load
    updateDisplay();
});
</script>
@endsection
