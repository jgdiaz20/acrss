@extends('layouts.admin')
@section('styles')
    <link href="{{ asset('css/lesson-timepicker.css') }}" rel="stylesheet">
    <style>
/* CRITICAL: Room Timetable CSS - Must load in HEAD to prevent flash */
/* Timetable Header Styling */
.timetable-header {
    background: white;
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timetable-title {
    color: #28a745;
    font-weight: bold;
    font-size: 24px;
    margin: 0 0 10px 0;
}

.timetable-info {
    color: #495057;
    font-size: 14px;
    margin: 0;
    font-weight: 500;
}

/* Timetable Grid Layout */
.timetable-wrapper {
    overflow-x: auto;
    overflow-y: hidden;
}

.timetable-grid {
    display: grid;
    grid-template-columns: 120px repeat(7, 150px);
    grid-auto-flow: row;
    min-width: 1170px;
    border: 1px solid #dee2e6;
}

/* Time Column Header */
.timetable-time-header {
    background-color: #f8f9fa;
    border-right: 2px solid #dee2e6;
    border-bottom: 2px solid #dee2e6;
    position: sticky;
    left: 0;
    z-index: 20;
}

.time-header-cell {
    font-weight: bold;
    padding: 8px;
    text-align: center;
}

.time-header-sub {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    font-size: 10px;
    color: #6c757d;
    text-align: center;
    margin-top: 5px;
}

.time-header-sub .sub-label {
    padding-top: 5px;
    display: block;
}

/* Day Headers */
.timetable-day-header {
    background-color: #e9ecef;
    border-bottom: 2px solid #dee2e6;
    border-right: 1px solid #dee2e6;
    text-align: center;
    padding: 10px;
    position: sticky;
    top: 0;
    z-index: 5;
}

.timetable-day-header.weekend {
    background-color: #e9ecef;
}

/* Time Cells */
.timetable-time-cell {
    background-color: #f8f9fa;
    border-right: 2px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    padding: 8px;
    text-align: center;
    position: sticky;
    left: 0;
    z-index: 1;
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.time-cell-content {
    font-size: 11px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    align-items: center;
    width: 100%;
    text-align: center;
}

/* Timetable Cells */
.timetable-cell {
    border-right: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    min-height: 60px;
    padding: 4px;
    position: relative;
}

/* Only apply transition to available slots, not lesson cells */
.timetable-cell.available-for-scheduling {
    transition: background-color 0.3s ease;
}

.timetable-cell.weekend {
    background-color: #fffef5;
}

/* Zebra striping */
.zebra-row {
    background-color: #fcfcfd;
}

.timetable-cell.weekend.zebra-row {
    background-color: #fffdf0;
}

/* Lesson Cell Styling - Multi-hour merging */
/* All lesson cells must have consistent styling applied immediately */
#roomTimetableGrid .timetable-cell.lesson-start,
#roomTimetableGrid .timetable-cell.lesson-middle,
#roomTimetableGrid .timetable-cell.lesson-end {
    background: #ffffff !important;
    border: none !important;
    border-left: 1px solid #dee2e6 !important;
    border-right: 1px solid #dee2e6 !important;
    padding: 0 !important;
    min-height: 60px !important;
}

/* Specific borders for start cell */
#roomTimetableGrid .timetable-cell.lesson-start {
    border-top: 1px solid #dee2e6 !important;
}

/* Specific borders for end cell */
#roomTimetableGrid .timetable-cell.lesson-end {
    border-bottom: 1px solid #dee2e6 !important;
}

/* Lesson Content Styling - Override ALL global styles */
/* CRITICAL: Must override custom.css global styles to prevent card appearance and red borders */
#roomTimetableGrid .lesson-slot,
#roomTimetableGrid .class-box,
#roomTimetableGrid .class-box.has-conflicts,
.admin-room-timetable .class-box,
.room-timetable .class-box {
    background: none !important;
    color: #28a745 !important;
    border: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    padding: 8px !important;
    margin: 0 !important;
    width: 100% !important;
    height: 100% !important;
    min-height: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: flex-start !important;
    font-size: 12px !important;
    line-height: 1.4 !important;
    transform: none !important;
    transition: none !important;
}

