
<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Academic Program Details</h3>
                <div class="card-tools">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('academic_program_edit')): ?>
                        <a class="btn btn-info btn-sm" href="<?php echo e(route('admin.academic-programs.edit', $academicProgram->id)); ?>">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.academic-programs.index')); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Programs
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>Program Name</th>
                                <td><?php echo e($academicProgram->name); ?></td>
                            </tr>
                            <tr>
                                <th>Program Code</th>
                                <td><?php echo e($academicProgram->code); ?></td>
                            </tr>
                            <tr>
                                <th>Program Type</th>
                                <td>
                                    <span class="badge badge-<?php echo e($academicProgram->type == 'senior_high' ? 'primary' : 'info'); ?>">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $academicProgram->type))); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Duration</th>
                                <td><?php echo e($academicProgram->duration_years); ?> years</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php if($academicProgram->is_active): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Description</h4>
                            </div>
                            <div class="card-body">
                                <p><?php echo e($academicProgram->description ?? 'No description provided.'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Grade Levels</h4>
                            </div>
                            <div class="card-body">
                                <?php if($academicProgram->gradeLevels->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Level Name</th>
                                                    <th>Description</th>
                                                    <th>Order</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $academicProgram->gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gradeLevel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($gradeLevel->level_name); ?></td>
                                                        <td><?php echo e($gradeLevel->description ?? 'N/A'); ?></td>
                                                        <td><?php echo e($gradeLevel->order); ?></td>
                                                        <td>
                                                            <?php if($gradeLevel->is_active): ?>
                                                                <span class="badge badge-success">Active</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary">Inactive</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No grade levels found for this program.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/academic-programs/show.blade.php ENDPATH**/ ?>