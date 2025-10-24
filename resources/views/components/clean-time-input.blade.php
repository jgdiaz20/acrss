@push('styles')
    <link href="{{ asset('css/time-input-clean.css') }}" rel="stylesheet">
@endpush

<div class="form-group {{ $attributes->get('containerClass') }}">
    @if($label)
        <label class="{{ $required ? 'required' : '' }}" for="{{ $id }}">{{ $label }}</label>
    @endif
    <input 
        type="time" 
        class="form-control time-input-clean {{ $errors->has($name) ? 'is-invalid' : '' }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        step="{{ $step }}"
    >
    @if($errors->has($name))
        <div class="invalid-feedback">
            {{ $errors->first($name) }}
        </div>
    @endif
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeInputs = document.querySelectorAll('.time-input-clean');
    
    timeInputs.forEach(input => {
        input.addEventListener('focus', (e) => {
            e.preventDefault();
            // Prevent native time picker from showing
            input.blur();
            
            // Custom time selection logic
            const current = input.value || '07:00';
            const picker = new TimePickerDialog(input, {
                initialTime: current,
                onChange: (time) => {
                    input.value = time;
                    input.dispatchEvent(new Event('change'));
                }
            });
        });
    });
});

class TimePickerDialog {
    constructor(inputElement, options) {
        this.input = inputElement;
        this.options = {
            step: 30, // minutes
            minTime: '07:00',
            maxTime: '18:00',
            ...options
        };
        
        this.createAndShowDialog();
    }

    createAndShowDialog() {
        const times = this.generateTimeSlots();
        const select = document.createElement('select');
        select.className = 'time-select';
        
        times.forEach(time => {
            const option = document.createElement('option');
            option.value = time.value;
            option.textContent = time.label;
            if (time.value === this.input.value) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        // Position the select element
        const rect = this.input.getBoundingClientRect();
        select.style.position = 'absolute';
        select.style.top = `${rect.bottom}px`;
        select.style.left = `${rect.left}px`;
        select.style.width = `${rect.width}px`;
        select.style.zIndex = '1000';

        document.body.appendChild(select);

        // Handle selection
        select.addEventListener('change', () => {
            this.options.onChange(select.value);
            select.remove();
        });

        // Handle click outside
        document.addEventListener('click', (e) => {
            if (e.target !== select && e.target !== this.input) {
                select.remove();
            }
        }, { once: true });

        select.focus();
    }

    generateTimeSlots() {
        const slots = [];
        let current = moment(this.options.minTime, 'HH:mm');
        const end = moment(this.options.maxTime, 'HH:mm');
        
        while (current <= end) {
            slots.push({
                value: current.format('HH:mm'),
                label: current.format('h:mm A')
            });
            current.add(this.options.step, 'minutes');
        }
        return slots;
    }
}</script>
@endpush
@endonce