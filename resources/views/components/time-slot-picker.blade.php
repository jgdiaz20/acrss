@push('styles')
    <link href="{{ asset('css/time-slot-manager.css') }}" rel="stylesheet">
@endpush

<div class="form-group">
    <div class="row">
        <div class="col-md-6">
            <label class="required" for="start_time">Start Time</label>
            <select class="form-control time-select {{ $errors->has('start_time') ? 'is-invalid' : '' }}" 
                    name="start_time" 
                    id="start_time" 
                    required>
                <option value="">Select Start Time</option>
            </select>
            @if($errors->has('start_time'))
                <div class="invalid-feedback">
                    {{ $errors->first('start_time') }}
                </div>
            @endif
        </div>
        <div class="col-md-6">
            <label class="required" for="end_time">End Time</label>
            <select class="form-control time-select {{ $errors->has('end_time') ? 'is-invalid' : '' }}" 
                    name="end_time" 
                    id="end_time" 
                    required>
                <option value="">Select End Time</option>
            </select>
            @if($errors->has('end_time'))
                <div class="invalid-feedback">
                    {{ $errors->first('end_time') }}
                </div>
            @endif
        </div>
    </div>
    <div id="time-feedback" class="time-feedback"></div>
</div>

@push('scripts')
    <script src="{{ asset('js/time-slot-manager.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.timeSlotManager = new TimeSlotManager();
        });
    </script>
@endpush