/* Disable hover effects from global styles */
#roomTimetableGrid .class-box:hover,
.admin-room-timetable .class-box:hover,
.room-timetable .class-box:hover {
    background: none !important;
    box-shadow: none !important;
    transform: none !important;
    border: none !important;
}

/* All text inside lesson boxes should be green */
#roomTimetableGrid .class-box .class-subject,
#roomTimetableGrid .class-box .class-time,
#roomTimetableGrid .class-box .class-teacher,
#roomTimetableGrid .class-box .class-class {
    color: #28a745 !important;
}

#roomTimetableGrid .class-box i {
    color: #28a745 !important;
}

/* Continuation cells - white background to blend */
#roomTimetableGrid .lesson-continued-slot {
    background: #ffffff !important;
    border: none !important;
    margin: 0 !important;
    padding: 0 !important;
    height: 100%;
}

/* Empty Slot Styling */
.empty-slot-content {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.6;
    pointer-events: none;
}

.empty-slot-text {
    text-align: center;
    color: #6c757d;
}

/* Available slots - light green by default, not hoverable */
.timetable-cell.available-for-scheduling {
    background: #e8f5e9 !important;
    cursor: default;
}

/* Edit Mode Styling - Brighter green for available slots */
.timetable-cell.available-for-scheduling.edit-mode-active {
    background: #a5d6a7 !important;
    cursor: pointer;
}

.timetable-cell.available-for-scheduling.edit-mode-active:hover {
    background: #81c784 !important;
}

.timetable-cell.available-for-scheduling.edit-mode-active .empty-slot-content {
    opacity: 1;
}

/* Editable lessons in edit mode */
.editable-lesson.edit-mode-active {
    cursor: pointer;
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.editable-lesson.edit-mode-active:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-1px);
}

/* Lesson Actions Container - Enhanced with smooth transitions */
.lesson-actions {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 10;
    display: flex !important;
    align-items: center;
    gap: 6px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-5px) scale(0.95);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    padding: 4px 6px;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Show actions on lesson hover with smooth animation */
.editable-lesson.edit-mode-active:hover .lesson-actions {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

/* Enhanced Button Styling */
.lesson-actions .btn {
    padding: 4px 8px;
    font-size: 11px;
    border-radius: 4px;
    border-width: 1.5px;
    font-weight: 500;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}

/* Edit Button - Primary Blue */
.lesson-actions .btn-outline-primary {
    color: #007bff;
    border-color: #007bff;
    background: white;
}

.lesson-actions .btn-outline-primary:hover {
    color: white;
    background: #007bff;
    border-color: #0056b3;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
    transform: translateY(-2px) scale(1.05);
}

.lesson-actions .btn-outline-primary:active {
    transform: translateY(0) scale(0.98);
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
}

/* Delete Button - Danger Red */
.lesson-actions .btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
    background: white;
}

.lesson-actions .btn-outline-danger:hover {
    color: white;
    background: #dc3545;
    border-color: #bd2130;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    transform: translateY(-2px) scale(1.05);
}

.lesson-actions .btn-outline-danger:active {
    transform: translateY(0) scale(0.98);
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
}

/* Button Icons */
.lesson-actions .btn i {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.lesson-actions .btn:hover i {
    transform: scale(1.1);
}

/* Staggered animation for buttons */
.lesson-actions .btn:nth-child(1) {
    animation-delay: 0.05s;
}

.lesson-actions .btn:nth-child(2) {
    animation-delay: 0.1s;
}

/* Ripple effect on click */
.lesson-actions .btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.lesson-actions .btn:active::before {
    width: 120%;
    height: 120%;
}

/* Responsive - Horizontal scroll at 768px */
@media (max-width: 768px) {
    .timetable-grid {
        grid-template-columns: 100px repeat(7, 130px);
        min-width: 1010px;
    }
    
    .class-subject,
    .class-teacher,
    .class-class {
        font-size: 10px !important;
    }
    
    .class-time {
        font-size: 9px !important;
    }
    
    /* On small screens, show action buttons persistently in edit mode (no hover needed) */
    .editable-lesson.edit-mode-active .lesson-actions {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) scale(1) !important;
        position: absolute;
        top: 4px;
        right: 4px;
        padding: 2px 4px;
        gap: 4px;
        z-index: 100 !important;
    }
    
    .lesson-actions .btn {
        padding: 2px 6px;
        font-size: 10px;
    }
}

