<?php $__env->startSection('content'); ?>

<div class="card">
    <div class="card-header">
        Create Subject
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo e(route("admin.subjects.store")); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label class="required" for="name">Subject Name</label>
                <input class="form-control <?php echo e($errors->has('name') ? 'is-invalid' : ''); ?>" type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required>
                <?php if($errors->has('name')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('name')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Enter the full name of the subject (e.g., Computer Programming)</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="code">Subject Code</label>
                <input class="form-control <?php echo e($errors->has('code') ? 'is-invalid' : ''); ?>" type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" required>
                <?php if($errors->has('code')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('code')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Enter a unique code for the subject (e.g., COMPROG)</span>
            </div>
            
            
            <div class="form-group">
                <label class="required" for="credits">Total Credits</label>
                <input class="form-control <?php echo e($errors->has('credits') ? 'is-invalid' : ''); ?>" type="number" name="credits" id="credits" value="<?php echo e(old('credits', 3)); ?>" min="1" max="3" required>
                <?php if($errors->has('credits')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('credits')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block" id="credits-help">Lab/Lecture mode: Enter credits (1-3) | Flexible mode: Auto-calculated (max 3)</span>
            </div>

            <div class="form-group">
                <label class="required" for="scheduling_mode">Scheduling Mode</label>
                <select class="form-control <?php echo e($errors->has('scheduling_mode') ? 'is-invalid' : ''); ?>" name="scheduling_mode" id="scheduling_mode" required>
                    <option value="">Select Mode</option>
                    <?php $__currentLoopData = \App\Subject::SCHEDULING_MODES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('scheduling_mode') == $key ? 'selected' : ''); ?>><?php echo e($mode); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('scheduling_mode')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('scheduling_mode')); ?>

                    </div>
                <?php endif; ?>
            </div>

            <div id="flexible-fields" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lecture_units">Lecture Units</label>
                            <input class="form-control <?php echo e($errors->has('lecture_units') ? 'is-invalid' : ''); ?>" type="number" name="lecture_units" id="lecture_units" value="<?php echo e(old('lecture_units', 0)); ?>" min="0" max="10">
                            <?php if($errors->has('lecture_units')): ?>
                                <div class="invalid-feedback">
                                    <?php echo e($errors->first('lecture_units')); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lab_units">Laboratory Units</label>
                            <input class="form-control <?php echo e($errors->has('lab_units') ? 'is-invalid' : ''); ?>" type="number" name="lab_units" id="lab_units" value="<?php echo e(old('lab_units', 0)); ?>" min="0" max="10">
                            <?php if($errors->has('lab_units')): ?>
                                <div class="invalid-feedback">
                                    <?php echo e($errors->first('lab_units')); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info" id="hours-summary" style="display: none;">
                    <strong>Total Hours:</strong> <span id="total-hours-display">0</span> hours
                    (<span id="lecture-hours-display">0</span> lecture + <span id="lab-hours-display">0</span> lab)
                </div>
            </div>
            
            <div class="form-group">
                <label class="required" for="type">Subject Type</label>
                <select class="form-control <?php echo e($errors->has('type') ? 'is-invalid' : ''); ?>" name="type" id="type" required>
                    <option value="">Select Type</option>
                    <?php $__currentLoopData = \App\Subject::SUBJECT_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('type') == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('type')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('type')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">Select the type of subject</span>
            </div>
            
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> Create Subject
                </button>
                <a href="<?php echo e(route('admin.subjects.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const schedulingMode = document.getElementById('scheduling_mode');
    const credits = document.getElementById('credits');
    const creditsHelp = document.getElementById('credits-help');
    const lectureUnits = document.getElementById('lecture_units');
    const labUnits = document.getElementById('lab_units');
    const flexibleFields = document.getElementById('flexible-fields');
    const lectureHoursDisplay = document.getElementById('lecture-hours-display');
    const labHoursDisplay = document.getElementById('lab-hours-display');
    const totalHoursDisplay = document.getElementById('total-hours-display');
    const hoursSummary = document.getElementById('hours-summary');

    function updateDisplay() {
        const mode = schedulingMode.value;
        
        if (mode === 'lab') {
            // Lab mode: Pure laboratory
            flexibleFields.style.display = 'none';
            lectureUnits.value = 0;
            labUnits.value = credits.value || 3;
            lectureUnits.removeAttribute('required');
            labUnits.removeAttribute('required');
            credits.removeAttribute('readonly');
            creditsHelp.textContent = 'Lab mode: Enter credits 1-3 (1 credit = 3 lab hours)';
        } else if (mode === 'lecture') {
            // Lecture mode: Pure lecture
            flexibleFields.style.display = 'none';
            lectureUnits.value = credits.value || 3;
            labUnits.value = 0;
            lectureUnits.removeAttribute('required');
            labUnits.removeAttribute('required');
            credits.removeAttribute('readonly');
            creditsHelp.textContent = 'Lecture mode: Enter credits 1-3 (1 credit = 1 lecture hour)';
        } else if (mode === 'flexible') {
            // Flexible mode: Show breakdown fields
            flexibleFields.style.display = 'block';
            lectureUnits.setAttribute('required', 'required');
            labUnits.setAttribute('required', 'required');
            credits.setAttribute('readonly', 'readonly');
            creditsHelp.textContent = 'Flexible mode: Auto-calculated from lecture + lab units (max 3 total)';
        } else {
            flexibleFields.style.display = 'none';
            lectureUnits.removeAttribute('required');
            labUnits.removeAttribute('required');
            credits.removeAttribute('readonly');
            creditsHelp.textContent = 'Select a scheduling mode';
        }
        
        calculateTotals();
    }

    function calculateTotals() {
        const mode = schedulingMode.value;
        const lecture = parseInt(lectureUnits.value) || 0;
        const lab = parseInt(labUnits.value) || 0;
        
        if (mode === 'lab') {
            // Lab mode: credits = lab units
            const totalCredits = lab;
            credits.value = totalCredits;
        } else if (mode === 'lecture') {
            // Lecture mode: credits = lecture units
            const totalCredits = lecture;
            credits.value = totalCredits;
        } else if (mode === 'flexible') {
            // Flexible mode: credits = lecture + lab
            const totalCredits = lecture + lab;
            credits.value = totalCredits;
            
            // Calculate hours
            const lectureHours = lecture * 1;
            const labHours = lab * 3;
            const totalHours = lectureHours + labHours;
            
            // Update display
            lectureHoursDisplay.textContent = lectureHours;
            labHoursDisplay.textContent = labHours;
            totalHoursDisplay.textContent = totalHours;
            hoursSummary.style.display = totalHours > 0 ? 'block' : 'none';
        }
    }

    // Event listeners
    schedulingMode.addEventListener('change', updateDisplay);
    lectureUnits.addEventListener('input', calculateTotals);
    labUnits.addEventListener('input', calculateTotals);

    // Initialize on page load
    updateDisplay();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/subjects/create.blade.php ENDPATH**/ ?>