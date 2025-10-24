@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="timetable-container">
                <div class="timetable-header">
                    <h2>My Class Schedule - {{ auth()->user()->name }}</h2>
                    <p style="margin: 10px 0 0 0; opacity: 0.9;">View your class timetable</p>
                </div>

                @if(session('status'))
                    <div class="alert alert-success m-3" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

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
                        <div class="timetable-day-column {{ ($index == 6 || $index == 7) ? 'weekend' : '' }}">
                            @if(isset($calendarData[$index]) && count($calendarData[$index]) > 0)
                                @foreach($calendarData[$index] as $lesson)
                                    <div class="class-box">
                                        <div class="class-subject">{{ $lesson['subject'] ?? 'Class' }}</div>
                                        <div class="class-time">{{ $lesson['start_time'] }} - {{ $lesson['end_time'] }}</div>
                                        <div class="class-room">Room {{ $lesson['room_name'] }}</div>
                                        <div class="class-teacher">Teacher: {{ $lesson['teacher_name'] }}</div>
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

                <div class="p-4 bg-light">
                    <h5>Schedule Summary</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-white border">
                                <div class="card-body">
                                    <h6 class="card-title">Total Classes This Week</h6>
                                    <p class="card-text">
                                        @php
                                            $totalClasses = 0;
                                            foreach($calendarData as $dayLessons) {
                                                $totalClasses += count($dayLessons);
                                            }
                                        @endphp
                                        <strong>{{ $totalClasses }}</strong> classes scheduled
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-white border">
                                <div class="card-body">
                                    <h6 class="card-title">Schedule Status</h6>
                                    <p class="card-text">
                                        @if($totalClasses > 0)
                                            <span class="text-success">✓ Schedule Active</span>
                                        @else
                                            <span class="text-warning">⚠ No classes scheduled</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
