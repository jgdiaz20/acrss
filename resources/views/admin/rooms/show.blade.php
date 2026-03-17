@extends('layouts.admin')
@section('content')

<div class="row mb-3">
    <div class="col-lg-12">
        <a href="{{ route('admin.room-management.rooms.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Rooms
        </a>
        <button onclick="showQRCode({{ $room->id }}, '{{ $room->name }}')" class="btn btn-success ml-2">
            <i class="fas fa-qrcode mr-1"></i> View QR Code
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Room Details
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>
                        ID
                    </th>
                    <td>
                        {{ $room->id }}
                    </td>
                </tr>
                <tr>
                    <th>
                        Name
                    </th>
                    <td>
                        {{ $room->name }}
                    </td>
                </tr>
                <tr>
                    <th>
                        Description
                    </th>
                    <td>
                        {{ $room->description ?? 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <th>
                        Capacity
                    </th>
                    <td>
                        {{ $room->capacity ?? 'N/A' }} students
                    </td>
                </tr>
                <tr>
                    <th>
                        Room Type
                    </th>
                    <td>
                        @if($room->is_lab)
                            <span class="badge badge-warning">
                                <i class="fas fa-flask"></i> Laboratory
                            </span>
                        @else
                            <span class="badge badge-primary">
                                <i class="fas fa-chalkboard"></i> Classroom
                            </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>
                        Timetable
                    </th>
                    <td>
                        <a href="{{ route('admin.room-management.room-timetables.show', $room->id) }}" class="btn btn-info">
                            <i class="fas fa-calendar"></i> View Room Timetable
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Room Schedules
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Teacher</th>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($room->lessons as $lesson)
                        <tr>
                            <td>{{ $lesson->class->name ?? '' }}</td>
                            <td>{{ $lesson->teacher->name ?? '' }}</td>
                            <td>{{ \App\Lesson::WEEK_DAYS[$lesson->weekday] ?? '' }}</td>
                            <td>{{ $lesson->start_time }}</td>
                            <td>{{ $lesson->end_time }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@include('partials.qr-code-modal')
@section('scripts')
@parent
@include('partials.qr-code-modal-scripts')
@endsection
