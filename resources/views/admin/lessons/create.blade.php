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
            <a href="{{ route('admin.lessons.index') }}">
                <i class="fas fa-clock"></i> Schedules
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-plus"></i> Create Schedule
        </li>
    </ol>
</nav>

{{-- Single consolidated error display --}}
@if ($errors->any())
    <div class="alert alert-danger mb-3">
        <i class="fas fa-exclamation-circle"></i>
        <strong>Unable to create schedule</strong>
        <p class="mb-0 mt-2">Please correct the errors highlighted below.</p>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-plus mr-2"></i>
            {{ trans('global.create') }} {{ trans('cruds.lesson.title_singular') }}
        </h3>
    </div>

    <div class="card-body">
        @php
            $prefillWeekday = request()->get('weekday');
            $prefillRoomId = request()->get('room_id');
            $prefillStart = request()->get('start_time');
        @endphp
        @if($prefillWeekday !== null || $prefillRoomId || $prefillStart)
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <div>
                Prefilled from master timetable
                @if($prefillWeekday !== null)
                    • Day: {{ \App\Lesson::WEEK_DAYS[$prefillWeekday] ?? $prefillWeekday }}
                @endif
                @if($prefillRoomId)
                    • Room: {{ $rooms[$prefillRoomId] ?? ('ID '.$prefillRoomId) }}
                @endif
                @if($prefillStart)
                    • Start: {{ $prefillStart }}
                @endif
            </div>
        </div>
        @endif
        <form method="POST" action="{{ route("admin.lessons.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="class_id">{{ trans('cruds.lesson.fields.section') }}</label>
                <select class="form-control select2 {{ $errors->has('class') ? 'is-invalid' : '' }}" name="class_id" id="class_id" required>
                    @foreach($classes as $id => $class)
                        <option value="{{ $id }}" {{ old('class_id') == $id ? 'selected' : '' }}>{{ $class }}</option>
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
                    @foreach($subjects as $id => $subject)
                        <option value="{{ $id }}" {{ old('subject_id') == $id ? 'selected' : '' }}>{{ $subject }}</option>
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
                <label class="required" for="lesson_type">Lesson Type</label>
                <select class="form-control {{ $errors->has('lesson_type') ? 'is-invalid' : '' }}" name="lesson_type" id="lesson_type" required>
                    <option value="">-- Select Type --</option>
                    @foreach(\App\Lesson::LESSON_TYPES as $key => $type)
                        <option value="{{ $key }}" {{ old('lesson_type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @if($errors->has('lesson_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('lesson_type') }}
                    </div>
                @endif
                <span class="help-block" id="lesson-type-help">Select whether this is a lecture or laboratory session</span>
            </div>
            
            {{-- Hours Tracking Display --}}
            <div id="hours-tracking-container" style="display: none;" class="mb-3">
                <div class="card border-info">
                    <div class="card-header bg-info text-white py-2">
                        <h6 class="mb-0"><i class="fas fa-clock mr-2"></i>Hours Tracking</h6>
                    </div>
                    <div class="card-body p-3">
                        <div id="hours-tracking-content">
                            <div id="lecture-hours-section" class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="font-weight-bold">Lecture Hours:</span>
                                    <span id="lecture-hours-text" class="badge badge-secondary">0h / 0h</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div id="lecture-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <span id="lecture-progress-text">0%</span>
                                    </div>
                                </div>
                                <small id="lecture-remaining-text" class="text-muted">0h remaining</small>
                            </div>
                            
                            <div id="lab-hours-section" class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="font-weight-bold">Lab Hours:</span>
                                    <span id="lab-hours-text" class="badge badge-secondary">0h / 0h</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div id="lab-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <span id="lab-progress-text">0%</span>
                                    </div>
                                </div>
                                <small id="lab-remaining-text" class="text-muted">0h remaining</small>
                            </div>
                            
                            <div id="hours-error-message" class="alert alert-danger mt-2" style="display: none;">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span id="hours-error-text"></span>
                            </div>
                            
                            <div id="hours-info-message" class="alert alert-info mt-2" style="display: none;">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span id="hours-info-text"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="required" for="teacher_id">{{ trans('cruds.lesson.fields.teacher') }}</label>
                <select class="form-control select2 {{ $errors->has('teacher_id') ? 'is-invalid' : '' }}" name="teacher_id" id="teacher_id" required>
                    <option value="">-- Select Teacher --</option>
                    @foreach($teachers as $id => $teacher)
                        <option value="{{ $id }}" {{ old('teacher_id') == $id ? 'selected' : '' }}>{{ $teacher }}</option>
                    @endforeach
                </select>
                @if($errors->has('teacher_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('teacher_id') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.lesson.fields.teacher_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="room_id">Room</label>
                @php $isLockedRoom = request('room_id'); @endphp
                <select class="form-control select2 {{ $errors->has('room_id') ? 'is-invalid' : '' }}" name="room_id" id="room_id" {{ $isLockedRoom ? 'disabled' : '' }} required>
                    <option value="">-- Select Room --</option>
                    @foreach($rooms as $id => $room)
                        <option value="{{ $id }}" {{ old('room_id', request('room_id')) == $id ? 'selected' : '' }}>{{ $room }}</option>
                    @endforeach
                </select>
                @if($isLockedRoom)
                    <input type="hidden" name="room_id" value="{{ request('room_id') }}">
                    <small class="form-text text-muted">Room locked from master timetable selection.</small>
                @endif
                @if($errors->has('room_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('room_id') }}
                    </div>
                @endif
                <span class="help-block">Select a room for this class schedule</span>
            </div>
            <div class="form-group">
                <label class="required" for="weekday">{{ trans('cruds.lesson.fields.weekday') }}</label>
                <select class="form-control {{ $errors->has('weekday') ? 'is-invalid' : '' }}" name="weekday" id="weekday" required>
                    <option value="">Select Day</option>
                    @foreach(\App\Lesson::WEEK_DAYS as $key => $day)
                        <option value="{{ $key }}" {{ (string) old('weekday', request('weekday')) === (string) $key ? 'selected' : '' }}>{{ $day }}</option>
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
                <input class="form-control lesson-timepicker {{ $errors->has('start_time') ? 'is-invalid' : '' }}" type="text" name="start_time" id="start_time" value="{{ old('start_time', request('start_time')) }}" required>
                @if($errors->has('start_time'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_time') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.lesson.fields.start_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_time">{{ trans('cruds.lesson.fields.end_time') }}</label>
                <input class="form-control lesson-timepicker {{ $errors->has('end_time') || $errors->has('duration_hours') ? 'is-invalid' : '' }}" type="text" name="end_time" id="end_time" value="{{ old('end_time', request('end_time')) }}" required>
                @if($errors->has('end_time'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_time') }}
                    </div>
                @endif
                @if($errors->has('duration_hours'))
                    <div class="invalid-feedback" style="display: block;">
                        {{ $errors->first('duration_hours') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.lesson.fields.end_time_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" id="submit-lesson">
                    <i class="fas fa-save"></i> {{ trans('global.save') }}
                </button>
                <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <a href="{{ route('admin.room-management.master-timetable.show', request('weekday', 1)) }}" class="btn btn-light">
                    <i class="fas fa-th"></i> Back to Master Timetable
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Create subjects lookup object for instant lesson type updates
    window.subjectsLookup = {
        @foreach($subjectsCollection as $subject)
            {{ $subject->id }}: {
                id: {{ $subject->id }},
                scheduling_mode: '{{ $subject->scheduling_mode ?? '' }}'
            },
        @endforeach
    };
    console.log('Subjects lookup initialized:', window.subjectsLookup);

$(document).ready(function() {
    // When coming from master timetable, the room is locked (prefilled and disabled)
    const IS_ROOM_LOCKED = {{ request('room_id') ? 'true' : 'false' }};
    
    // No weekend filtering needed - all program types can schedule weekend classes
    function updateWeekdayOptions(programType) {
        // Function kept for compatibility but no filtering applied
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
        
        if (subjectId) {
            // Show loading indicators
            $('#teacher_id').html('<option value="">Loading teachers...</option>');
            if (!IS_ROOM_LOCKED) {
                $('#room_id').html('<option value="">Loading rooms...</option>');
            }
            
            // Update teachers based on subject
            $.get('{{ route("admin.lessons.get-teachers-for-subject") }}', {
                subject_id: subjectId
            }, function(data) {
                var teacherSelect = $('#teacher_id');
                teacherSelect.empty();
                
                // Check if no teachers are assigned to this subject
                if (data.teachers && data.teachers.no_teachers) {
                    teacherSelect.append('<option value="">' + data.teachers.no_teachers + '</option>');
                    teacherSelect.prop('disabled', true);
                    // Show a warning message
                    if ($('#teacher-warning').length === 0) {
                        teacherSelect.after('<div id="teacher-warning" class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle"></i> ' + data.teachers.no_teachers + '</div>');
                    }
                } else {
                    teacherSelect.prop('disabled', false);
                    $('#teacher-warning').remove(); // Remove warning if it exists
                    teacherSelect.append('<option value="">-- Select Teacher --</option>');
                    
                    $.each(data.teachers, function(id, name) {
                        teacherSelect.append('<option value="' + id + '">' + name + '</option>');
                    });
                }
                
                // Trigger change to update room options
                teacherSelect.trigger('change');
            }).fail(function() {
                $('#teacher_id').html('<option value="">Error loading teachers</option>');
                $('#teacher_id').prop('disabled', true);
            });
            
            // Update rooms list (no filtering regardless of subject)
            if (!IS_ROOM_LOCKED) {
                $.get('{{ route("admin.lessons.get-rooms-for-subject") }}', {
                    subject_id: subjectId
                }, function(data) {
                    var roomSelect = $('#room_id');
                    roomSelect.empty();
                    roomSelect.append('<option value="">-- Select Room --</option>');
                    
                    $.each(data.rooms, function(id, name) {
                        roomSelect.append('<option value="' + id + '">' + name + '</option>');
                    });
                }).fail(function() {
                    $('#room_id').html('<option value="">Error loading rooms</option>');
                });
            }
            
            // Fetch subject data for lesson type auto-select/disable
            // Use the subjects lookup object created at page load
            if (window.subjectsLookup && window.subjectsLookup[subjectId]) {
                subjectData = window.subjectsLookup[subjectId];
                console.log('Found subject in lookup:', subjectData);
                updateLessonTypeField(); // This triggers change event which calls updateLessonTypeHelp()
            } else {
                // Fallback: Fetch from server if not found in page data
                console.log('Subject not in lookup, fetching from server...');
                $.get('{{ route("admin.lessons.hours-tracking") }}', {
                    subject_id: subjectId,
                    class_id: $('#class_id').val() || 0 // Use 0 if no class selected yet
                }, function(response) {
                    if (response && response.success) {
                        subjectData = response;
                        console.log('Fetched subject data from server:', subjectData);
                        updateLessonTypeField(); // This triggers change event which calls updateLessonTypeHelp()
                    }
                }).fail(function() {
                    console.log('Could not fetch subject data');
                });
            }
        } else {
            // Reset to all options if no subject selected
            var teacherSelect = $('#teacher_id');
            teacherSelect.empty();
            teacherSelect.prop('disabled', false);
            $('#teacher-warning').remove(); // Remove any warning message
            teacherSelect.append('<option value="">Select Teacher</option>');
            @foreach($teachers as $id => $teacher)
                @if($id)
                    teacherSelect.append('<option value="{{ $id }}">{{ $teacher }}</option>');
                @endif
            @endforeach
            
            if (!IS_ROOM_LOCKED) {
                var roomSelect = $('#room_id');
                roomSelect.empty();
                roomSelect.append('<option value="">Select Room</option>');
                @foreach($rooms as $id => $room)
                    @if($id)
                        roomSelect.append('<option value="{{ $id }}">{{ $room }}</option>');
                    @endif
                @endforeach
            }
            
            // Reset lesson type field when no subject selected
            subjectData = {};
            $('#lesson_type').val('').prop('disabled', false);
            console.log('No subject selected: Reset lesson type');
        }
    });
    
    // Initialize with current subject if editing or on validation error
    // IMPORTANT: Always trigger hours tracking if both subject and class are selected
    // This ensures lesson_type field and hours tracking display correctly on page load
    $(document).ready(function() {
        const subjectId = $('#subject_id').val();
        const oldTeacherId = '{{ old("teacher_id") }}';
        
        // If subject is already selected (validation error with old input), trigger filtering
        if (subjectId) {
            console.log('Page load with subject selected, triggering teacher/room filtering...');
            $('#subject_id').trigger('change');
            
            // After AJAX completes, restore the old teacher selection
            if (oldTeacherId) {
                setTimeout(function() {
                    $('#teacher_id').val(oldTeacherId).trigger('change');
                }, 500);
            }
        }
    });

    // --- Conflict detection (live) ---
    const $form = $('form');
    const $submitBtn = $form.find('button[type="submit"]');

    // Display conflict detection messages (NOT validation errors)
    function showMessages(type, items) {
        // Remove any existing conflict message
        $('#conflict-messages').remove();
        
        if (!items || items.length === 0) {
            return;
        }

        let alertClass = 'alert-info';
        let icon = 'fa-info-circle';
        
        if (type === 'error') {
            alertClass = 'alert-danger';
            icon = 'fa-exclamation-circle';
        } else if (type === 'success') {
            alertClass = 'alert-success';
            icon = 'fa-check-circle';
        }

        let html = `
            <div id="conflict-messages" class="alert ${alertClass} alert-dismissible fade show mb-3" role="alert">
                <i class="fas ${icon}"></i>
                <strong>${type === 'error' ? 'Schedule Conflict Detected' : (type === 'success' ? 'No Conflicts' : 'Information')}</strong>
                <ul class="mb-0 mt-2 pl-3">
        `;
        
        items.forEach(function(msg) {
            html += `<li>${msg}</li>`;
        });
        
        html += `
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Insert after the main error banner (if exists) or after breadcrumb
        if ($('.alert-danger').first().length) {
            $('.alert-danger').first().after(html);
        } else {
            $('nav[aria-label="breadcrumb"]').after(html);
        }
    }

    let conflictCheckTimer = null;
    function scheduleConflictCheck() {
        clearTimeout(conflictCheckTimer);
        conflictCheckTimer = setTimeout(runConflictCheck, 300);
    }

    function runConflictCheck() {
        const payload = {
            _token: '{{ csrf_token() }}',
            weekday: $('#weekday').val(),
            start_time: $('#start_time').val(),
            end_time: $('#end_time').val(),
            class_id: $('#class_id').val(),
            teacher_id: $('#teacher_id').val(),
            room_id: $('#room_id').val()
        };

        // Only run if required fields present
        if (!payload.weekday || !payload.start_time || !payload.end_time || !payload.class_id || !payload.teacher_id || !payload.room_id) {
            $submitBtn.prop('disabled', false);
            return;
        }

        $.post('{{ route('admin.lessons.check-conflicts') }}', payload)
            .done(function(resp) {
                if (resp && resp.conflicts && resp.conflicts.length > 0) {
                    const msgs = resp.conflicts.map(c => c.message || 'Conflict detected');
                    showMessages('error', msgs);
                    $submitBtn.prop('disabled', true);
                } else {
                    showMessages('success', ['No conflicts detected for the selected time and resources.']);
                    $submitBtn.prop('disabled', false);
                }
            })
            .fail(function() {
                showMessages('info', ['Could not verify conflicts at the moment. You can still try to save.']);
                $submitBtn.prop('disabled', false);
            });
    }

    $('#weekday, #start_time, #end_time, #class_id, #teacher_id, #room_id').on('change keyup', function() {
        // Clear server-side validation errors when user changes fields
        const $field = $(this);
        $field.removeClass('is-invalid');
        $field.siblings('.invalid-feedback').hide();
        
        // Clear previous conflict messages when user makes changes
        $('#conflict-messages').remove();
        
        // Schedule conflict check
        scheduleConflictCheck();
    });

    // --- Room compatibility feedback for selected subject ---
    function checkRoomCompatibility() {
        // Compatibility check removed as all rooms are allowed regardless of subject requirements
        $submitBtn.prop('disabled', false);
    }

    $('#subject_id, #room_id').on('change', function() {
        checkRoomCompatibility();
        scheduleConflictCheck();
    });

    // Initial checks if prefilled; also lock weekday from master timetable if provided
    checkRoomCompatibility();
    scheduleConflictCheck();
    const prefillWeekday = '{{ request('weekday') }}';
    if (prefillWeekday) {
        $('#weekday').prop('disabled', true);
        $('<input>').attr({ type: 'hidden', name: 'weekday', value: prefillWeekday }).appendTo('form');
    }

    // Handle subject selection and hours tracking (class-specific)
    let subjectData = {};
    let hoursTrackingData = null;
    
    // Track if end_time was manually entered vs auto-filled
    let endTimeManuallyEntered = false;

    // Handle lesson type selection
    $('#lesson_type').on('change', function() {
        updateLessonTypeHelp();
        // Always try to suggest duration when lesson type changes
        suggestDuration();
    });

    // Handle time changes for duration calculation and auto-suggestion
    $('#start_time, #end_time').on('change', function() {
        calculateDuration();
        // Trigger duration suggestion when start_time changes
        if ($(this).attr('id') === 'start_time') {
            suggestDuration();
        }
    });
    
    // Handle datetimepicker change events (dp.change is triggered by the picker widget)
    $('#start_time').on('dp.change', function() {
        console.log('Start time changed via datetimepicker');
        suggestDuration();
        calculateDuration();
    });
    
    $('#end_time').on('dp.change', function() {
        console.log('End time changed via datetimepicker');
        calculateDuration();
    });
    
    // Track manual changes to end_time
    $('#end_time').on('input', function() {
        // Mark as manually entered if user types directly
        endTimeManuallyEntered = true;
        console.log('End time marked as manually entered');
    });

    function updateLessonTypeField() {
        const $lessonTypeField = $('#lesson_type');
        const subjectId = $('#subject_id').val();
        
        console.log('updateLessonTypeField called - subjectId:', subjectId, 'subjectData:', subjectData);
        
        // If no subject selected, reset field
        if (!subjectId || subjectId === '') {
            $lessonTypeField.val('');
            $lessonTypeField.prop('disabled', false);
            console.log('No subject selected: Reset lesson type field');
            return;
        }
        
        // If subjectData is not set or missing scheduling_mode, return early
        if (!subjectData || !subjectData.scheduling_mode) {
            console.warn('subjectData missing or no scheduling_mode:', subjectData);
            $lessonTypeField.prop('disabled', false);
            return;
        }
        
        // Lab mode: Auto-select laboratory and disable
        if (subjectData.scheduling_mode === 'lab') {
            // IMPORTANT: Enable field first, change value, then disable
            // This ensures the value change is properly reflected in the UI
            $lessonTypeField.prop('disabled', false);
            $lessonTypeField.val('laboratory');
            $lessonTypeField.prop('disabled', true);
            console.log('Lab mode: Auto-selected laboratory, field disabled');
        }
        // Lecture mode: Auto-select lecture and disable
        else if (subjectData.scheduling_mode === 'lecture') {
            // IMPORTANT: Enable field first, change value, then disable
            // This ensures the value change is properly reflected in the UI
            $lessonTypeField.prop('disabled', false);
            $lessonTypeField.val('lecture');
            $lessonTypeField.prop('disabled', true);
            console.log('Lecture mode: Auto-selected lecture, field disabled');
        }
        // Flexible mode: Enable field for user selection
        else if (subjectData.scheduling_mode === 'flexible') {
            $lessonTypeField.prop('disabled', false);
            // Don't clear value in flexible mode - allows user to keep their selection
            console.log('Flexible mode: Field enabled for user selection');
        }
        
        // Trigger change to update help text and duration suggestion
        $lessonTypeField.trigger('change');
    }

    function updateLessonTypeHelp() {
        const lessonType = $('#lesson_type').val();
        let helpText = 'Select whether this is a lecture or laboratory session';
        
        if (lessonType === 'laboratory') {
            helpText = '<strong>Laboratory:</strong> Duration between 3 to 5 hours. Default 3-hour session is advised.';
        } else if (lessonType === 'lecture') {
            helpText = '<strong>Lecture:</strong> Duration between 1 to 3 hours (30-minute intervals). Default 1-hour session is advised.';
        }
        
        if (subjectData.scheduling_mode === 'lab') {
            helpText += '<br><span class="text-info">This subject is in Pure Laboratory. Lesson type is automatically set.</span>';
        } else if (subjectData.scheduling_mode === 'lecture') {
            helpText += '<br><span class="text-info">This subject is Pure Lecture. Lesson type is automatically set.</span>';
        }
        
        $('#lesson-type-help').html(helpText);
    }

    function suggestDuration() {
        const lessonType = $('#lesson_type').val();
        const startTime = $('#start_time').val();
        const currentEndTime = $('#end_time').val();

        // ENHANCED BEHAVIOR WITH INTELLIGENT CAPPING:
        // 1. Only suggest if start_time is populated
        // 2. Recalculate if end_time was auto-filled (not manually entered)
        // 3. Don't override manually entered end_time
        // 4. Cap duration based on remaining hours
        // 5. Don't auto-fill if no hours remaining
        
        if (!startTime) {
            console.log('Duration suggestion skipped: start_time not populated');
            return;
        }
        
        if (!lessonType) {
            console.log('Duration suggestion skipped: lesson_type not selected');
            return;
        }
        
        // Check if end_time was manually entered
        if (currentEndTime && endTimeManuallyEntered) {
            console.log('Duration suggestion skipped: end_time was manually entered by user');
            return;
        }
        
        // Get remaining hours from hours tracking data
        let remainingHours = null;
        if (hoursTrackingData) {
            if (lessonType === 'lecture') {
                remainingHours = hoursTrackingData.lecture_hours.remaining;
            } else if (lessonType === 'laboratory') {
                remainingHours = hoursTrackingData.lab_hours.remaining;
            }
        }
        
        // If no hours remaining, don't auto-fill
        if (remainingHours !== null && remainingHours === 0) {
            console.log('Duration suggestion skipped: No remaining hours for this lesson type');
            $('#end_time').val('');
            updateHoursTrackingDisplay();
            return;
        }
        
        // Determine default duration
        let defaultDuration = lessonType === 'laboratory' ? 3 : 1;
        
        // Apply intelligent capping
        let suggestedDuration = defaultDuration;
        if (remainingHours !== null && remainingHours < defaultDuration) {
            // Cap to remaining hours
            suggestedDuration = remainingHours;
            console.log(`Duration capped to remaining hours: ${suggestedDuration}h (default was ${defaultDuration}h)`);
        }
        
        // Calculate end time
        const start = moment(startTime, 'h:mm A');
        const suggestedEnd = start.clone().add(suggestedDuration, 'hours');
        $('#end_time').val(suggestedEnd.format('h:mm A'));
        endTimeManuallyEntered = false; // Mark as auto-filled
        
        const typeLabel = lessonType === 'laboratory' ? 'Laboratory' : 'Lecture';
        console.log(`${typeLabel}: Auto-suggested ${suggestedDuration}-hour duration (start + ${suggestedDuration}h)`);
        
        // Update hours tracking display
        updateHoursTrackingDisplay();
    }

    function calculateDuration() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        
        if (startTime && endTime) {
            const start = moment(startTime, 'h:mm A');
            const end = moment(endTime, 'h:mm A');
            const duration = end.diff(start, 'hours', true);
            
            // Update hours tracking display with current duration
            updateHoursTrackingDisplay();
            
            // Server-side validation will handle strict enforcement
            // No client-side alerts needed
        }
    }
    
    // ===== HOURS TRACKING FUNCTIONS =====
    
    /**
     * Fetch hours tracking data from server
     */
    function fetchHoursTracking() {
        const classId = $('#class_id').val();
        const subjectId = $('#subject_id').val();
        
        if (!classId || !subjectId) {
            $('#hours-tracking-container').hide();
            hoursTrackingData = null;
            return;
        }
        
        console.log('Fetching hours tracking for class:', classId, 'subject:', subjectId);
        
        $.ajax({
            url: '{{ route("admin.lessons.hours-tracking") }}',
            method: 'GET',
            data: {
                class_id: classId,
                subject_id: subjectId
            },
            success: function(response) {
                if (response.success) {
                    hoursTrackingData = response;
                    console.log('Hours tracking data received:', hoursTrackingData);
                    updateHoursTrackingDisplay();
                    $('#hours-tracking-container').show();
                } else {
                    console.error('Hours tracking error:', response.error);
                    $('#hours-tracking-container').hide();
                    hoursTrackingData = null;
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch hours tracking:', error);
                $('#hours-tracking-container').hide();
                hoursTrackingData = null;
            }
        });
    }
    
    /**
     * Update hours tracking display with current data
     */
    function updateHoursTrackingDisplay() {
        if (!hoursTrackingData) {
            return;
        }
        
        const lectureData = hoursTrackingData.lecture_hours;
        const labData = hoursTrackingData.lab_hours;
        const schedulingMode = hoursTrackingData.scheduling_mode;
        
        // Conditionally show/hide sections based on scheduling mode
        if (schedulingMode === 'lab') {
            // Pure lab: Show only lab hours
            $('#lecture-hours-section').hide();
            $('#lab-hours-section').show();
        } else if (schedulingMode === 'lecture') {
            // Pure lecture: Show only lecture hours
            $('#lecture-hours-section').show();
            $('#lab-hours-section').hide();
        } else {
            // Flexible: Show both
            $('#lecture-hours-section').show();
            $('#lab-hours-section').show();
        }
        
        // Calculate current lesson duration
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        let currentDuration = 0;
        
        if (startTime && endTime) {
            const start = moment(startTime, 'h:mm A');
            const end = moment(endTime, 'h:mm A');
            currentDuration = end.diff(start, 'hours', true);
        }
        
        const lessonType = $('#lesson_type').val();
        
        // Update lecture hours display (if visible)
        if (schedulingMode === 'lecture' || schedulingMode === 'flexible') {
            updateProgressBar('lecture', lectureData, currentDuration, lessonType === 'lecture');
        }
        
        // Update lab hours display (if visible)
        if (schedulingMode === 'lab' || schedulingMode === 'flexible') {
            updateProgressBar('lab', labData, currentDuration, lessonType === 'laboratory');
        }
        
        // Check for errors and update submit button
        validateHoursAndUpdateSubmit(currentDuration, lessonType);
    }
    
    /**
     * Update individual progress bar
     */
    function updateProgressBar(type, data, currentDuration, isCurrentType) {
        const total = data.total;
        const scheduled = data.scheduled;
        const remaining = data.remaining;
        
        // Calculate what the new scheduled would be if this lesson is added
        const projectedScheduled = isCurrentType ? scheduled + currentDuration : scheduled;
        const projectedRemaining = Math.max(0, total - projectedScheduled);
        const progress = total > 0 ? Math.min(100, (projectedScheduled / total) * 100) : 0;
        
        // Update text displays
        $(`#${type}-hours-text`).text(`${projectedScheduled.toFixed(1)}h / ${total}h`);
        $(`#${type}-remaining-text`).text(`${projectedRemaining.toFixed(1)}h remaining`);
        $(`#${type}-progress-text`).text(`${progress.toFixed(0)}%`);
        
        // Update progress bar
        const $progressBar = $(`#${type}-progress-bar`);
        $progressBar.css('width', `${progress}%`);
        $progressBar.attr('aria-valuenow', progress);
        
        // Color coding based on remaining percentage
        $progressBar.removeClass('bg-success bg-warning bg-danger');
        const remainingPercent = total > 0 ? (projectedRemaining / total) * 100 : 0;
        
        if (remainingPercent > 50) {
            $progressBar.addClass('bg-success');
        } else if (remainingPercent >= 20) {
            $progressBar.addClass('bg-warning');
        } else {
            $progressBar.addClass('bg-danger');
        }
        
        console.log(`${type} hours - Total: ${total}, Scheduled: ${scheduled}, Current: ${currentDuration}, Projected: ${projectedScheduled}, Remaining: ${projectedRemaining}`);
    }
    
    /**
     * Validate hours and update submit button state
     */
    function validateHoursAndUpdateSubmit(currentDuration, lessonType) {
        if (!hoursTrackingData || !lessonType || currentDuration <= 0) {
            $('#hours-error-message').hide();
            $('#hours-info-message').hide();
            return;
        }
        
        const data = lessonType === 'lecture' ? hoursTrackingData.lecture_hours : hoursTrackingData.lab_hours;
        const remaining = data.remaining;
        const total = data.total;
        const scheduled = data.scheduled;
        
        // Check if duration exceeds remaining hours
        if (currentDuration > remaining) {
            const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
            const errorMsg = `This lesson (${currentDuration.toFixed(1)}h) exceeds remaining ${typeLabel.toLowerCase()} hours (${remaining.toFixed(1)}h). Scheduled: ${scheduled.toFixed(1)}h / Total: ${total}h`;
            
            $('#hours-error-text').text(errorMsg);
            $('#hours-error-message').show();
            $('#hours-info-message').hide();
            
            // Disable submit button
            $submitBtn.prop('disabled', true);
            console.log('Submit disabled: Duration exceeds remaining hours');
        } else if (remaining === 0) {
            const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
            const errorMsg = `No remaining ${typeLabel.toLowerCase()} hours for this class. All ${total}h have been scheduled.`;
            
            $('#hours-error-text').text(errorMsg);
            $('#hours-error-message').show();
            $('#hours-info-message').hide();
            
            // Disable submit button
            $submitBtn.prop('disabled', true);
            console.log('Submit disabled: No remaining hours');
        } else {
            // Valid duration
            $('#hours-error-message').hide();
            
            // Show info message about hours usage
            const typeLabel = lessonType === 'lecture' ? 'Lecture' : 'Laboratory';
            const newRemaining = remaining - currentDuration;
            const infoMsg = `This lesson will use ${currentDuration.toFixed(1)}h (${newRemaining.toFixed(1)}h ${typeLabel.toLowerCase()} hours remaining after)`;
            
            $('#hours-info-text').text(infoMsg);
            $('#hours-info-message').show();
            
            // Re-enable submit button (if no other errors)
            $submitBtn.prop('disabled', false);
            console.log('Submit enabled: Valid duration within remaining hours');
        }
    }
    
    // Fetch hours tracking when class or subject changes
    $('#class_id, #subject_id').on('change', function() {
        fetchHoursTracking();
    });
    
    // Update display when end_time changes
    $('#end_time').on('change dp.change', function() {
        updateHoursTrackingDisplay();
    });
    
    // Initial fetch if both class and subject are selected
    if ($('#class_id').val() && $('#subject_id').val()) {
        fetchHoursTracking();
    }
    
    // Enable lesson_type field before form submission so value is sent
    $('form').on('submit', function() {
        $('#lesson_type').prop('disabled', false);
    });
});
</script>
@endsection