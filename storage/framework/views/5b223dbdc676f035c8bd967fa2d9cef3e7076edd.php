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
        <?php if($preSelectedProgramId): ?>
            <?php
                $program = \App\AcademicProgram::find($preSelectedProgramId);
            ?>
            <?php if($program): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.school-classes.program', $program->type)); ?>">
                        <i class="fas fa-<?php echo e($program->type == 'senior_high' ? 'graduation-cap' : 'university'); ?>"></i>
                        <?php echo e($program->type == 'senior_high' ? 'Senior High School' : 'College'); ?>

                    </a>
                </li>
                <?php if($preSelectedGradeLevelId): ?>
                    <?php
                        $gradeLevel = \App\GradeLevel::find($preSelectedGradeLevelId);
                    ?>
                    <?php if($gradeLevel): ?>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('admin.school-classes.program.grade', [$program->type, $gradeLevel->id])); ?>">
                                <i class="fas fa-layer-group"></i> <?php echo e($gradeLevel->level_name); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-plus"></i> Create Class
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
        <?php echo e(trans('global.create')); ?> <?php echo e(trans('cruds.schoolClass.title_singular')); ?>

    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo e(route("admin.school-classes.store")); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="program_id">Academic Program</label>
                        <?php if($preSelectedProgramId): ?>
                            <!-- Show current program as read-only when creating from specific program context -->
                            <?php
                                $selectedProgram = $academicPrograms->where('id', $preSelectedProgramId)->first();
                            ?>
                            <input type="text" class="form-control" value="<?php echo e($selectedProgram->name ?? 'Unknown Program'); ?>" readonly>
                            <input type="hidden" name="program_id" value="<?php echo e($preSelectedProgramId); ?>">
                            <small class="form-text text-muted">
                                <i class="fas fa-lock mr-1"></i>
                                Program is locked because you're creating within the program context.
                            </small>
                        <?php else: ?>
                            <!-- Allow program selection when creating from general context -->
                            <select class="form-control <?php echo e($errors->has('program_id') ? 'is-invalid' : ''); ?>" name="program_id" id="program_id" required>
                                <option value="">Select Program</option>
                                <?php $__currentLoopData = $academicPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($program->id); ?>" <?php echo e((old('program_id', $preSelectedProgramId) == $program->id) ? 'selected' : ''); ?>>
                                        <?php echo e($program->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($errors->has('program_id')): ?>
                                <div class="invalid-feedback">
                                    <?php echo e($errors->first('program_id')); ?>

                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if($preSelectedGradeLevelId): ?>
                    <!-- Hidden input for pre-selected grade level -->
                    <input type="hidden" name="grade_level_id" value="<?php echo e($preSelectedGradeLevelId); ?>">
                <?php else: ?>
                    <!-- Only show grade level field for Senior High School programs -->
                    <div class="col-md-6" id="grade_level_container" style="display: none;">
                        <div class="form-group">
                            <label class="required" for="grade_level_id" id="grade_level_label">Grade Level</label>
                            <select class="form-control <?php echo e($errors->has('grade_level_id') ? 'is-invalid' : ''); ?>" name="grade_level_id" id="grade_level_id">
                                <option value="">Select Grade Level</option>
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
                        <label class="required" for="name">Class Name</label>
                        <input class="form-control <?php echo e($errors->has('name') ? 'is-invalid' : ''); ?>" type="text" name="name" id="name" value="<?php echo e(old('name', '')); ?>" required>
                        <?php if($errors->has('name')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('name')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">e.g., STEM, ABM, Computer Engineering</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="section">Section</label>
                        <input class="form-control <?php echo e($errors->has('section') ? 'is-invalid' : ''); ?>" type="text" name="section" id="section" value="<?php echo e(old('section', '')); ?>">
                        <?php if($errors->has('section')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('section')); ?>

                            </div>
                        <?php endif; ?>
                        <span class="help-block">e.g., A, B, C or Alpha, Beta</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="max_students">Maximum Students</label>
                        <input class="form-control <?php echo e($errors->has('max_students') ? 'is-invalid' : ''); ?>" type="number" name="max_students" id="max_students" value="<?php echo e(old('max_students', '')); ?>" min="1">
                        <?php if($errors->has('max_students')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('max_students')); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <select class="form-control <?php echo e($errors->has('is_active') ? 'is-invalid' : ''); ?>" name="is_active" id="is_active">
                            <option value="1" <?php echo e(old('is_active', '1') == '1' ? 'selected' : ''); ?>>Active</option>
                            <option value="0" <?php echo e(old('is_active') == '0' ? 'selected' : ''); ?>>Inactive</option>
                        </select>
                        <?php if($errors->has('is_active')): ?>
                            <div class="invalid-feedback">
                                <?php echo e($errors->first('is_active')); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
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
    
    // Only run the program change logic if we don't have pre-selected values
    if (!preSelectedGradeLevelId) {
        // Load grade levels when program changes
        $('#program_id').change(function() {
            var programId = $(this).val();
            var gradeLevelSelect = $('#grade_level_id');
            var gradeLevelContainer = $('#grade_level_container');
            
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
                                gradeLevelSelect.append('<option value="' + value.id + '">' + value.level_name + '</option>');
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
        
        // Trigger change on page load if a program is already selected
        if ($('#program_id').val()) {
            $('#program_id').trigger('change');
        }
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/school-classes/create.blade.php ENDPATH**/ ?>