/* Additional responsive handling for very narrow viewports (DevTools open) */
@media (max-width: 1200px) {
    /* Ensure lesson cells don't clip action buttons */
    #roomTimetableGrid .lesson-slot,
    #roomTimetableGrid .class-box {
        position: relative;
        overflow: visible !important; /* Prevent clipping */
    }
    
    /* Show buttons persistently on narrow viewports (no hover) */
    .editable-lesson.edit-mode-active .lesson-actions {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        z-index: 50;
        white-space: nowrap;
    }
}
    </style>
@endsection

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="timetable-container admin-room-timetable">
                <div class="timetable-header">
                    <h2 class="timetable-title">{{ $room->name }} Timetable</h2>
                    @if($room->description)
                        <p class="timetable-info">{{ $room->description }}</p>
                    @endif
                    <div class="print-only" style="margin-top: 10px; font-size: 12px; opacity: 0.8;">
                        <p>Printed on: {{ date('F j, Y \a\t g:i A') }}</p>
                        @if($room->capacity)
                            <p>Capacity: {{ $room->capacity }}</p>
                        @endif
                    </div>
                </div>

                @if(session('status'))
                    <div class="alert alert-success m-3" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mb-3 p-3 bg-light">
                    <a href="{{ route('admin.room-management.room-timetables.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Room List
                    </a>
                    <a href="{{ route('admin.room-management.rooms.show', $room->id) }}" class="btn btn-info">
                        <i class="fas fa-info-circle"></i> Room Details
                    </a>
                    <div class="btn-group ml-2" role="group">
                        <button type="button" class="btn btn-primary" id="editModeToggle">
                            <i class="fas fa-edit"></i> <span id="editModeText">Enable Edit Mode</span>
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="refreshTimetable" style="display: none;">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Timetable Grid -->
                <div class="timetable-wrapper">
                    <div class="timetable-grid" id="roomTimetableGrid">
                        <!-- Time Column Header -->
                        <div class="timetable-time-header">
                            <div class="time-header-cell">
                                <div>Time</div>
                                <div class="time-header-sub">
                                    <span class="sub-label">From</span>
                                    <span class="sub-label">To</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Day Headers -->
                        @foreach($weekDays as $dayNumber => $dayName)
                            <div class="timetable-day-header {{ ($dayNumber == 6 || $dayNumber == 7) ? 'weekend' : '' }}">
                                <strong>{{ $dayName }}</strong>
                            </div>
                        @endforeach

                        <!-- Timetable Rows -->
                        @foreach($timetableData['timetable_matrix'] as $rowIndex => $row)
                            <!-- Time Slot Cell -->
                            <div class="timetable-time-cell {{ $rowIndex % 2 === 0 ? 'zebra-row' : '' }}" data-time-slot="{{ $row['time_slot']['start'] }}">
                                <div class="time-cell-content">
                                    <span class="time-from">{{ $row['time_slot']['start_formatted'] }}</span>
                                    <span class="time-to">{{ $row['time_slot']['end_formatted'] }}</span>
                                </div>
                            </div>

                            <!-- Day Cells -->
                            @foreach($row['days'] as $dayIndex => $dayData)
                                @php
                                    $dayNumber = array_keys($weekDays)[$dayIndex];
                                    $isLesson = $dayData['type'] === 'lesson';
                                    $lesson = $isLesson ? $dayData['lesson'] : null;
                                    $isStartSlot = $isLesson && (\Carbon\Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'))->format('H:i') === $row['time_slot']['start']);
                                    $isEndSlot = $isLesson && (\Carbon\Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('end_time'))->format('H:i') === $row['time_slot']['end']);
                                    $isMiddleSlot = $isLesson && !$isStartSlot && !$isEndSlot;

                                    $lessonClass = '';
                                    if ($isStartSlot) {
                                        $lessonClass = 'lesson-start';
                                    } elseif ($isMiddleSlot) {
                                        $lessonClass = 'lesson-middle';
                                    } elseif ($isEndSlot) {
                                        $lessonClass = 'lesson-end';
                                    }
                                @endphp
                                <div class="timetable-cell {{ $rowIndex % 2 === 0 ? 'zebra-row' : '' }} {{ $isLesson ? 'lesson-cell ' . $lessonClass . ' ' . $dayData['css_class'] : $dayData['css_class'] }} {{ ($dayNumber == 6 || $dayNumber == 7) ? 'weekend' : '' }}" 
                                     data-room-id="{{ $room->id }}"
                                     data-weekday="{{ $dayNumber }}"
                                     data-time-start="{{ $row['time_slot']['start'] }}"
                                     data-time-end="{{ $row['time_slot']['end'] }}"
                                     data-time-start-formatted="{{ $row['time_slot']['start_formatted'] }}"
                                     data-time-end-formatted="{{ $row['time_slot']['end_formatted'] }}"
                                     @if(!$isLesson) title="Enable edit mode to schedule lesson" @endif>
                                    @if($isLesson)
                                        @if($isStartSlot)
                                            <div class="class-box lesson-slot editable-lesson {{ str_replace(['has-conflicts','long-lesson','short-lesson'], '', $dayData['css_class']) }}" 
                                                 data-lesson-id="{{ $lesson->id }}"
                                                 title="Subject: {{ $lesson->subject->name ?? 'No Subject' }} ({{ $lesson->subject->code ?? 'N/A' }}) | Type: {{ ucfirst($lesson->lesson_type) }} | Class: {{ $lesson->class->display_name ?? 'No Class' }} | Teacher: {{ $lesson->teacher->name ?? 'No Teacher' }} | Time: {{ $lesson->start_time }} - {{ $lesson->end_time }}">
                                                <div class="class-subject">
                                                    {{ $lesson->subject->code ?? 'No Code' }}
                                                    @if($lesson->lesson_type === 'laboratory')
                                                        <i class="fas fa-flask ml-1" title="Laboratory"></i>
                                                    @else
                                                        <i class="fas fa-chalkboard-teacher ml-1" title="Lecture"></i>
                                                    @endif
                                                </div>
                                                <div class="class-time">{{ $lesson->start_time }} - {{ $lesson->end_time }}</div>
                                                <div class="class-teacher">{{ $lesson->teacher->name ?? 'No Teacher' }}</div>
                                                <div class="class-class">{{ $lesson->class->display_name ?? 'No Class' }}</div>
                                                <div class="lesson-actions">
                                                    <button class="btn btn-sm btn-outline-primary edit-lesson" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger delete-lesson" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="lesson-continued-slot" aria-hidden="true"></div>
                                        @endif
                                    @else
                                        <div class="empty-slot-content">
                                            <div class="empty-slot-text">
                                                <small>Available</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Include Lesson Edit Modal -->
