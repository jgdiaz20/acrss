@extends('layouts.admin')
@section('content')

@php
    // Check if there are scheduling conflict errors
    $hasConflictError = false;
    $conflictMessage = '';
    
    if ($errors->has('start_time')) {
        $errorMessage = $errors->first('start_time');
        if (str_contains($errorMessage, 'Scheduling conflict') || 
            str_contains($errorMessage, 'Conflict with') ||
            str_contains($errorMessage, 'already scheduled')) {
            $hasConflictError = true;
            $conflictMessage = $errorMessage;
        }
    }
@endphp

@if($hasConflictError)
    <div class="alert alert-conflict alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-clock mr-3" style="font-size: 24px;"></i>
            <div>
                <strong style="font-size: 16px;">Scheduling Conflict Detected</strong>
                <p class="mb-0 mt-1">{{ $conflictMessage }}</p>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.lesson.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.lessons.update", [$lesson->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="class_id">{{ trans('cruds.lesson.fields.class') }}</label>
                <select class="form-control select2 {{ $errors->has('class') ? 'is-invalid' : '' }}" name="class_id" id="class_id" required>
                    @foreach($classes as $id => $class)
                        <option value="{{ $id }}" {{ ($lesson->class ? $lesson->class->id : old('class_id')) == $id ? 'selected' : '' }}>{{ $class }}</option>
                    @endforeach
                </select>
                @if($errors->has('class'))
                    <div class="invalid-feedback">
                        {{ $errors->first('class') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.lesson.fields.class_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="subject_id">Subject</label>
                <select class="form-control select2 {{ $errors->has('subject_id') ? 'is-invalid' : '' }}" name="subject_id" id="subject_id" required>
                    <option value="">-- Select Subject --</option>
                    @foreach($subjects as $id => $subject)
                        <option value="{{ $id }}" {{ ($lesson->subject ? $lesson->subject->id : old('subject_id')) == $id ? 'selected' : '' }}>{{ $subject }}</option>
                    @endforeach
                </select>
                @if($errors->has('subject_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('subject_id') }}
                    </div>
                @endif
                <span class="help-block">Select the subject for this class schedule</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="teacher_id">{{ trans('cruds.lesson.fields.teacher') }}</label>
                <select class="form-control select2 {{ $errors->has('teacher') ? 'is-invalid' : '' }}" name="teacher_id" id="teacher_id" required>
                    @foreach($teachers as $id => $teacher)
                        <option value="{{ $id }}" {{ ($lesson->teacher ? $lesson->teacher->id : old('teacher_id')) == $id ? 'selected' : '' }}>{{ $teacher }}</option>
                    @endforeach
                </select>
                @if($errors->has('teacher'))
                    <div class="invalid-feedback">
                        {{ $errors->first('teacher') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.lesson.fields.teacher_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="room_id">Room</label>
                <select class="form-control select2 {{ $errors->has('room') ? 'is-invalid' : '' }}" name="room_id" id="room_id" required>
                    @foreach($rooms as $id => $room)
                        <option value="{{ $id }}" {{ ($lesson->room ? $lesson->room->id : old('room_id')) == $id ? 'selected' : '' }}>{{ $room }}</option>
                    @endforeach
                </select>
                @if($errors->has('room'))
                    <div class="invalid-feedback">
                        {{ $errors->first('room') }}
                    </div>
                @endif
                <span class="help-block">Select the room for this lesson</span>
            </div>
            <div class="form-group">
                <label class="required" for="weekday">{{ trans('cruds.lesson.fields.weekday') }}</label>
                <select class="form-control {{ $errors->has('weekday') ? 'is-invalid' : '' }}" name="weekday" id="weekday" required>
                    <option value="">Select Day</option>
                    @foreach(\App\Lesson::WEEK_DAYS as $key => $day)
                        <option value="{{ $key }}" data-is-weekend="{{ in_array($key, [6, 7]) ? 'true' : 'false' }}" {{ (old('weekday', $lesson->weekday) == $key) ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                </select>
                @if($errors->has('weekday'))
                    <div class="invalid-feedback">
                        {{ $errors->first('weekday') }}
                    </div>
                @endif
                <span class="help-block" id="weekday-help">{{ trans('cruds.lesson.fields.weekday_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_time">{{ trans('cruds.lesson.fields.start_time') }}</label>
                <input class="form-control lesson-timepicker {{ $errors->has('start_time') ? 'is-invalid' : '' }}" type="text" name="start_time" id="start_time" value="{{ old('start_time', $lesson->start_time) }}" required>
                @if($errors->has('start_time'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_time') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.lesson.fields.start_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_time">{{ trans('cruds.lesson.fields.end_time') }}</label>
                <input class="form-control lesson-timepicker {{ $errors->has('end_time') ? 'is-invalid' : '' }}" type="text" name="end_time" id="end_time" value="{{ old('end_time', $lesson->end_time) }}" required>
                @if($errors->has('end_time'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_time') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.lesson.fields.end_time_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> {{ trans('global.save') }}
                </button>
                <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Dynamic weekday filtering based on program type
    function updateWeekdayOptions(programType) {
        const weekdaySelect = $('#weekday');
        const currentValue = weekdaySelect.val();
        const isCurrentWeekend = weekdaySelect.find('option:selected').data('is-weekend') === 'true';
        
        // Show/hide weekend options based on program type
        weekdaySelect.find('option').each(function() {
            const isWeekend = $(this).data('is-weekend') === 'true';
            
            if (isWeekend && programType !== 'diploma') {
                $(this).hide();
                // If weekend day is currently selected, clear it
                if ($(this).val() === currentValue) {
                    weekdaySelect.val('');
                }
            } else {
                $(this).show();
            }
        });
        
        // Clear weekend validation error if switching to Diploma with weekend selected
        if (programType === 'diploma' && isCurrentWeekend && currentValue) {
            weekdaySelect.removeClass('is-invalid');
            const $feedback = weekdaySelect.closest('.form-group').find('.invalid-feedback');
            if ($feedback.length) {
                $feedback.text('').hide().remove();
            }
        }
        
        // Also clear error if switching to Diploma (regardless of weekend selection)
        // This handles the case where error exists from previous submission
        if (programType === 'diploma') {
            weekdaySelect.removeClass('is-invalid');
            const $feedback = weekdaySelect.closest('.form-group').find('.invalid-feedback');
            if ($feedback.length && ($feedback.text().includes('Weekend') || $feedback.text().includes('weekend'))) {
                $feedback.text('').hide().remove();
            }
        }
        
        // Update help text
        if (programType === 'diploma') {
            $('#weekday-help').html('<span class="text-success"><i class="fas fa-check-circle"></i> Diploma programs can schedule classes on weekends (Saturday/Sunday)</span>');
        } else {
            $('#weekday-help').html('{{ trans('cruds.lesson.fields.weekday_helper') }}');
        }
    }
    
    // Fetch program type when class is selected
    $('#class_id').on('change', function() {
        const classId = $(this).val();
        
        if (classId) {
            $.get('{{ route("admin.school-classes.program-type", ":id") }}'.replace(':id', classId), function(data) {
                updateWeekdayOptions(data.program_type);
            });
        } else {
            // Reset to show only weekdays if no class selected
            updateWeekdayOptions('');
        }
    });
    
    // Trigger on page load if class is already selected
    if ($('#class_id').val()) {
        $('#class_id').trigger('change');
    }
    
    // Dynamic filtering based on subject selection
    $('#subject_id').on('change', function() {
        var subjectId = $(this).val();
        var currentTeacherId = $('#teacher_id').val(); // Preserve current teacher selection
        
        if (subjectId) {
            // Update teachers based on subject
            $.get('{{ route("admin.lessons.get-teachers-for-subject") }}', {
                subject_id: subjectId
            }, function(data) {
                var teacherSelect = $('#teacher_id');
                var currentValue = teacherSelect.val(); // Get current value before clearing
                teacherSelect.empty();
                teacherSelect.append('<option value="">Select Teacher</option>');
                
                $.each(data.teachers, function(id, name) {
                    var selected = (id == currentValue) ? ' selected' : '';
                    teacherSelect.append('<option value="' + id + '"' + selected + '>' + name + '</option>');
                });
                
                // Only trigger change if value actually changed
                if (teacherSelect.val() !== currentValue) {
                    teacherSelect.trigger('change');
                }
            });
            
            // Update rooms based on subject requirements
            $.get('{{ route("admin.lessons.get-rooms-for-subject") }}', {
                subject_id: subjectId
            }, function(data) {
                var roomSelect = $('#room_id');
                var currentRoomValue = roomSelect.val(); // Preserve current room selection
                roomSelect.empty();
                roomSelect.append('<option value="">Select Room</option>');
                
                $.each(data.rooms, function(id, name) {
                    var selected = (id == currentRoomValue) ? ' selected' : '';
                    roomSelect.append('<option value="' + id + '"' + selected + '>' + name + '</option>');
                });
            });
        } else {
            // Reset to all options if no subject selected
            $.get('{{ route("admin.lessons.get-teachers-for-subject") }}', {}, function(data) {
                var teacherSelect = $('#teacher_id');
                var currentValue = teacherSelect.val();
                teacherSelect.empty();
                teacherSelect.append('<option value="">Select Teacher</option>');
                
                $.each(data.teachers, function(id, name) {
                    var selected = (id == currentValue) ? ' selected' : '';
                    teacherSelect.append('<option value="' + id + '"' + selected + '>' + name + '</option>');
                });
            });
            
            $.get('{{ route("admin.lessons.get-rooms-for-subject") }}', {}, function(data) {
                var roomSelect = $('#room_id');
                var currentValue = roomSelect.val();
                roomSelect.empty();
                roomSelect.append('<option value="">Select Room</option>');
                
                $.each(data.rooms, function(id, name) {
                    var selected = (id == currentValue) ? ' selected' : '';
                    roomSelect.append('<option value="' + id + '"' + selected + '>' + name + '</option>');
                });
            });
        }
    });
    
    // Trigger change on page load if subject is already selected
    @if(old('subject_id') || $lesson->subject_id)
        $('#subject_id').trigger('change');
    @endif
});
</script>
@endsection