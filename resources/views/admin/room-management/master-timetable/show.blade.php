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
            <a href="{{ route('admin.room-management.rooms.index') }}">
                <i class="fas fa-building"></i> Room Management
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.room-management.master-timetable.index') }}">
                <i class="fas fa-th"></i> Master Timetable
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-calendar-day"></i> {{ $timetableData['weekday_name'] }}
        </li>
    </ol>
</nav>

<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-th mr-2"></i>
                Master Timetable - {{ $timetableData['weekday_name'] }}
            </h3>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-light btn-sm" id="refreshTimetable">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <div class="dropdown-menu">
                        <h6 class="dropdown-header">Export Current Day Schedules</h6>
                        <a class="dropdown-item" href="#" onclick="exportTimetable('json')">
                            <i class="fas fa-file-code mr-2"></i>JSON - Class Schedules
                        </a>
                        <a class="dropdown-item" href="#" onclick="exportTimetable('csv')">
                            <i class="fas fa-file-csv mr-2"></i>CSV - Class Schedules
                        </a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Export Complete Week Schedules</h6>
                        <a class="dropdown-item" href="#" onclick="exportAllDays('json')">
                            <i class="fas fa-calendar-week mr-2"></i>Complete JSON - All Schedules
                        </a>
                        <a class="dropdown-item" href="#" onclick="exportAllDays('csv')">
                            <i class="fas fa-calendar-week mr-2"></i>Complete CSV - All Schedules
                        </a>
                    </div>
                </div>
                <button type="button" class="btn btn-light btn-sm" onclick="printTimetable()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <!-- Day Navigation Tabs -->
        <div class="border-bottom">
            <ul class="nav nav-tabs" id="dayNavigationTabs" role="tablist">
                @foreach($weekdayOptions as $weekday => $dayName)
                    <li class="nav-item">
                        <a class="nav-link {{ $weekday == $timetableData['weekday'] ? 'active' : '' }}" 
                           href="{{ route('admin.room-management.master-timetable.show', $weekday) }}">
                            <i class="fas fa-calendar-day mr-1"></i>
                            {{ $dayName }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        

        <!-- Timetable Statistics -->
        <div class="p-3 bg-light border-bottom">
            <div class="row">
                <div class="col-md-2">
                    <div class="text-center">
                        <h5 class="mb-0 text-primary">{{ $timetableData['statistics']['total_rooms'] }}</h5>
                        <small class="text-muted">Total Rooms</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h5 class="mb-0 text-success">{{ $timetableData['statistics']['occupied_slots'] }}</h5>
                        <small class="text-muted">Occupied Slots</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h5 class="mb-0 text-warning">{{ $timetableData['statistics']['empty_slots'] }}</h5>
                        <small class="text-muted">Available Slots</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h5 class="mb-0 text-info">{{ $timetableData['statistics']['utilization_percentage'] }}%</h5>
                        <small class="text-muted">Utilization</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h5 class="mb-0 text-secondary">{{ $timetableData['statistics']['rooms_with_lessons'] }}</h5>
                        <small class="text-muted">Active Rooms</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h5 class="mb-0 text-dark">{{ $timetableData['statistics']['rooms_without_lessons'] }}</h5>
                        <small class="text-muted">Empty Rooms</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Timetable Grid -->
        <div class="timetable-container">
            <div class="timetable-grid" id="masterTimetableGrid">
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
                
                <!-- Room Headers -->
                @foreach($timetableData['rooms'] as $room)
                    <div class="timetable-room-header" data-room-id="{{ $room->id }}">
                        <div class="room-header-cell">
                            <strong>{{ $room->name }}</strong>
                            @if($room->is_lab)
                                <small class="badge badge-info ml-1">Lab</small>
                            @endif
                            <br>
                            <small class="text-muted">{{ $room->capacity ?? 'N/A' }} seats</small>
                        </div>
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

                    <!-- Room Cells -->
                    @foreach($row['rooms'] as $roomIndex => $roomData)
                        @php
                            $isLesson = $roomData['type'] === 'lesson';
                            $lesson = $isLesson ? $roomData['lesson'] : null;
                            $isStartSlot = $isLesson && (\Carbon\Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'))->format('H:i') === $row['time_slot']['start']);
                            $isWithinLesson = $isLesson && (\Carbon\Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'))->format('H:i') !== $row['time_slot']['end']);
                        @endphp
                        <div class="timetable-cell {{ $rowIndex % 2 === 0 ? 'zebra-row' : '' }} {{ $isLesson ? $roomData['css_class'] : $roomData['css_class'] }}" 
                             data-room-id="{{ $timetableData['rooms'][$roomIndex]->id }}"
                             data-time-slot="{{ $row['time_slot']['start'] }}"
                             data-time-end="{{ $row['time_slot']['end'] }}">
                            @if($isLesson)
                                @if($isStartSlot)
                                    <div class="class-box lesson-slot {{ str_replace(['has-conflicts','long-lesson','short-lesson'], '', $roomData['css_class']) }}" 
                                         data-lesson-id="{{ $lesson->id }}"
                                         title="Subject: {{ $lesson->subject->name ?? 'No Subject' }} | Class: {{ $lesson->class->display_name ?? 'No Class' }} | Teacher: {{ $lesson->teacher->name ?? 'No Teacher' }} | Time: {{ $lesson->start_time }} - {{ $lesson->end_time }}">
                                        <div class="class-subject">
                                            {{ $lesson->subject->name ?? 'No Subject' }}
                                            @if($lesson->subject && $lesson->subject->requires_lab)
                                                <i class="fas fa-flask ml-1" title="Lab Required"></i>
                                            @endif
                                        </div>
                                        <div class="class-time">{{ $lesson->start_time }} - {{ $lesson->end_time }}</div>
                                        <div class="class-teacher">{{ $lesson->teacher->name ?? 'No Teacher' }}</div>
                                        <div class="class-class">{{ $lesson->class->display_name ?? 'No Class' }}</div>
                                    </div>
                                @else
                                    <div class="lesson-continued-slot" aria-hidden="true"></div>
                                @endif
                            @else
                                <div class="empty-slot-content" 
                                     title="Available time slot"
                                     data-room-id="{{ $timetableData['rooms'][$roomIndex]->id }}"
                                     data-room-name="{{ $timetableData['rooms'][$roomIndex]->name }}"
                                     data-time-start="{{ $row['time_slot']['start_formatted'] }}"
                                     data-time-end="{{ $row['time_slot']['end_formatted'] }}"
                                     data-weekday="{{ $timetableData['weekday'] }}"
                                     data-weekday-name="{{ $timetableData['weekday_name'] }}">
                                    <div class="empty-slot-text">
                                        <i class="fas fa-clock"></i>
                                        <br>
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

<!-- Lesson Details Modal -->
<div class="modal fade" id="lessonDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lesson Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="lessonDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editLessonBtn">Edit Lesson</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Schedule Modal -->
<div class="modal fade" id="quickScheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Schedule</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="quickScheduleContent">
                    <!-- Content will be populated -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="scheduleLessonBtn">Schedule Lesson</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .timetable-container {
        overflow-x: auto;
        overflow-y: hidden;
    }

    .timetable-grid {
        display: grid;
        grid-template-columns: 120px repeat({{ count($timetableData['rooms']) }}, 200px);
        min-width: {{ 120 + (count($timetableData['rooms']) * 200) }}px;
        border: 1px solid #dee2e6;
    }

    .timetable-time-header {
        background-color: #f8f9fa;
        border-right: 2px solid #dee2e6;
        position: sticky;
        left: 0;
        z-index: 20; /* Ensure it sits above hovered cells */
    }

    .timetable-room-header {
        background-color: #e9ecef;
        border-bottom: 2px solid #dee2e6;
        text-align: center;
        padding: 10px;
        position: sticky;
        top: 0;
        z-index: 5;
    }

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

    .timetable-cell {
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        min-height: 60px;
        padding: 4px;
        cursor: pointer;
        transition: background-color 0.15s ease, box-shadow 0.15s ease; /* Avoid scale transform overlap */
        position: relative;
    }

    .timetable-cell:hover {
        background-color: #f8f9fa;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    /* Lesson slot styles are now handled by custom.css */

    .lesson-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 2px;
    }

    .lesson-subject {
        font-weight: bold;
        font-size: 12px;
        color: #1976d2;
        margin-bottom: 2px;
    }

    .lesson-class {
        font-size: 11px;
        color: #424242;
        margin-bottom: 1px;
    }

    .lesson-teacher {
        font-size: 10px;
        color: #666;
        margin-bottom: 1px;
    }

    .lesson-time {
        font-size: 9px;
        color: #888;
    }

    .lesson-conflict {
        position: absolute;
        top: 2px;
        right: 2px;
    }

    .empty-slot {
        background-color: #ffffff;
        border: 1px dashed #dee2e6;
    }

    .empty-slot.available-for-scheduling:hover {
        background-color: #e8f5e8;
        border-color: #28a745;
    }

    .empty-slot-content {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.6;
        pointer-events: auto;
    }

    .empty-slot-content:hover {
        opacity: 1;
    }

    .empty-slot-text {
        text-align: center;
        color: #6c757d;
    }

    .time-header-cell,
    .room-header-cell {
        font-weight: bold;
        padding: 8px;
    }

    .time-header-sub {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        font-size: 10px;
        color: #6c757d;
        text-align: center;
    }

    .time-header-sub .sub-label {
        padding-top:25px;
        display: block;
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

    /* Zebra striping across the grid: applied to time cells and corresponding row cells */
    .zebra-row {
        background-color: #fcfcfd;
    }

    .lesson-continued-slot {
        height: 100%;
        background: transparent;
    }

    /* Master Timetable unified style: white background, green text for all lessons */
    #masterTimetableGrid .lesson-slot,
    #masterTimetableGrid .class-box {
        background: #ffffff !important;
        color: #28a745 !important;
        border: none !important;
        box-shadow: none !important;
    }

    /* Ensure all inner texts are green */
    #masterTimetableGrid .class-box .class-subject,
    #masterTimetableGrid .class-box .class-time,
    #masterTimetableGrid .class-box .class-teacher,
    #masterTimetableGrid .class-box .class-class {
        color: #28a745 !important;
    }

    /* Icons inherit green; keep lab icon only for distinction */
    #masterTimetableGrid .class-box i { color: #28a745 !important; }

    /* Ensure all inner texts are green */
    #masterTimetableGrid .class-box .class-subject,
    #masterTimetableGrid .class-box .class-time,
    #masterTimetableGrid .class-box .class-teacher,
    #masterTimetableGrid .class-box .class-class {
        color: #28a745 !important;
    }

    /* Icons inherit green; keep lab icon only for distinction */
    #masterTimetableGrid .class-box i { color: #28a745 !important; }

    /* Neutralize variant classes within Master Timetable (no conflict/length variants) */
    #masterTimetableGrid .has-conflicts,
    #masterTimetableGrid .long-lesson,
    #masterTimetableGrid .short-lesson {
        background: #ffffff !important;
        color: #28a745 !important;
        border: none !important;
        box-shadow: none !important;
    }

    /* Enforce consistent class-box layout in Master Timetable */
    #masterTimetableGrid .class-box {
        border-radius: 0 !important;
        padding: 8px !important;
        margin: 0 !important;
        min-height: 80px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        font-size: 12px !important;
        line-height: 1.3 !important;
        background-clip: padding-box !important;
    }

    /* Improve visual merging for continuation cells */
    #masterTimetableGrid .lesson-continued-slot {
        background: #ffffff !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .timetable-grid {
            grid-template-columns: 100px repeat({{ count($timetableData['rooms']) }}, 150px);
            min-width: {{ 100 + (count($timetableData['rooms']) * 150) }}px;
        }
        
        .lesson-subject {
            font-size: 10px;
        }
        
        .lesson-class,
        .lesson-teacher {
            font-size: 9px;
        }
        
        .lesson-time {
            font-size: 8px;
        }
    }

    /* Print styles */
    @media print {
        .card-header,
        .breadcrumb,
        .btn,
        .modal {
            display: none !important;
        }
        
        .timetable-grid {
            border: 2px solid #000;
        }
        
        .lesson-slot {
            background-color: #f0f0f0 !important;
            border: 1px solid #000;
        }
        
        .empty-slot {
            border: 1px solid #ccc;
        }
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentWeekday = {{ $timetableData['weekday'] }};
    
    // Lesson click handler
    $(document).on('click', '.lesson-content', function(e) {
        e.stopPropagation();
        const lessonId = $(this).data('lesson-id');
        showLessonDetails(lessonId);
    });
    
    // Empty slot click handler
    $(document).on('click', '.empty-slot-content', function(e) {
        e.stopPropagation();
        const roomId = $(this).data('room-id');
        const roomName = $(this).data('room-name');
        const timeStart = $(this).data('time-start');
        const timeEnd = $(this).data('time-end');
        const weekday = $(this).data('weekday');
        const weekdayName = $(this).data('weekday-name');
        showQuickSchedule({ roomId, roomName, timeStart, timeEnd, weekday, weekdayName });
    });
    
    // Refresh button handler
    $('#refreshTimetable').on('click', function() {
        location.reload();
    });
    
    // Edit lesson button handler
    $('#editLessonBtn').on('click', function() {
        const lessonId = $(this).data('lesson-id');
        if (lessonId) {
            window.location.href = `/admin/lessons/${lessonId}/edit`;
        }
    });
});

