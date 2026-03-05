
<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Academic Programs</h3>
                <div class="card-tools">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('academic_program_create')): ?>
                        <a class="btn btn-success btn-sm" href="<?php echo e(route('admin.academic-programs.create')); ?>">
                            <i class="fas fa-plus"></i> <?php echo e(trans('global.add')); ?> Academic Program
                        </a>
                    <?php endif; ?>
                    <a class="btn btn-outline-secondary btn-sm" href="<?php echo e(route('admin.academic-programs.index')); ?>">
                        <i class="fas fa-sync"></i> Refresh
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('admin.academic-programs.index')); ?>" class="mb-3">
                    <div class="form-row align-items-end">
                        <div class="col-md-4 mb-2">
                            <label for="q" class="small text-muted">Search</label>
                            <input type="text" name="q" id="q" value="<?php echo e($search ?? ''); ?>" class="form-control" placeholder="Search by name, code, or description">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="type" class="small text-muted">Type</label>
                            <select name="type" id="type" class="form-control" onchange="this.form.submit()">
                                <option value="" <?php echo e(empty($activeType) ? 'selected' : ''); ?>>All</option>
                                <option value="senior_high" <?php echo e((isset($activeType) && $activeType==='senior_high') ? 'selected' : ''); ?>>Senior High School</option>
                                <option value="diploma" <?php echo e((isset($activeType) && $activeType==='diploma') ? 'selected' : ''); ?>>Diploma Program (TESDA)</option>
                                <option value="college" <?php echo e((isset($activeType) && $activeType==='college') ? 'selected' : ''); ?>>College</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="sub_filter" class="small text-muted"><?php echo e($filterLabel ?? 'Course/Strand'); ?></label>
                            <input type="text" name="sub_filter" id="sub_filter" value="<?php echo e($subFilter ?? ''); ?>" class="form-control" placeholder="e.g., Information Technology or ABM">
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Search</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Duration (Years)</th>
                                <th>Grade Levels</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $academicPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($program->name); ?></strong>
                                    </td>
                                    <td><?php echo e($program->code); ?></td>
                                    <td>
                                        <?php
                                            $badgeColor = 'secondary';
                                            if ($program->type == 'senior_high') $badgeColor = 'primary';
                                            elseif ($program->type == 'diploma') $badgeColor = 'warning';
                                            elseif ($program->type == 'college') $badgeColor = 'info';
                                        ?>
                                        <span class="badge badge-<?php echo e($badgeColor); ?>">
                                            <?php echo e($program->type == 'diploma' ? 'Diploma Program' : ucfirst(str_replace('_', ' ', $program->type))); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($program->duration_years); ?></td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            <?php echo e($program->gradeLevels->count()); ?> levels
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('academic_program_show')): ?>
                                            <a class="btn btn-xs btn-primary" href="<?php echo e(route('admin.academic-programs.show', $program->id)); ?>">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        <?php endif; ?>

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('academic_program_edit')): ?>
                                            <a class="btn btn-xs btn-info" href="<?php echo e(route('admin.academic-programs.edit', $program->id)); ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        <?php endif; ?>

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('academic_program_delete')): ?>
                                            <form action="<?php echo e(route('admin.academic-programs.destroy', $program->id)); ?>" method="POST" class="d-inline-block ap-delete-form">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                <button type="button" class="btn btn-xs btn-danger trigger-ap-delete"
                                                    data-ap-id="<?php echo e($program->id); ?>"
                                                    data-ap-name="<?php echo e($program->name); ?>"
                                                    data-ap-code="<?php echo e($program->code); ?>"
                                                    data-ap-type="<?php echo e(ucfirst(str_replace('_', ' ', $program->type))); ?>"
                                                    data-ap-levels="<?php echo e($program->gradeLevels->count()); ?>">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).on('click', '.trigger-ap-delete', function(){
        const $btn = $(this);
        const $form = $btn.closest('form');
        $('#apDeleteId').text($btn.data('ap-id'));
        $('#apDeleteName').text($btn.data('ap-name'));
        $('#apDeleteCode').text($btn.data('ap-code'));
        $('#apDeleteType').text($btn.data('ap-type'));
        $('#apDeleteLevels').text($btn.data('ap-levels'));
        $('#confirmApDeleteBtn').off('click').on('click', function(){
            $form.trigger('submit');
        });
        $('#apDeleteModal').modal('show');
    });
});
</script>

<!-- Academic Program Delete Confirmation Modal -->
<div class="modal fade" id="apDeleteModal" tabindex="-1" role="dialog" aria-labelledby="apDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="apDeleteModalLabel"><i class="fas fa-trash-alt mr-1"></i> Confirm Delete Academic Program</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>You're about to delete this academic program:</p>
        <ul class="list-unstyled mb-0">
          <li><strong>ID:</strong> <span id="apDeleteId"></span></li>
          <li><strong>Name:</strong> <span id="apDeleteName"></span></li>
          <li><strong>Code:</strong> <span id="apDeleteCode"></span></li>
          <li><strong>Type:</strong> <span id="apDeleteType"></span></li>
          <li><strong>Grade Levels:</strong> <span id="apDeleteLevels"></span></li>
        </ul>
        <div class="alert alert-warning mt-3">
          <i class="fas fa-exclamation-triangle mr-1"></i> This action cannot be undone. If classes or levels reference this program, deletion will be blocked.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmApDeleteBtn"><i class="fas fa-trash-alt"></i> Delete</button>
      </div>
    </div>
  </div>
 </div>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/academic-programs/index.blade.php ENDPATH**/ ?>