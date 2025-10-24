@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="timetable-container">
                <div class="timetable-header">
                    <h2>My Schedule - {{ auth()->user()->name }}</h2>
                    <p style="margin: 10px 0 0 0; opacity: 0.9;">View your assigned classes and schedule</p>
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

                <div class="p-4 bg-light">
                    <h5>Schedule Summary</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Classes</h6>
                                    <h4>
                                        @php
                                            $totalClasses = 0;
                                            foreach($calendarData as $dayLessons) {
                                                $totalClasses += count($dayLessons);
                                            }
                                        @endphp
                                        {{ $totalClasses }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Classes</h6>
                                    <h4>{{ collect($calendarData)->flatten(1)->pluck('class_name')->unique()->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Subjects</h6>
                                    <h4>{{ collect($calendarData)->flatten(1)->pluck('subject_name')->unique()->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Rooms</h6>
                                    <h4>{{ collect($calendarData)->flatten(1)->pluck('room_name')->unique()->count() }}</h4>
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