@include('partials.lesson-edit-modal')

@endsection
@section('scripts')
@parent
<!-- Include Timepicker JavaScript -->
<script src="{{ asset('js/room-timetable-timepicker.js') }}"></script>
<!-- Include Inline Editing JavaScript -->
<script src="{{ asset('js/inline-editing.js') }}"></script>

<script>
// Edit mode toggle functionality
let editMode = false;

document.addEventListener('DOMContentLoaded', function() {
    // Set page title
    document.title = '{{ $room->name }} Timetable';
    
    // Edit mode toggle
    $('#editModeToggle').click(function() {
        editMode = !editMode;
        
        if (editMode) {
            enableEditMode();
        } else {
            disableEditMode();
        }
    });
    
    // Refresh timetable
    $('#refreshTimetable').click(function() {
        window.location.reload();
    });
    
    // Handle empty cell clicks (only in edit mode)
    $(document).on('click', '.timetable-cell.available-for-scheduling', function(e) {
        if (!editMode) {
            return; // Do nothing if edit mode is not enabled
        }
        
        e.stopPropagation();
        
        const roomId = $(this).data('room-id');
        const weekday = $(this).data('weekday');
        const startTime = $(this).data('time-start');
        
        console.log('Empty cell clicked - Room:', roomId, 'Day:', weekday, 'Start Time:', startTime);
        
        if (typeof inlineEditing !== 'undefined') {
            // Call with prefilled start_time
            inlineEditing.showCreateModal(weekday, roomId, startTime);
        }
    });
    
    // Handle lesson clicks (view details on single click)
    $(document).on('click', '.editable-lesson', function(e) {
        // Check if clicking edit/delete buttons or their children
        if ($(e.target).closest('.lesson-actions').length > 0 || 
            $(e.target).closest('.btn').length > 0) {
            return;
        }
        
        e.stopPropagation();
        const lessonId = $(this).data('lesson-id');
        
        if (lessonId && typeof inlineEditing !== 'undefined') {
            // On smaller screens in edit mode, don't show details modal
            // (buttons are always visible, user should click edit button)
            if (editMode && window.innerWidth <= 1200) {
                console.log('Lesson click ignored in edit mode on small screen - use action buttons');
                return;
            }
            
            // Single click shows details
            clearTimeout(window.lessonClickTimeout);
            window.lessonClickTimeout = setTimeout(() => {
                inlineEditing.showLessonDetails(lessonId);
            }, 250);
        }
    });
    
    // Handle lesson double-click (edit in edit mode)
    $(document).on('dblclick', '.editable-lesson', function(e) {
        if (!editMode) {
            return;
        }
        
        clearTimeout(window.lessonClickTimeout);
        e.stopPropagation();
        
        const lessonId = $(this).data('lesson-id');
        
        if (lessonId && typeof inlineEditing !== 'undefined') {
            inlineEditing.showEditModal(lessonId);
        }
    });
});

