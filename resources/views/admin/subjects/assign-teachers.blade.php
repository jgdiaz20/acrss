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
            <i class="fas fa-users"></i> Assign Teachers
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-chalkboard-teacher text-primary mr-2"></i>
                Assign Teachers to {{ $subject->name }}
            </h1>
        </div>
        <div class="col-8 justify-content-between align-items-center">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-outline-primary">
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
            <i class="fas fa-users text-success mr-2"></i>
            Teacher Assignment
        </h3>
    </div>
    <div class="card-body">
        <!-- Success Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
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

        <form method="POST" action="{{ route('admin.subjects.update-teachers', $subject->id) }}" id="teacherAssignmentForm">
            @csrf
            <!-- Add cache-busting parameter -->
            <input type="hidden" name="cache_buster" value="{{ time() }}">
            
            <!-- Instructions -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle mr-2"></i>Teacher Assignment Instructions</h5>
                        <p class="mb-0">
                            Select one or more teachers who can teach this subject. You can assign just one teacher or multiple teachers as needed.
                            <br><strong>Note:</strong> Teachers assigned here will be available when creating lessons for this subject.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Teachers Grid -->
            <div class="row">
                @foreach($teachers as $teacher)
                    <div class="col-md-6 mb-3">
                        <div class="card teacher-card">
                            <div class="card-body">
                                @php
                                    $isAssigned = $subject->teachers->contains($teacher->id);
                                    $pivotData = $isAssigned ? $subject->teachers->find($teacher->id)->pivot : null;
                                @endphp
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input teacher-checkbox" type="checkbox" 
                                           name="teachers[{{ $teacher->id }}][teacher_id]" 
                                           value="{{ $teacher->id }}" 
                                           id="teacher_{{ $teacher->id }}"
                                           {{ $isAssigned ? 'checked' : '' }}
                                           onchange="toggleTeacherFields({{ $teacher->id }})">
                                    <label class="form-check-label" for="teacher_{{ $teacher->id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $teacher->name }}</div>
                                                <small class="text-muted">Teacher</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="teacher-fields" id="fields_{{ $teacher->id }}" 
                                     style="{{ $isAssigned ? '' : 'display: none;' }}">
                                    
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="teachers[{{ $teacher->id }}][is_primary]" 
                                                   value="1" 
                                                   id="primary_{{ $teacher->id }}"
                                                   {{ $pivotData && $pivotData->is_primary ? 'checked' : '' }}>
                                            <label class="form-check-label" for="primary_{{ $teacher->id }}">
                                                <i class="fas fa-star text-warning mr-1"></i>
                                                Primary Subject Teacher
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Mark as primary teacher for this subject</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="experience_{{ $teacher->id }}">Experience (Years)</label>
                                        <input type="number" class="form-control" 
                                               name="teachers[{{ $teacher->id }}][experience_years]" 
                                               id="experience_{{ $teacher->id }}"
                                               min="0" max="50"
                                               value="{{ $pivotData ? $pivotData->experience_years : 0 }}">
                                        <small class="form-text text-muted">Years of experience teaching this subject</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="notes_{{ $teacher->id }}">Notes</label>
                                        <textarea class="form-control" 
                                                  name="teachers[{{ $teacher->id }}][notes]" 
                                                  id="notes_{{ $teacher->id }}" 
                                                  rows="2" 
                                                  placeholder="Any additional notes about this teacher's assignment...">{{ $pivotData ? $pivotData->notes : '' }}</textarea>
                                        <small class="form-text text-muted">Optional notes about this teacher's assignment</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($teachers->count() == 0)
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>No teachers found.</strong> Please create teachers first before assigning them to subjects.
                    <div class="mt-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Create Teacher
                        </a>
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
                <div class="form-group mt-4">
                <div class="card-footer bg-light">
                    <div class="row">
                        <!--
                            We use col-12 to ensure full width, d-flex to enable flex container,
                            and justify-content-between to push the Save and Cancel buttons to opposite ends.
                        -->
                        <div class="col-8 justify-content-between align-items-center">
                            
                            <!-- Left Side: Save Button (Primary Action) -->
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save mr-2"></i> Save Teacher Assignments
                            </button>
                            
                            <!-- Right Side: Cancel Button (Secondary Action) -->
                            <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                            
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function toggleTeacherFields(teacherId) {
    const checkbox = document.getElementById('teacher_' + teacherId);
    const fields = document.getElementById('fields_' + teacherId);
    
    if (checkbox.checked) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
        // Clear the form data when unchecked
        document.getElementById('primary_' + teacherId).checked = false;
        document.getElementById('experience_' + teacherId).value = '0';
        document.getElementById('notes_' + teacherId).value = '';
    }
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.teacher-checkbox');
    checkboxes.forEach(function(checkbox) {
        const teacherId = checkbox.id.replace('teacher_', '');
        toggleTeacherFields(teacherId);
    });
    
    // Handle form submission to remove unchecked teacher data
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        console.log('Form submission started');
        
        const checkboxes = document.querySelectorAll('.teacher-checkbox');
        let checkedCount = 0;
        
        console.log('Total checkboxes found:', checkboxes.length);
        
        checkboxes.forEach(function(checkbox) {
            if (!checkbox.checked) {
                // Remove all form fields for unchecked teachers
                const teacherId = checkbox.id.replace('teacher_', '');
                const teacherInputs = document.querySelectorAll(`[name*="teachers[${teacherId}]"]`);
                console.log('Removing inputs for unchecked teacher:', teacherId, 'inputs found:', teacherInputs.length);
                teacherInputs.forEach(function(input) {
                    input.remove();
                });
            } else {
                checkedCount++;
                console.log('Checked teacher:', checkbox.id);
            }
        });
        
        console.log('Total checked teachers:', checkedCount);
        
        // Add a hidden input to indicate if all teachers were removed
        if (checkedCount === 0) {
            console.log('No teachers selected, adding remove_all_teachers flag');
            
            // Remove any existing hidden input
            const existingHidden = document.querySelector('input[name="remove_all_teachers"]');
            if (existingHidden) {
                existingHidden.remove();
            }
            
            // Add hidden input to indicate all teachers removed
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_all_teachers';
            hiddenInput.value = '1';
            form.appendChild(hiddenInput);
        }
        
        // Log final form data before submission
        const formData = new FormData(form);
        console.log('Final form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
    });
    
    // Add real-time cache clearing functionality
    // This will help ensure that when this modal is opened, fresh data is loaded
    window.clearTeacherAssignmentCache = function(subjectId) {
        if (typeof fetch !== 'undefined') {
            fetch('/admin/lessons/get-teachers-for-subject?subject_id=' + subjectId + '&clear_cache=true', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(response => response.json()).then(data => {
                console.log('Cache cleared for subject:', subjectId);
            }).catch(error => {
                console.log('Cache clear request failed:', error);
            });
        }
    };
    
    // Clear cache when modal/page loads to ensure fresh data
    @if(isset($subject))
        window.clearTeacherAssignmentCache({{ $subject->id }});
    @endif
    
    // Prevent browser back button from showing cached form data
    window.addEventListener('pageshow', function(event) {
        // If the page was loaded from cache (back button), reload it
        if (event.persisted) {
            console.log('Page loaded from cache, refreshing...');
            window.location.reload();
        }
    });
    
    // Add form submission handler to prevent back button issues
    const form = document.getElementById('teacherAssignmentForm');
    if (form) {
        form.addEventListener('submit', function() {
            // Disable form resubmission
            form.style.display = 'none';
            // Show loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'text-center p-4';
            loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin fa-2x"></i><br><p>Updating teacher assignments...</p>';
            form.parentNode.appendChild(loadingDiv);
        });
    }
});
</script>
@endsection
