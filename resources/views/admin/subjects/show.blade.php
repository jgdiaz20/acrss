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
            <a href="{{ route('admin.subjects.index') }}">
                <i class="fas fa-book"></i> Subjects
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-eye"></i> {{ $subject->name }}
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-book text-success mr-2"></i>
                {{ $subject->name }}
            </h1>
            <p class="page-subtitle text-muted">
                Subject Details & Information
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Subjects
                </a>
                @can('subject_edit')
                    <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endcan
                @can('subject_edit')
                    <a href="{{ route('admin.subjects.assign-teachers', $subject->id) }}" class="btn btn-primary">
                        <i class="fas fa-users"></i> Teachers
                    </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Subject Information -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle text-primary mr-2"></i>
                    Subject Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="info-label">Subject Name</label>
                            <div class="info-value">{{ $subject->name }}</div>
                        </div>
                        <div class="info-item mb-3">
                            <label class="info-label">Subject Code</label>
                            <div class="info-value">
                                <span class="badge badge-info">{{ $subject->code }}</span>
                            </div>
                        </div>
                        <div class="info-item mb-3">
                            <label class="info-label">Subject Type</label>
                            <div class="info-value">
                                <span class="badge badge-{{ $subject->type === 'core' ? 'primary' : 'secondary' }}">
                                    {{ \App\Subject::SUBJECT_TYPES[$subject->type] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="info-label">Credits</label>
                            <div class="info-value">{{ $subject->credits }}</div>
                        </div>
                        <div class="info-item mb-3">
                            <label class="info-label">Status</label>
                            <div class="info-value">
                                <span class="badge badge-{{ $subject->is_active ? 'success' : 'danger' }}">
                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="info-item mb-3">
                            <label class="info-label">Requirements</label>
                            <div class="info-value">
                                @if($subject->requires_lab)
                                    <span class="badge badge-warning mr-1">Laboratory</span>
                                @endif
                                @if($subject->requires_equipment)
                                    <span class="badge badge-info mr-1">Equipment</span>
                                @endif
                                @if(!$subject->requires_lab && !$subject->requires_equipment)
                                    <span class="badge badge-success">No Special Requirements</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($subject->description)
                    <div class="info-item">
                        <label class="info-label">Description</label>
                        <div class="info-value">{{ $subject->description }}</div>
                    </div>
                @endif
                
                @if($subject->equipment_requirements)
                    <div class="info-item">
                        <label class="info-label">Equipment Requirements</label>
                        <div class="info-value">{{ $subject->equipment_requirements }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar text-success mr-2"></i>
                    Statistics
                </h3>
            </div>
            <div class="card-body">
                <div class="stat-item text-center mb-3">
                    <div class="stat-number text-primary">{{ $stats['total_lessons'] }}</div>
                    <div class="stat-label">Total Lessons</div>
                </div>
                <div class="stat-item text-center mb-3">
                    <div class="stat-number text-success">{{ $stats['active_teachers'] }}</div>
                    <div class="stat-label">Active Teachers</div>
                </div>
                <div class="stat-item text-center">
                    <div class="stat-number text-info">{{ number_format($stats['weekly_hours'], 1) }}</div>
                    <div class="stat-label">Weekly Hours</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($subject->teachers->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chalkboard-teacher text-warning mr-2"></i>
            Assigned Teachers
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Teacher Name</th>
                        <th>Role</th>
                        <th>Experience</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subject->teachers as $teacher)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $teacher->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($teacher->pivot->is_primary)
                                    <span class="badge badge-primary">Primary</span>
                                @else
                                    <span class="badge badge-secondary">Secondary</span>
                                @endif
                            </td>
                            <td>{{ $teacher->pivot->experience_years }} years</td>
                            <td>
                                <span class="badge badge-{{ $teacher->pivot->is_active ? 'success' : 'danger' }}">
                                    {{ $teacher->pivot->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $teacher->pivot->notes ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@if($subject->lessons->count() > 0)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt text-info mr-2"></i>
            Recent Lessons
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Class</th>
                        <th>Teacher</th>
                        <th>Room</th>
                        <th>Day</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subject->lessons->take(10) as $lesson)
                        <tr>
                            <td>
                                <span class="badge badge-outline-primary">{{ $lesson->class->name ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $lesson->teacher->name ?? 'N/A' }}</td>
                            <td>{{ $lesson->room->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-outline-secondary">
                                    {{ \App\Lesson::WEEK_DAYS[$lesson->weekday] ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $lesson->start_time }} - {{ $lesson->end_time }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($subject->lessons->count() > 10)
            <div class="card-footer bg-light">
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Showing first 10 lessons. Total: {{ $subject->lessons->count() }}
                </small>
            </div>
        @endif
    </div>
</div>
@endif

@endsection
