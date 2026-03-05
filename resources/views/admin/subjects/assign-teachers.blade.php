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
        <div class="col-md-4">
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

            <!-- Search Section -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body py-2">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>
                                        <input type="text" 
                                               class="form-control" 
                                               id="teacher-search" 
                                               placeholder="Search teachers by name..."
                                               onkeyup="searchTeachers()">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    onclick="clearSearch()"
                                                    title="Clear search">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        <span id="teacher-count">{{ $teachers->count() }}</span> teacher(s) available
                                        <span id="filtered-count" style="display: none;"> | <span id="visible-count">0</span> matching</span>
                                    </small>
                                </div>
                            </div>
                        </div>
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
                                @endphp
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input teacher-checkbox" type="checkbox" 
                                           name="teachers[{{ $teacher->id }}][teacher_id]" 
                                           value="{{ $teacher->id }}" 
                                           id="teacher_{{ $teacher->id }}"
                                           data-lesson-count="{{ $teacherLessonCounts[$teacher->id] ?? 0 }}"
                                           data-teacher-name="{{ $teacher->name }}"
                                           {{ $isAssigned ? 'checked' : '' }}>
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
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            
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

<!-- Warning Modal for Teachers with Lessons -->
<div class="modal fade" id="teacherHasLessonsModal" tabindex="-1" role="dialog" aria-labelledby="teacherHasLessonsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="teacherHasLessonsModalLabel">
          <i class="fas fa-exclamation-triangle mr-1"></i> Cannot Remove Teacher
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong id="teacherNameDisplay"></strong> currently has <strong id="lessonCountDisplay"></strong> active lesson(s) for this subject.</p>
        <div class="alert alert-info mt-3">
          <i class="fas fa-info-circle mr-1"></i> Please reassign these lessons to another teacher first before removing this teacher from the subject.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">
          Understood
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
// Search functionality
function searchTeachers() {
    const searchTerm = document.getElementById('teacher-search').value.toLowerCase().trim();
    const teacherCards = document.querySelectorAll('.teacher-card');
    let visibleCount = 0;
    
    teacherCards.forEach(card => {
        const teacherName = card.querySelector('.font-weight-bold').textContent.toLowerCase();
        const parentCol = card.closest('.col-md-6');
        
        if (searchTerm === '' || teacherName.includes(searchTerm)) {
            parentCol.style.display = '';
            visibleCount++;
        } else {
            parentCol.style.display = 'none';
        }
    });
    
    // Update count display
    const filteredCountSpan = document.getElementById('filtered-count');
    const visibleCountSpan = document.getElementById('visible-count');
    
    if (searchTerm === '') {
        filteredCountSpan.style.display = 'none';
    } else {
        filteredCountSpan.style.display = '';
        visibleCountSpan.textContent = visibleCount;
    }
}

function clearSearch() {
    document.getElementById('teacher-search').value = '';
    searchTeachers();
}

// Store initial checkbox states
const initialCheckboxStates = {};

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Store initial checkbox states
    document.querySelectorAll('.teacher-checkbox').forEach(checkbox => {
        initialCheckboxStates[checkbox.id] = checkbox.checked;
    });

    // Add change event listener to all teacher checkboxes
    document.querySelectorAll('.teacher-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            const wasChecked = initialCheckboxStates[this.id];
            const isNowChecked = this.checked;
            const lessonCount = parseInt(this.getAttribute('data-lesson-count'));
            const teacherName = this.getAttribute('data-teacher-name');

            // If trying to uncheck a teacher who was previously assigned and has lessons
            if (wasChecked && !isNowChecked && lessonCount > 0) {
                // Prevent unchecking
                e.preventDefault();
                this.checked = true;

                // Show warning modal
                document.getElementById('teacherNameDisplay').textContent = teacherName;
                document.getElementById('lessonCountDisplay').textContent = lessonCount;
                $('#teacherHasLessonsModal').modal('show');
            }
        });
    });

    // Handle form submission to remove unchecked teacher data
    const form = document.getElementById('teacherAssignmentForm');
    if (form) {
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
            
            // Disable form resubmission
            form.style.display = 'none';
            // Show loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'text-center p-4';
            loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin fa-2x"></i><br><p>Updating teacher assignments...</p>';
            form.parentNode.appendChild(loadingDiv);
        });
    }
    
    // Prevent browser back button from showing cached form data
    window.addEventListener('pageshow', function(event) {
        // If the page was loaded from cache (back button), reload it
        if (event.persisted) {
            console.log('Page loaded from cache, refreshing...');
            window.location.reload();
        }
    });
});
</script>
@endsection
