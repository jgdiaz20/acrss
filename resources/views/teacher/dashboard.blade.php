@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3>Welcome, {{ auth()->user()->name }}</h3>
                    <p class="text-muted">Your teaching schedule and upcoming classes</p>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Classes This Week</h5>
                                    <h2 class="card-text">{{ $totalClasses }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Today's Classes</h5>
                                    <h2 class="card-text">{{ $todayClasses->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Upcoming Classes</h5>
                                    <h2 class="card-text">{{ $upcomingClasses->flatten()->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Today's Schedule</h5>
                                </div>
                                <div class="card-body">
                                    @if($todayClasses->count() > 0)
                                        @foreach($todayClasses as $lesson)
                                            <div class="alert alert-info">
                                                <strong>{{ $lesson->class->name }}</strong><br>
                                                <small>
                                                    Time: {{ \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($lesson->end_time)->format('g:i A') }}<br>
                                                    Room: {{ $lesson->room->display_name ?? 'N/A' }}
                                                </small>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No classes scheduled for today.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Upcoming Classes</h5>
                                </div>
                                <div class="card-body">
                                    @if($upcomingClasses->count() > 0)
                                        @foreach($upcomingClasses as $day => $classes)
                                            <h6 class="text-primary">{{ $day }}</h6>
                                            @foreach($classes as $lesson)
                                                <div class="alert alert-light">
                                                    <strong>{{ $lesson->class->name }}</strong><br>
                                                    <small>
                                                        Time: {{ \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($lesson->end_time)->format('g:i A') }}<br>
                                                        Room: {{ $lesson->room->display_name ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    @else
                                        <p class="text-muted">No upcoming classes scheduled.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Embedded Timetable -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="timetable-container">
                                <div class="timetable-header">
                                    <h2>My Weekly Schedule</h2>
                                    <p style="margin: 10px 0 0 0; opacity: 0.9;">Your complete teaching timetable</p>
                                </div>

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
                                    
                                    @php
                                        // Generate timetable data for the dashboard
                                        $teacher = auth()->user();
                                        $teacherLessons = App\Lesson::with(['class', 'room', 'subject'])
                                            ->where('teacher_id', $teacher->id)
                                            ->get();
                                        
                                        $dashboardCalendarData = [];
                                        foreach ($weekDays as $index => $day) {
                                            $dayLessons = $teacherLessons->where('weekday', $index)->sortBy(function($lesson) {
                                                return \Carbon\Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
                                            });
                                            
                                            if ($dayLessons->count() > 0) {
                                                $dashboardCalendarData[$index] = $dayLessons->map(function($lesson) {
                                                    return [
                                                        'class_name' => $lesson->class->name ?? 'Unknown Class',
                                                        'room_name' => $lesson->room->display_name ?? $lesson->room->name ?? 'No Room',
                                                        'subject_name' => $lesson->subject->name ?? 'No Subject',
                                                        'start_time' => $lesson->start_time,
                                                        'end_time' => $lesson->end_time,
                                                        'lesson_id' => $lesson->id
                                                    ];
                                                })->values();
                                            } else {
                                                $dashboardCalendarData[$index] = [];
                                            }
                                        }
                                    @endphp
                                    
                                    @foreach($weekDays as $index => $day)
                                        <div class="timetable-day-column {{ ($index == 6 || $index == 7) ? 'weekend' : '' }}">
                                            @if(isset($dashboardCalendarData[$index]) && count($dashboardCalendarData[$index]) > 0)
                                                @foreach($dashboardCalendarData[$index] as $lesson)
                                                    <div class="class-box">
                                                        <div class="class-subject">{{ $lesson['subject_name'] }}</div>
                                                        <div class="class-time">{{ $lesson['start_time'] }} - {{ $lesson['end_time'] }}</div>
                                                        <div class="class-instructor">{{ $lesson['class_name'] }}</div>
                                                        <div class="class-room">{{ $lesson['room_name'] }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="not-scheduled-box">
                                                    {{ ($index == 6 || $index == 7) ? 'Not Scheduled' : 'No Classes' }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
