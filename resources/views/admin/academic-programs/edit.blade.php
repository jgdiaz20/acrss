@extends('layouts.admin')
@section('content')

@if($academicProgram->type === 'diploma' && $weekendLessonCount > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Weekend Lessons Detected</h5>
        <p class="mb-2">
            <strong>{{ $weekendLessonCount }}</strong> lesson(s) are currently scheduled on weekends (Saturday/Sunday) for this Diploma Program.
        </p>
        <p class="mb-2">
            <strong>Important:</strong> If you change this program type to <strong>Senior High School</strong> or <strong>College</strong>, 
            you must first delete or reschedule these weekend lessons to weekdays (Monday-Friday).
        </p>
        <hr>
        <p class="mb-2"><strong>Weekend Lessons:</strong></p>
        <ul class="mb-2">
            @foreach($weekendLessons->take(5) as $lesson)
                <li>
                    <strong>{{ $lesson->class->name ?? 'Unknown Class' }}</strong> - 
                    {{ $lesson->subject->name ?? 'Unknown Subject' }} 
                    ({{ $lesson->weekday == 6 ? 'Saturday' : 'Sunday' }} 
                    {{ $lesson->start_time }} - {{ $lesson->end_time }})
                </li>
            @endforeach
            @if($weekendLessonCount > 5)
                <li><em>...and {{ $weekendLessonCount - 5 }} more</em></li>
            @endif
        </ul>
        <a href="{{ route('admin.lessons.index') }}?program_id={{ $academicProgram->id }}" class="btn btn-sm btn-warning">
            <i class="fas fa-calendar-alt"></i> View All Lessons
        </a>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        Edit Academic Program
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.academic-programs.update", [$academicProgram->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="name">Program Name</label>
                        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $academicProgram->name) }}" required>
                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                        <span class="help-block">e.g., Science, Technology, Engineering, and Mathematics</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="code">Program Code</label>
                        <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code', $academicProgram->code) }}" required>
                        @if($errors->has('code'))
                            <div class="invalid-feedback">
                                {{ $errors->first('code') }}
                            </div>
                        @endif
                        <span class="help-block">e.g., STEM, ABM, BSIT</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="type">Program Type</label>
                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                            <option value="">Select Program Type</option>
                            <option value="senior_high" data-duration="2" {{ old('type', $academicProgram->type) == 'senior_high' ? 'selected' : '' }}>Senior High School (2 years)</option>
                            <option value="diploma" data-duration="3" {{ old('type', $academicProgram->type) == 'diploma' ? 'selected' : '' }}>Diploma Program / TESDA (3 years)</option>
                            <option value="college" data-duration="4" {{ old('type', $academicProgram->type) == 'college' ? 'selected' : '' }}>College (4 years)</option>
                        </select>
                        @if($errors->has('type'))
                            <div class="invalid-feedback">
                                {{ $errors->first('type') }}
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="duration_years">Duration (Years)</label>
                        <input class="form-control {{ $errors->has('duration_years') ? 'is-invalid' : '' }}" type="number" name="duration_years" id="duration_years" value="{{ old('duration_years', $academicProgram->duration_years) }}" min="1" max="10" readonly required>
                        @if($errors->has('duration_years'))
                            <div class="invalid-feedback">
                                {{ $errors->first('duration_years') }}
                            </div>
                        @endif
                        <span class="help-block"><i class="fas fa-info-circle"></i> Duration is automatically set based on Program Type</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description" rows="3">{{ old('description', $academicProgram->description) }}</textarea>
                        @if($errors->has('description'))
                            <div class="invalid-feedback">
                                {{ $errors->first('description') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <select class="form-control {{ $errors->has('is_active') ? 'is-invalid' : '' }}" name="is_active" id="is_active">
                            <option value="1" {{ old('is_active', $academicProgram->is_active) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $academicProgram->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @if($errors->has('is_active'))
                            <div class="invalid-feedback">
                                {{ $errors->first('is_active') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
                <a href="{{ route('admin.academic-programs.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-fill duration based on program type
    $('#type').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var duration = selectedOption.data('duration');
        
        if (duration) {
            $('#duration_years').val(duration);
        }
    });
    
    // Trigger on page load if type is already selected
    if ($('#type').val()) {
        $('#type').trigger('change');
    }
});
</script>
@endsection
