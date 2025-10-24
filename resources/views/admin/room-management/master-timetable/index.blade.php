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
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-th"></i> Master Timetable
        </li>
    </ol>
</nav>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-th mr-2"></i>
            Master Timetable Overview
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" id="refreshStats">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- Simple Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-door-open"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Rooms</span>
                        <span class="info-box-number" id="totalRooms">{{ $totalRooms }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-chalkboard-teacher"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Lessons</span>
                        <span class="info-box-number" id="totalLessons">{{ $totalLessons }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Teachers</span>
                        <span class="info-box-number" id="activeTeachers">{{ $activeTeachers }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Scheduling Conflicts</span>
                        <span class="info-box-number" id="totalConflicts">{{ $totalConflicts }}</span>
                    </div>
                </div>
            </div>
        </div>

        

        <!-- Main Action Button -->
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ route('admin.room-management.master-timetable.show', 1) }}" 
                   class="btn btn-primary btn-lg">
                    <i class="fas fa-calendar-week mr-2"></i>
                    View Master Timetable
                </a>
                <p class="text-muted mt-2">
                    <small>Click to view the complete master timetable grid starting from Monday</small>
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .info-box {
        display: block;
        min-height: 90px;
        background: #fff;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        border-radius: 2px;
        margin-bottom: 15px;
    }

    .info-box-icon {
        border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 90px;
        width: 90px;
        text-align: center;
        font-size: 45px;
        line-height: 90px;
        background: rgba(0,0,0,0.2);
        color: #fff;
    }

    .info-box-content {
        padding: 5px 10px;
        margin-left: 90px;
    }

    .info-box-text {
        text-transform: uppercase;
        font-weight: bold;
        font-size: 14px;
    }

    .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 18px;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }

    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
    }

    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .progress-xs {
        height: 8px;
    }

    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        margin-bottom: 1rem;
    }

    .card-header {
        background-color: rgba(0,0,0,.03);
        border-bottom: 1px solid rgba(0,0,0,.125);
    }

    .d-grid {
        display: grid;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .btn-block {
        display: block;
        width: 100%;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Refresh button handler
    $('#refreshStats').on('click', function() {
        location.reload();
    });
    
    
});
</script>
@endsection
