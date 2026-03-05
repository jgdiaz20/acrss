<?php $__env->startSection('content'); ?>

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('admin.home')); ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('admin.school-classes.index')); ?>">
                <i class="fas fa-school"></i> School Classes
            </a>
        </li>
        <?php if($schoolClass->program): ?>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('admin.school-classes.program', $schoolClass->program->type)); ?>">
                    <i class="fas fa-<?php echo e($schoolClass->program->type == 'senior_high' ? 'graduation-cap' : 'university'); ?>"></i>
                    <?php echo e($schoolClass->program->type == 'senior_high' ? 'Senior High School' : 'College'); ?>

                </a>
            </li>
            <?php if($schoolClass->gradeLevel): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.school-classes.program.grade', [$schoolClass->program->type, $schoolClass->gradeLevel->id])); ?>">
                        <i class="fas fa-layer-group"></i> <?php echo e($schoolClass->gradeLevel->level_name); ?>

                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-edit"></i> Edit <?php echo e($schoolClass->name); ?>

        </li>
    </ol>
</nav>

<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <?php echo e(trans('global.edit')); ?> <?php echo e(trans('cruds.schoolClass.title_singular')); ?>

    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo e(route("admin.school-classes.update", [$schoolClass->id])); ?>" enctype="multipart/form-data">
            <?php echo method_field('PUT'); ?>
            <?php echo csrf_field(); ?>
            
            <?php if($preSelectedGradeLevelId): ?>
                <input type="hidden" name="grade_level_id" value="<?php echo e($preSelectedGradeLevelId); ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="program_id">Academic Program</label>
                        <input type="text" class="form-control" value="<?php echo e($schoolClass->program->name ?? 'Unknown Program'); ?>" readonly>
                        <input type="hidden" name="program_id" value="<?php echo e($schoolClass->program_id); ?>">
                        <small class="form-text text-muted">
                            <i class="fas fa-lock mr-1"></i>
                            Program is locked.
                        </small>
                    </div>
                </div>
                
                <?php if($preSelectedGradeLevelId): ?>
                    <!-- Hidden input for pre-selected grade level -->
                    <input type="hidden" name="grade_level_id" value="<?php echo e($preSelectedGradeLevelId); ?>">
                    <input type="hidden" name="program_id" value="<?php echo e($preSelectedProgramId); ?>">
                <?php else: ?>
                    <!-- Only show grade level field for Senior High School programs -->
                    <div class="col-md-6" id="grade_level_container" style="<?php echo e(($schoolClass->program && $schoolClass->program->type !== 'senior_high') ? 'display: none;' : ''); ?>">
                        <div class="form-group">
                            <label class="required" for="grade_level_id" id="grade_level_label">Grade Level</label>
                            <select class="form-control <?php echo e($errors->has('grade_level_id') ? 'is-invalid' : ''); ?>" name="grade_level_id" id="grade_level_id" <?php echo e(($schoolClass->program && $schoolClass->program->type !== 'senior_high') ? '' : 'required'); ?>>
                                <option value="">Select Grade Level</option>
                                <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gradeLevel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($gradeLevel->id); ?>" <?php echo e((old('grade_level_id', $schoolClass->grade_level_id) == $gradeLevel->id) ? 'selected' : ''); ?>>
                                        <?php echo e($gradeLevel->level_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($errors->has('grade_level_id')): ?>
                                <div class="invalid-feedback">
                                    <?php echo e($errors->first('grade_level_id')); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="name">Section Name</label>
                        <input class="form-control <?php echo e($errors->has('name') ? 'is-invalid' : ''); ?>" type="text" name="name" id="name" value="<?php echo e(old('name', $schoolClass->name)); ?>" required>
                        <?php if($errors->has('name')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('name')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">e.g., STEM-A, ABM-B, Computer Engineering 1-A</span>
                    </div>
                </div>
            </div>
            
            <!-- Hidden fields -->
            <input type="hidden" name="is_active" value="1">
            
            <div class="form-group">
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> <?php echo e(trans('global.save')); ?>

                </button>
                <?php if($preSelectedProgramId && $preSelectedGradeLevelId): ?>
                    <?php
                        $program = \App\AcademicProgram::find($preSelectedProgramId);
                    ?>
                    <?php if($program): ?>
                        <a href="<?php echo e(route('admin.school-classes.program.grade', [$program->type, $preSelectedGradeLevelId])); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                <?php elseif($preSelectedProgramId): ?>
                    <?php
                        $program = \App\AcademicProgram::find($preSelectedProgramId);
                    ?>
                    <?php if($program): ?>
                        <a href="<?php echo e(route('admin.school-classes.program', $program->type)); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                <?php elseif($schoolClass->program && $schoolClass->gradeLevel): ?>
                    <a href="<?php echo e(route('admin.school-classes.program.grade', [$schoolClass->program->type, $schoolClass->gradeLevel->id])); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                <?php elseif($schoolClass->program): ?>
                    <a href="<?php echo e(route('admin.school-classes.program', $schoolClass->program->type)); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('admin.school-classes.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    var preSelectedGradeLevelId = '<?php echo e($preSelectedGradeLevelId); ?>';
    var isCollegeContext = preSelectedGradeLevelId && '<?php echo e($schoolClass->program->type ?? ''); ?>' === 'college';
    
    // Only run the program change logic if we don't have pre-selected values or if it's not a college context
    if (!isCollegeContext) {
        // Load grade levels when program changes
        $('#program_id').change(function() {
            var programId = $(this).val();
            var gradeLevelSelect = $('#grade_level_id');
            var gradeLevelContainer = $('#grade_level_container');
            var currentGradeLevelId = '<?php echo e(old('grade_level_id', $schoolClass->grade_level_id)); ?>';
            
            // Get the selected program's type
            var selectedOption = $(this).find('option:selected');
            var programName = selectedOption.text();
            
            // Check if it's a senior high school program
            if (programName.toLowerCase().includes('senior high') || 
                programName.toLowerCase().includes('stem') || 
                programName.toLowerCase().includes('abm') || 
                programName.toLowerCase().includes('humss') || 
                programName.toLowerCase().includes('gas')) {
                
                // Show grade level field for senior high school programs
                gradeLevelContainer.show();
                gradeLevelSelect.attr('required', 'required');
                
                gradeLevelSelect.html('<option value="">Loading...</option>');
                
                if (programId) {
                    $.ajax({
                        url: '<?php echo e(route("admin.admin.grade-levels.by-program", "")); ?>/' + programId,
                        type: 'GET',
                        success: function(data) {
                            gradeLevelSelect.html('<option value="">Select Grade Level</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == currentGradeLevelId) ? 'selected' : '';
                                gradeLevelSelect.append('<option value="' + value.id + '" ' + selected + '>' + value.level_name + '</option>');
                            });
                        },
                        error: function() {
                            gradeLevelSelect.html('<option value="">Error loading grade levels</option>');
                        }
                    });
                } else {
                    gradeLevelSelect.html('<option value="">Select Grade Level</option>');
                }
            } else {
                // Hide grade level field for all other programs (college programs)
                gradeLevelContainer.hide();
                gradeLevelSelect.removeAttr('required');
                gradeLevelSelect.val('');
            }
        });
        
        // Load grade levels on page load if program is already selected
        if ($('#program_id').val()) {
            $('#program_id').trigger('change');
        }
    } else {
        // For college context with pre-selected values, ensure the program field is also locked
        console.log('College context detected - grade level field hidden');
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/school-classes/edit.blade.php ENDPATH**/ ?>