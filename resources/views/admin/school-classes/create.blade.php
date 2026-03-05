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
            <a href="{{ route('admin.school-classes.index') }}">
                <i class="fas fa-school"></i> School Classes
            </a>
        </li>
        @if($preSelectedProgramId)
            @php
                $program = \App\AcademicProgram::find($preSelectedProgramId);
            @endphp
            @if($program)
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.school-classes.program', $program->type) }}">
                        <i class="fas fa-{{ $program->type == 'senior_high' ? 'graduation-cap' : 'university' }}"></i>
                        {{ $program->type == 'senior_high' ? 'Senior High School' : 'College' }}
                    </a>
                </li>
                @if($preSelectedGradeLevelId)
                    @php
                        $gradeLevel = \App\GradeLevel::find($preSelectedGradeLevelId);
                    @endphp
                    @if($gradeLevel)
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.school-classes.program.grade', [$program->type, $gradeLevel->id]) }}">
                                <i class="fas fa-layer-group"></i> {{ $gradeLevel->level_name }}
                            </a>
                        </li>
                    @endif
                @endif
            @endif
        @endif
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-plus"></i> Create Class
        </li>
    </ol>
</nav>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.schoolClass.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.school-classes.store") }}" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="program_id">Academic Program</label>
                        @php
                            $selectedProgram = $academicPrograms->where('id', $preSelectedProgramId)->first();
                        @endphp
                        <input type="text" class="form-control" value="{{ $selectedProgram->name ?? 'Unknown Program' }}" readonly>
                        <input type="hidden" name="program_id" value="{{ $preSelectedProgramId }}">
                        <small class="form-text text-muted">
                            <i class="fas fa-lock mr-1"></i>
                            Program is locked.
                        </small>
                    </div>
                </div>
                
                @if($preSelectedGradeLevelId)
                    <!-- Hidden input for pre-selected grade level -->
                    <input type="hidden" name="grade_level_id" value="{{ $preSelectedGradeLevelId }}">
                @else
                    <!-- Only show grade level field for Senior High School programs -->
                    <div class="col-md-6" id="grade_level_container" style="display: none;">
                        <div class="form-group">
                            <label class="required" for="grade_level_id" id="grade_level_label">Grade Level</label>
                            <select class="form-control {{ $errors->has('grade_level_id') ? 'is-invalid' : '' }}" name="grade_level_id" id="grade_level_id">
                                <option value="">Select Grade Level</option>
                            </select>
                            @if($errors->has('grade_level_id'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('grade_level_id') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="name">Section Name</label>
                        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                        <span class="help-block">e.g., STEM-A, ABM-B, Computer Engineering 1-A</span>
                    </div>
                </div>
            </div>
            
            <!-- Hidden fields -->
            <input type="hidden" name="is_active" value="1">
            
            <div class="form-group">
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> {{ trans('global.save') }}
                </button>
                @if($preSelectedProgramId && $preSelectedGradeLevelId)
                    @php
                        $program = \App\AcademicProgram::find($preSelectedProgramId);
                    @endphp
                    @if($program)
                        <a href="{{ route('admin.school-classes.program.grade', [$program->type, $preSelectedGradeLevelId]) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    @endif
                @elseif($preSelectedProgramId)
                    @php
                        $program = \App\AcademicProgram::find($preSelectedProgramId);
                    @endphp
                    @if($program)
                        <a href="{{ route('admin.school-classes.program', $program->type) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    @endif
                @else
                    <a href="{{ route('admin.school-classes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var preSelectedGradeLevelId = '{{ $preSelectedGradeLevelId }}';
    
    // Only run the program change logic if we don't have pre-selected values
    if (!preSelectedGradeLevelId) {
        // Load grade levels when program changes
        $('#program_id').change(function() {
            var programId = $(this).val();
            var gradeLevelSelect = $('#grade_level_id');
            var gradeLevelContainer = $('#grade_level_container');
            
            // Get the selected program's type
            var selectedOption = $(this).find('option:selected');
            var programName = selectedOption.text();
            
            // Check if it's a senior high school program
            if (programName.toLowerCase().includes('senior high') || 
                programName.toLowerCase().includes('stem') || 
                programName.toLowerCase().includes('abm') || 
                programName.toLowerCase().includes('humss') || 
                programName.toLowerCase().includes('gas')) {
                
                // Show grade level field for senior high school programs
                gradeLevelContainer.show();
                gradeLevelSelect.attr('required', 'required');
                
                gradeLevelSelect.html('<option value="">Loading...</option>');
                
                if (programId) {
                    $.ajax({
                        url: '{{ route("admin.admin.grade-levels.by-program", "") }}/' + programId,
                        type: 'GET',
                        success: function(data) {
                            gradeLevelSelect.html('<option value="">Select Grade Level</option>');
                            $.each(data, function(key, value) {
                                gradeLevelSelect.append('<option value="' + value.id + '">' + value.level_name + '</option>');
                            });
                        },
                        error: function() {
                            gradeLevelSelect.html('<option value="">Error loading grade levels</option>');
                        }
                    });
                } else {
                    gradeLevelSelect.html('<option value="">Select Grade Level</option>');
                }
            } else {
                // Hide grade level field for all other programs (college programs)
                gradeLevelContainer.hide();
                gradeLevelSelect.removeAttr('required');
                gradeLevelSelect.val('');
            }
        });
        
        // Trigger change on page load if a program is already selected
        if ($('#program_id').val()) {
            $('#program_id').trigger('change');
        }
    }
});
</script>
@endsection