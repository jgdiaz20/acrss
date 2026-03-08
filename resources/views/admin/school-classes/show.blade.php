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
        @if($schoolClass->program)
            <li class="breadcrumb-item">
                <a href="{{ route('admin.school-classes.program', $schoolClass->program->type) }}">
                    <i class="fas fa-{{ $schoolClass->program->type == 'senior_high' ? 'graduation-cap' : 'university' }}"></i>
                    {{ $schoolClass->program->type == 'senior_high' ? 'Senior High School' : 'College' }}
                </a>
            </li>
            @if($schoolClass->gradeLevel)
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.school-classes.program.grade', [$schoolClass->program->type, $schoolClass->gradeLevel->id]) }}">
                        <i class="fas fa-layer-group"></i> {{ $schoolClass->gradeLevel->level_name }}
                    </a>
                </li>
            @endif
        @endif
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-eye"></i> {{ $schoolClass->name }} Schedule
        </li>
    </ol>
</nav>

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header -->
            <div class="timetable-container school-class-timetable">
                <div class="timetable-header">
                    <div class="text-center mb-3">
                        <h2 class="timetable-title">{{ $schoolClass->name }} - Class Schedule</h2>
                        <p class="timetable-info">
                            {{ $schoolClass->program->name ?? 'N/A' }} - {{ $schoolClass->gradeLevel->level_name ?? 'N/A' }}
                            @if($schoolClass->section)
                                (Section {{ $schoolClass->section }})
                            @endif
                        </p>
                    </div>
                    <div class="text-center">
                        <a class="btn btn-secondary" href="{{ route('admin.school-classes.index') }}">
                            <i class="fas fa-arrow-left"></i> Back to Classes
                        </a>
                        <button onclick="printTimetable()" class="btn btn-success btn-print">
                            <i class="fas fa-print"></i> Print Timetable
                        </button>
                    </div>
                </div>

                @if(session('status'))
                    <div class="alert alert-success m-3" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Timetable Grid -->
                <div class="timetable-grid">
                    <!-- Day headers -->
                    <div class="timetable-day-header"></div>
                    @foreach($weekDays as $day)
                        <div class="timetable-day-header">{{ $day }}</div>
                    @endforeach

                    <!-- Day columns with lessons -->
                    <div class="timetable-time-column">
                        <!-- This column will be empty in the new design -->
                    </div>
                    
                    @foreach($weekDays as $index => $day)
                        <div class="timetable-day-column {{ ($index == 6 || $index == 7) ? 'weekend' : '' }}" data-day="{{ $index }}">
                            @if(isset($calendarData[$index]) && count($calendarData[$index]) > 0)
                                @foreach($calendarData[$index] as $lesson)
                                    <div class="class-box school-class-lesson-box" 
                                         title="Lesson: {{ $lesson['subject_code'] }} with {{ $lesson['teacher_name'] }}">
                                        <div class="class-subject">{{ $lesson['subject_code'] }}</div>
                                        <div class="class-time">{{ $lesson['start_time'] }} - {{ $lesson['end_time'] }}</div>
                                        <div class="class-instructor">{{ $lesson['teacher_name'] }}</div>
                                        <div class="class-room">{{ $lesson['room_name'] }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="not-scheduled-box">
                                    Available
                                </div>
                            @endif                  
                        </div>
                    @endforeach
                </div>

                <!-- Class Statistics -->
                <div class="row mt-4 justify-content-center">
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card statistics-card">
                            <div class="card-body text-center">
                                <h5 class="statistics-title">Total Lessons</h5>
                                <h3 class="statistics-number">{{ $lessons->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card statistics-card">
                            <div class="card-body text-center">
                                <h5 class="statistics-title">Subjects</h5>
                                <h3 class="statistics-number">{{ $lessons->pluck('subject_id')->unique()->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card statistics-card">
                            <div class="card-body text-center">
                                <h5 class="statistics-title">Teachers</h5>
                                <h3 class="statistics-number">{{ $lessons->pluck('teacher_id')->unique()->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card statistics-card">
                            <div class="card-body text-center">
                                <h5 class="statistics-title">Rooms Used</h5>
                                <h3 class="statistics-number">{{ $lessons->pluck('room_id')->unique()->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lesson Details Modal -->
<div class="modal fade" id="lessonDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lesson Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="lessonDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editLessonBtn">Edit Lesson</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
$(document).ready(function() {
    // Section timetable is read-only - no editing functionality
});

function showLessonDetails(lessonId) {
    // Load lesson details via AJAX
    $.get('{{ route("admin.lessons.show", "") }}/' + lessonId)
        .done(function(data) {
            $('#lessonDetailsContent').html(data);
            $('#lessonDetailsModal').modal('show');
        })
        .fail(function() {
            alert('Failed to load lesson details');
        });
}
</script>


<script>
function printTimetable() {
    // Add printing class to body
    document.body.classList.add('printing');
    
    // Print the page
    window.print();
    
    // Remove printing class after print dialog closes
    setTimeout(function() {
        document.body.classList.remove('printing');
    }, 1000);
}

// Handle print events
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});
</script>
@endsection