function showLessonDetails(lessonId) {
    // Use the new lessons.info endpoint
    $.get(`/admin/lessons/${lessonId}/info`)
    .done(function(lesson) {
        const content = `
            <div class="lesson-info-display">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle mr-2"></i>Lesson Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 40%;"><i class="fas fa-book mr-2"></i>Subject:</td>
                                <td><strong>${lesson.subject_name}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-users mr-2"></i>Class:</td>
                                <td><strong>${lesson.class_name}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-chalkboard-teacher mr-2"></i>Teacher:</td>
                                <td><strong>${lesson.teacher_name}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-door-open mr-2"></i>Room:</td>
                                <td><strong>${lesson.room_name}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-clock mr-2"></i>Schedule</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 40%;"><i class="fas fa-calendar-day mr-2"></i>Day:</td>
                                <td><strong>${lesson.weekday_name}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-clock mr-2"></i>Time:</td>
                                <td><strong>${lesson.start_time} - ${lesson.end_time}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        `;
        
        $('#lessonDetailsContent').html(content);
        $('#editLessonBtn').data('lesson-id', lessonId);
        $('#lessonDetailsModal').modal('show');
    })
    .fail(function(xhr) {
        let errorMsg = 'Failed to load lesson details';
        if (xhr.status === 403) {
            errorMsg = 'You do not have permission to view this lesson';
        }
        alert(errorMsg);
    });
}