function enableEditMode() {
    editMode = true;
    $('#editModeText').text('Disable Edit Mode');
    $('#editModeToggle').removeClass('btn-primary').addClass('btn-info');
    $('#refreshTimetable').show();
    
    // Add visual feedback to available slots
    $('.timetable-cell.available-for-scheduling').addClass('edit-mode-active');
    $('.editable-lesson').addClass('edit-mode-active');
    
    // Update tooltips
    $('.timetable-cell.available-for-scheduling').attr('title', 'Click to schedule lesson at this time');
    $('.editable-lesson').attr('title', 'Click to view details, double-click to edit');
    
    console.log('Edit mode enabled');
}

function disableEditMode() {
    editMode = false;
    $('#editModeText').text('Enable Edit Mode');
    $('#editModeToggle').removeClass('btn-info').addClass('btn-primary');
    $('#refreshTimetable').hide();
    
    // Remove visual feedback
    $('.timetable-cell.available-for-scheduling').removeClass('edit-mode-active');
    $('.editable-lesson').removeClass('edit-mode-active');
    
    // Lesson actions will be hidden automatically by CSS when edit-mode-active is removed
    
    // Update tooltips
    $('.timetable-cell.available-for-scheduling').attr('title', 'Enable edit mode to schedule lesson');
    $('.editable-lesson').attr('title', 'Enable edit mode to edit lessons');
    
    console.log('Edit mode disabled');
}

// Global function for edit button clicks
function editLesson(lessonId) {
    if (typeof inlineEditing !== 'undefined') {
        inlineEditing.editLesson(lessonId);
    } else {
        console.error('Inline editing system not initialized');
    }
}
</script>
@endsection
