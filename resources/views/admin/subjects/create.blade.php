@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Create Subject
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.subjects.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">Subject Name</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">Enter the full name of the subject (e.g., Computer Programming)</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="code">Subject Code</label>
                <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code') }}" required>
                @if($errors->has('code'))
                    <div class="invalid-feedback">
                        {{ $errors->first('code') }}
                    </div>
                @endif
                <span class="help-block">Enter a unique code for the subject (e.g., COMPROG)</span>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description" rows="3">{{ old('description') }}</textarea>
                @if($errors->has('description'))
                    <div class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </div>
                @endif
                <span class="help-block">Optional description of the subject</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="credits">Credits</label>
                <input class="form-control {{ $errors->has('credits') ? 'is-invalid' : '' }}" type="number" name="credits" id="credits" value="{{ old('credits', 3) }}" min="1" max="10" required>
                @if($errors->has('credits'))
                    <div class="invalid-feedback">
                        {{ $errors->first('credits') }}
                    </div>
                @endif
                <span class="help-block">Number of credit hours for this subject</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="type">Subject Type</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value="">Select Type</option>
                    @foreach(\App\Subject::SUBJECT_TYPES as $key => $type)
                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </div>
                @endif
                <span class="help-block">Select the type of subject</span>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="requires_lab" id="requires_lab" value="1" {{ old('requires_lab') ? 'checked' : '' }}>
                    <label class="form-check-label" for="requires_lab">
                        Requires Laboratory
                    </label>
                </div>
                <span class="help-block">Check if this subject requires laboratory facilities</span>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="requires_equipment" id="requires_equipment" value="1" {{ old('requires_equipment') ? 'checked' : '' }}>
                    <label class="form-check-label" for="requires_equipment">
                        Requires Special Equipment
                    </label>
                </div>
                <span class="help-block">Check if this subject requires special equipment</span>
            </div>
            
            <div class="form-group">
                <label for="equipment_requirements">Equipment Requirements</label>
                <textarea class="form-control {{ $errors->has('equipment_requirements') ? 'is-invalid' : '' }}" name="equipment_requirements" id="equipment_requirements" rows="2">{{ old('equipment_requirements') }}</textarea>
                @if($errors->has('equipment_requirements'))
                    <div class="invalid-feedback">
                        {{ $errors->first('equipment_requirements') }}
                    </div>
                @endif
                <span class="help-block">List any specific equipment requirements</span>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
                <span class="help-block">Check if this subject is currently active</span>
            </div>
            
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> Create Subject
                </button>
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