function showQuickSchedule({ roomId, roomName, timeStart, timeEnd, weekday, weekdayName }) {
    const content = `
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Click "Schedule Lesson" to create a new lesson for this time slot.
        </div>
        <div class="form-group">
            <label>Room:</label>
            <input type="text" class="form-control" value="${roomName} (ID: ${roomId})" readonly>
        </div>
        <div class="form-group">
            <label>Time Slot:</label>
            <input type="text" class="form-control" value="${timeStart} - ${timeEnd}" readonly>
        </div>
        <div class="form-group">
            <label>Day:</label>
            <input type="text" class="form-control" value="${weekdayName}" readonly>
        </div>
    `;
    
    $('#quickScheduleContent').html(content);
    $('#scheduleLessonBtn')
        .data('room-id', roomId)
        .data('time-start', timeStart)
        .data('time-end', timeEnd)
        .data('weekday', weekday);
    $('#quickScheduleModal').modal('show');
}

function exportTimetable(format) {
    // Show loading indicator
    const exportBtn = event.target.closest('.dropdown-item');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
    exportBtn.style.pointerEvents = 'none';
    
    const url = `{{ route('admin.room-management.master-timetable.export') }}?weekday={{ $timetableData['weekday'] }}&format=${format}`;
    
    // Debug: Log the URL being called
    console.log('Export URL:', url);
    console.log('Format:', format);
    console.log('Weekday:', {{ $timetableData['weekday'] }});
    
    // Use fetch API for better error handling
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': format === 'json' ? 'application/json' : 'text/csv'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.blob();
    })
    .then(blob => {
        // Create download link
        const downloadUrl = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;
        
        // Set filename based on format and weekday
        const weekdayName = '{{ strtolower($timetableData["weekday_name"]) }}';
        const date = new Date().toISOString().split('T')[0];
        const filename = `class_schedules_${weekdayName}_${date}.${format}`;
        link.download = filename;
        
        // Trigger download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up the URL object
        window.URL.revokeObjectURL(downloadUrl);
        
        // Reset button and show success message
        exportBtn.innerHTML = originalText;
        exportBtn.style.pointerEvents = 'auto';
        
        showExportSuccessMessage(`${format.toUpperCase()} export completed successfully!`);
    })
    .catch(error => {
        console.error('Export error:', error);
        
        // Reset button and show error message
        exportBtn.innerHTML = originalText;
        exportBtn.style.pointerEvents = 'auto';
        
        // Try fallback method with direct link
        console.log('Trying fallback method...');
        tryFallbackExport(url, exportBtn, originalText, format);
    });
}

