@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        Edit Room
    </div>

    <div class="card-body">
        <form action="{{ route("admin.room-management.rooms.update", [$room->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">Room Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($room) ? $room->name : '') }}" required placeholder="e.g., Room 101, Computer Lab A">
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
            </div>
            
            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief description of the room's purpose or features">{{ old('description', isset($room) ? $room->description : '') }}</textarea>
                @if($errors->has('description'))
                    <em class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </em>
                @endif
            </div>
            
            <div class="form-group {{ $errors->has('capacity') ? 'has-error' : '' }}">
                <label for="capacity">Capacity *</label>
                <input type="number" id="capacity" name="capacity" class="form-control" value="{{ old('capacity', isset($room) ? $room->capacity : '') }}" min="1" max="500" required placeholder="Maximum number of students">
                @if($errors->has('capacity'))
                    <em class="invalid-feedback">
                        {{ $errors->first('capacity') }}
                    </em>
                @endif
            </div>
            
            <div class="form-group {{ $errors->has('is_lab') ? 'has-error' : '' }}">
                <label for="is_lab">Room Type *</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_lab" id="is_lab_classroom" value="0" {{ old('is_lab', isset($room) ? $room->is_lab : '0') == '0' ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_lab_classroom">
                        <strong>Classroom</strong> - Standard teaching room
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_lab" id="is_lab_lab" value="1" {{ old('is_lab', isset($room) ? $room->is_lab : '0') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_lab_lab">
                        <strong>Laboratory</strong> - Specialized lab for practical work
                    </label>
                </div>
                @if($errors->has('is_lab'))
                    <em class="invalid-feedback">
                        {{ $errors->first('is_lab') }}
                    </em>
                @endif
            </div>
            
            <div class="form-group {{ $errors->has('has_equipment') ? 'has-error' : '' }}">
                <label for="has_equipment">Equipment Available</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="has_equipment" id="has_equipment" value="1" {{ old('has_equipment', isset($room) ? $room->has_equipment : false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_equipment">
                        This room has specialized equipment (computers, projectors, lab equipment, etc.)
                    </label>
                </div>
                @if($errors->has('has_equipment'))
                    <em class="invalid-feedback">
                        {{ $errors->first('has_equipment') }}
                    </em>
                @endif
            </div>
            
            <div class="form-group">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> Laboratory rooms are required for subjects that need practical work. Equipment availability helps match subjects with appropriate rooms.
                </div>
            </div>
            
            <div class="form-group">
                <input class="btn btn-success" type="submit" value="Update Room">
                <a href="{{ route('admin.room-management.rooms.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
