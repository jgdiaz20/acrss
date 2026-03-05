@extends('layouts.admin')
@section('content')

<!-- Welcome Section -->
<div class="dashboard-welcome">
    <div class="welcome-content">
        <h1 class="welcome-title">
            <i class="fas fa-user-graduate"></i>
            Welcome, {{ auth()->user()->name }}
        </h1>
        <p class="welcome-subtitle">Your weekly teaching schedule made easy.                                                                                            </p>
    </div>
</div>

<!-- Statistics Overview -->
<div class="row mb-3 justify-content-center">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card-improved stat-card-primary">
            <div class="stat-content-improved">
                <div class="stat-number-improved">{{ $totalClasses }}</div>
                <div class="stat-label-improved">Total Classes This Week</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card-improved stat-card-success">
            <div class="stat-content-improved">
                <div class="stat-number-improved">{{ $todayClasses->count() }}</div>
                <div class="stat-label-improved">Today's Classes</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card-improved stat-card-info">
            <div class="stat-content-improved">
                <div class="stat-number-improved">{{ $tomorrowClasses->count() }}</div>
                <div class="stat-label-improved">Tomorrow's Classes</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="row">
    <!-- Today's Schedule -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="header-content">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i>
                        Today's Schedule
                    </h3>
                    <p class="card-subtitle">{{ now()->format('l, F j, Y') }}</p>
                </div>
            </div>
            <div class="card-body">
                @if($todayClasses->count() > 0)
                    <div class="schedule-list">
                        @foreach($todayClasses as $lesson)
                            <div class="schedule-item">
                                <div class="schedule-time">
                                    <i class="fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') }}
                                </div>
                                <div class="schedule-details">
                                    <h5 class="schedule-class">{{ $lesson->class->name }}</h5>
                                    <p class="schedule-info">
                                        <span><i class="fas fa-book"></i> {{ $lesson->subject->code ?? 'N/A' }}</span>
                                        <span><i class="fas fa-door-open"></i> {{ $lesson->room->display_name ?? 'N/A' }}</span>
                                    </p>
                                    <p class="schedule-duration">
                                        {{ \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($lesson->end_time)->format('g:i A') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No classes scheduled for today</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Tomorrow's Classes -->
     <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="header-content">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day"></i>
                        Tomorrow's Classes
                    </h3>
                    <p class="card-subtitle">{{ now()->addDay()->format('l, F j, Y') }}</p>
                </div>
            </div>
            <div class="card-body">
                @if($tomorrowClasses->count() > 0)
                    <div class="schedule-list">
                        @foreach($tomorrowClasses as $lesson)
                            <div class="schedule-item">
                                <div class="schedule-time">
                                    <i class="fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') }}
                                </div>
                                <div class="schedule-details">
                                    <h5 class="schedule-class">{{ $lesson->class->name }}</h5>
                                    <p class="schedule-info">
                                        <span><i class="fas fa-book"></i> {{ $lesson->subject->code ?? 'N/A' }}</span>
                                        <span><i class="fas fa-door-open"></i> {{ $lesson->room->display_name ?? 'N/A' }}</span>
                                    </p>
                                    <p class="schedule-duration">
                                        {{ \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($lesson->end_time)->format('g:i A') }}
                                    </p>                
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No classes scheduled for tomorrow</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


@endsection

@section('styles')
<style>
/* Teacher Dashboard Specific Styles */

/* Improved Statistics Cards - No Icons */
.stat-card-improved {
    background: white;
    border-radius: 12px;
    padding: 1.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 4px solid;
    height: 100%;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.stat-card-improved::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 60px;
    height: 60px;
    opacity: 0.1;
    border-radius: 50%;
    transform: translate(20px, -20px);
}

.stat-card-improved:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card-improved.stat-card-primary {
    border-left-color: #007bff;
}

.stat-card-improved.stat-card-primary::before {
    background: #007bff;
}

.stat-card-improved.stat-card-success {
    border-left-color: #28a745;
}

.stat-card-improved.stat-card-success::before {
    background: #28a745;
}

.stat-card-improved.stat-card-info {
    border-left-color: #17a2b8;
}

.stat-card-improved.stat-card-info::before {
    background: #17a2b8;
}

.stat-card-improved.stat-card-warning {
    border-left-color: #ffc107;
}

.stat-card-improved.stat-card-warning::before {
    background: #ffc107;
}

.stat-content-improved {
    text-align: center;
    width: 100%;
    z-index: 1;
    position: relative;
}

.stat-number-improved {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: #495057;
    line-height: 1;
    letter-spacing: -0.5px;
}

.stat-label-improved {
    font-size: 0.95rem;
    color: #6c757d;
    margin: 0;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1.3;
}

/* Schedule List Styles */
.schedule-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-height: 500px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.schedule-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #28a745;
    transition: all 0.2s ease;
}

.schedule-item:hover {
    background: #e9ecef;
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.schedule-time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #28a745;
    font-size: 0.95rem;
    min-width: 80px;
}

.schedule-time i {
    font-size: 0.9rem;
}

.schedule-details {
    flex: 1;
}

.schedule-class {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin: 0 0 0.5rem 0;
}

.schedule-info {
    display: flex;
    gap: 1.5rem;
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0 0 0.25rem 0;
}

.schedule-info span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.schedule-info i {
    font-size: 0.8rem;
    color: #28a745;
}

.schedule-duration {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}

/* Compact Schedule Items for Upcoming */
.upcoming-day-group {
    margin-bottom: 1.5rem;
}

.upcoming-day-group:last-child {
    margin-bottom: 0;
}

.upcoming-day-header {
    font-size: 0.95rem;
    font-weight: 600;
    color: #28a745;
    margin: 0 0 0.75rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.schedule-item-compact {
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-left-width: 3px;
}

.schedule-time-compact {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #28a745;
    font-size: 0.85rem;
    min-width: 70px;
}

.schedule-time-compact i {
    font-size: 0.8rem;
}

.schedule-details-compact {
    flex: 1;
}

.schedule-class-compact {
    font-size: 0.95rem;
    font-weight: 600;
    color: #495057;
    margin: 0 0 0.25rem 0;
}

.schedule-info-compact {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}

.schedule-info-compact span {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.schedule-info-compact i {
    font-size: 0.75rem;
    color: #28a745;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    font-size: 1rem;
    margin: 0;
}

/* Timetable Scroll Wrapper - Fixed width, horizontal scroll */
.timetable-scroll-wrapper {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}

.timetable-container-fixed {
    min-width: 1000px; /* Fixed minimum width to force horizontal scroll on mobile */
    width: 100%;
}

/* Teacher Timetable Class Box Styling - Fixed Dimensions for Consistency */
.teacher-timetable-class-box {
    background: white !important;
    border: 1px solid #d1d3d4 !important;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    width: 140px;
    height: 85px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    box-sizing: border-box;
}

.teacher-timetable-class-box .class-subject {
    color: #28a745 !important;
    font-weight: 600;
    font-size: 12px;
    margin-bottom: 3px;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.teacher-timetable-class-box .class-time {
    color: #495057 !important;
    font-size: 11px;
    font-weight: 500;
    margin-bottom: 2px;
    line-height: 1.1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.teacher-timetable-class-box .class-instructor {
    color: #6c757d !important;
    font-size: 10px;
    margin-bottom: 2px;
    line-height: 1.1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex-shrink: 0;
}

.teacher-timetable-class-box .class-room {
    color: #6c757d !important;
    font-size: 10px;
    font-weight: 500;
    line-height: 1.1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex-shrink: 0;
}

/* Scrollbar Styling */
.timetable-scroll-wrapper::-webkit-scrollbar {
    height: 8px;
}

.timetable-scroll-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.timetable-scroll-wrapper::-webkit-scrollbar-thumb {
    background: #28a745;
    border-radius: 4px;
}

.timetable-scroll-wrapper::-webkit-scrollbar-thumb:hover {
    background: #218838;
}

.schedule-list::-webkit-scrollbar {
    width: 6px;
}

.schedule-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.schedule-list::-webkit-scrollbar-thumb {
    background: #28a745;
    border-radius: 3px;
}

.schedule-list::-webkit-scrollbar-thumb:hover {
    background: #218838;
}

/* Mobile Responsiveness - Keep timetable scrollable */
@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 1rem;
    }
    
    .schedule-info {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .schedule-info-compact {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    /* Timetable remains fixed width and scrollable */
    .timetable-container-fixed {
        min-width: 1000px; /* Maintain fixed width on mobile */
    }
    
    .dashboard-card .card-header {
        padding: 1rem;
    }
    
    .card-title {
        font-size: 1.1rem;
    }
    
    .card-subtitle {
        font-size: 0.85rem;
    }
}

@media (max-width: 576px) {
    .welcome-title {
        font-size: 1.75rem;
    }
    
    .welcome-subtitle {
        font-size: 0.95rem;
    }
    
    .stat-number {
        font-size: 1.75rem;
    }
    
    .stat-label {
        font-size: 0.85rem;
    }
}
</style>
@endsection