function exportAllDays(format) {
    // Show loading indicator
    const exportBtn = event.target.closest('.dropdown-item');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting All Days...';
    exportBtn.style.pointerEvents = 'none';
    
    const url = `{{ route('admin.room-management.master-timetable.export-all') }}?format=${format}`;
    
    // Use fetch API for better error handling
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': format === 'json' ? 'application/json' : 'text/csv'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.blob();
    })
    .then(blob => {
        // Create download link
        const downloadUrl = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;
        
        // Set filename for complete export
        const date = new Date().toISOString().split('T')[0];
        const filename = `class_schedules_complete_${date}.${format}`;
        link.download = filename;
        
        // Trigger download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up the URL object
        window.URL.revokeObjectURL(downloadUrl);
        
        // Reset button and show success message
        exportBtn.innerHTML = originalText;
        exportBtn.style.pointerEvents = 'auto';
        
        showExportSuccessMessage(`Complete ${format.toUpperCase()} export completed successfully!`);
    })
    .catch(error => {
        console.error('Export error:', error);
        
        // Reset button and show error message
        exportBtn.innerHTML = originalText;
        exportBtn.style.pointerEvents = 'auto';
        
        showExportErrorMessage(`Export failed: ${error.message}`);
    });
}

function printTimetable() {
    window.print();
}

