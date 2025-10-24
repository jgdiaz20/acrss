@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Create Academic Program
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.academic-programs.store") }}" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="name">Program Name</label>
                        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name') }}" required>
                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                        <span class="help-block">e.g., Science, Technology, Engineering, and Mathematics</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="code">Program Code</label>
                        <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code') }}" required>
                        @if($errors->has('code'))
                            <div class="invalid-feedback">
                                {{ $errors->first('code') }}
                            </div>
                        @endif
                        <span class="help-block">e.g., STEM, ABM, BSIT</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="type">Program Type</label>
                        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                            <option value="">Select Program Type</option>
                            <option value="senior_high" data-duration="2" {{ old('type') == 'senior_high' ? 'selected' : '' }}>Senior High School (2 years)</option>
                            <option value="diploma" data-duration="3" {{ old('type') == 'diploma' ? 'selected' : '' }}>Diploma Program / TESDA (3 years)</option>
                            <option value="college" data-duration="4" {{ old('type') == 'college' ? 'selected' : '' }}>College (4 years)</option>
                        </select>
                        @if($errors->has('type'))
                            <div class="invalid-feedback">
                                {{ $errors->first('type') }}
                            </div>
                        @endif
                        <span class="help-block">Duration will be automatically set based on program type</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="duration_years">Duration (Years)</label>
                        <input class="form-control {{ $errors->has('duration_years') ? 'is-invalid' : '' }}" type="number" name="duration_years" id="duration_years" value="{{ old('duration_years') }}" min="1" max="10" required readonly>
                        @if($errors->has('duration_years'))
                            <div class="invalid-feedback">
                                {{ $errors->first('duration_years') }}
                            </div>
                        @endif
                        <span class="help-block">Automatically set based on program type</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description" rows="3">{{ old('description') }}</textarea>
                        @if($errors->has('description'))
                            <div class="invalid-feedback">
                                {{ $errors->first('description') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <select class="form-control {{ $errors->has('is_active') ? 'is-invalid' : '' }}" name="is_active" id="is_active">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @if($errors->has('is_active'))
                            <div class="invalid-feedback">
                                {{ $errors->first('is_active') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
                <a href="{{ route('admin.academic-programs.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-fill duration based on program type
    $('#type').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var duration = selectedOption.data('duration');
        
        if (duration) {
            $('#duration_years').val(duration);
        } else {
            $('#duration_years').val('');
        }
    });
    
    // Trigger on page load if type is already selected
    if ($('#type').val()) {
        $('#type').trigger('change');
    }
});
</script>
@endsection