// Schedule lesson button handler
$('#scheduleLessonBtn').on('click', function() {
    const roomId = $(this).data('room-id');
    const timeStart = $(this).data('time-start');
    const timeEnd = $(this).data('time-end');
    const weekday = $(this).data('weekday');
    
    // Redirect to lesson creation with pre-filled data
    // Ensure start_time is normalized like '7:00 AM'
    const normalizedStart = moment(timeStart, ['h:mm A','H:mm']).format('h:mm A');
    const url = `/admin/lessons/create?room_id=${encodeURIComponent(roomId)}&weekday=${encodeURIComponent(weekday)}&start_time=${encodeURIComponent(normalizedStart)}`;
    window.location.href = url;
});

// Fallback export method
function tryFallbackExport(url, exportBtn, originalText, format) {
    try {
        // Create a temporary link to trigger download
        const link = document.createElement('a');
        link.href = url;
        link.style.display = 'none';
        link.target = '_blank';
        document.body.appendChild(link);
        
        // Add error handling
        link.onerror = function() {
            showExportErrorMessage(`Export failed: Unable to download file. Please check your browser settings.`);
        };
        
        // Trigger download
        link.click();
        document.body.removeChild(link);
        
        // Reset button after a short delay
        setTimeout(() => {
            exportBtn.innerHTML = originalText;
            exportBtn.style.pointerEvents = 'auto';
            showExportSuccessMessage(`${format.toUpperCase()} export completed successfully!`);
        }, 1000);
        
    } catch (error) {
        console.error('Fallback export error:', error);
        showExportErrorMessage(`Export failed: ${error.message}`);
    }
}

// Helper functions for export messages
function showExportSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px; max-width: 500px;';
    alertDiv.innerHTML = `
        <i class="fas fa-check-circle mr-2"></i>
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto-remove alert after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function showExportErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px; max-width: 500px;';
    alertDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle mr-2"></i>
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto-remove alert after 7 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 7000);
}
</script>
@endsection
