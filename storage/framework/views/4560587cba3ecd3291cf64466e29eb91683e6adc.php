
<?php $__env->startSection('content'); ?>

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('admin.home')); ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-users"></i> User Management
        </li>
    </ol>
</nav>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_create')): ?>
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="<?php echo e(route("admin.users.create")); ?>">
                <i class="fas fa-plus"></i> <?php echo e(trans('global.add')); ?> <?php echo e(trans('cruds.user.title_singular')); ?>

            </a>
            <a class="btn btn-primary" href="<?php echo e(route("admin.users.create-student")); ?>">
                <i class="fas fa-user-graduate"></i> <?php echo e(trans('global.add')); ?> New Student
            </a>
        </div>
    </div>
<?php endif; ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users mr-2"></i>
            <?php echo e(trans('cruds.user.title_singular')); ?> <?php echo e(trans('global.list')); ?>

        </h3>
        <div class="card-tools">
            <span class="badge badge-primary mr-2">Total: <?php echo e($users->count()); ?> users</span>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="columnToggleBtn">
                    <i class="fas fa-columns"></i> Show/Hide Columns
                </button>
                <div class="dropdown-menu dropdown-menu-right" id="columnDropdown" style="min-width: 200px;">
                    <h6 class="dropdown-header">Toggle Column Visibility</h6>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="1" type="checkbox" checked>
                            <label class="form-check-label">ID</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="2" type="checkbox" checked>
                            <label class="form-check-label">Name</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="3" type="checkbox" checked>
                            <label class="form-check-label">Email</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="4" type="checkbox" checked>
                            <label class="form-check-label">Email Verified</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="5" type="checkbox" checked>
                            <label class="form-check-label">Roles</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="6" type="checkbox" checked>
                            <label class="form-check-label">Class</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="7" type="checkbox" checked>
                            <label class="form-check-label">Created</label>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item">
                        <button type="button" class="btn btn-sm btn-outline-info btn-block" id="restoreColumns">
                            <i class="fas fa-undo"></i> Restore All Columns
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Per Page Selector and Results Info -->
        <div class="row mb-2">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <label class="mb-0 mr-2">Show:</label>
                    <select class="form-control form-control-sm" style="width: auto;" id="per-page-selector" onchange="changePerPage()">
                        <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10</option>
                        <option value="20" <?php echo e(request('per_page') == 20 ? 'selected' : ''); ?>>20</option>
                        <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                        <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100</option>
                    </select>
                    <span class="ml-2 text-muted">entries per page</span>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <span class="text-muted">
                    Showing <?php echo e($users->firstItem() ?? 0); ?> to <?php echo e($users->lastItem() ?? 0); ?> of <?php echo e($users->total()); ?> users
                </span>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-User">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            <?php echo e(trans('cruds.user.fields.id')); ?>

                        </th>
                        <th>
                            <?php echo e(trans('cruds.user.fields.name')); ?>

                        </th>
                        <th>
                            <?php echo e(trans('cruds.user.fields.email')); ?>

                        </th>
                        <th>
                            <?php echo e(trans('cruds.user.fields.email_verified_at')); ?>

                        </th>
                        <th>
                            <?php echo e(trans('cruds.user.fields.roles')); ?>

                        </th>
                        <th>
                            <?php echo e(trans('cruds.user.fields.class')); ?>

                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr data-entry-id="<?php echo e($user->id); ?>" data-user-id="<?php echo e($user->id); ?>">
                            <td>

                            </td>
                            <td>
                                <?php echo e($user->id ?? ''); ?>

                            </td>
                            <td>
                                <?php echo e($user->name ?? ''); ?>

                            </td>
                            <td>
                                <?php echo e($user->email ?? ''); ?>

                            </td>
                            <td>
                                <?php echo e($user->email_verified_at ?? ''); ?>

                            </td>
                            <td>
                                <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge badge-info"><?php echo e($item->title); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td>
                                <?php echo e($user->class->name ?? ''); ?>

                            </td>
                            <td>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_show')): ?>
                                    <a class="btn btn-xs btn-primary" href="<?php echo e(route('admin.users.show', $user->id)); ?>">
                                        <?php echo e(trans('global.view')); ?>

                                    </a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_edit')): ?>
                                    <a class="btn btn-xs btn-info" href="<?php echo e(route('admin.users.edit', $user->id)); ?>">
                                        <?php echo e(trans('global.edit')); ?>

                                    </a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_delete')): ?>
                                    <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST" onsubmit="return confirm('<?php echo e(trans('global.areYouSure')); ?>');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                        <input type="submit" class="btn btn-xs btn-danger" value="<?php echo e(trans('global.delete')); ?>">
                                    </form>
                                <?php endif; ?>

                            </td>

                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($users->hasPages()): ?>
            <div class="d-flex justify-content-center mt-3">
                <?php echo e($users->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4')); ?>

            </div>
        <?php endif; ?>
    </div>
</div>



<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .user-row-highlighted {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3 !important;
        transition: all 0.3s ease;
    }
    
    .user-row-highlighted:hover {
        background-color: #bbdefb !important;
    }
    
    .user-checkbox:checked + td,
    .user-checkbox:checked ~ td {
        position: relative;
    }
    
    /* Ensure checkboxes are visible */
    .user-checkbox, #select-all-users {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 16px !important;
        height: 16px !important;
        margin: 0 auto !important;
    }
    
    /* Hide DataTables selection checkboxes if any */
    .dt-checkboxes-select-all, .dt-checkboxes-cell {
        display: none !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_delete')): ?>
  let deleteButtonTrans = '<?php echo e(trans('global.datatables.delete')); ?>'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "<?php echo e(route('admin.users.massDestroy')); ?>",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('<?php echo e(trans('global.datatables.zero_selected')); ?>')
        return
      }

      if (confirm('<?php echo e(trans('global.areYouSure')); ?>')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
<?php endif; ?>

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'desc' ]],
  });
  var table = $('.datatable-User:not(.ajaxTable)').DataTable({ 
    buttons: dtButtons,
    select: {
      style: 'multi',
      selector: 'td:first-child'
    },
    paging: false,       // Use Laravel paginator instead of DataTables paging
    searching: false,    // Use server-side filters/search if any
    info: false,        // Hide DataTables info summary
    lengthChange: false, // Hide page length selector
    columnDefs: [
      { targets: 0, orderable: false, searchable: false },
      { targets: 1, visible: true },
      { targets: 2, visible: true },
      { targets: 3, visible: true },
      { targets: 4, visible: true },
      { targets: 5, visible: true },
      { targets: 6, visible: true },
      { targets: 7, orderable: false, searchable: false }
    ]
  })
  
  // Initialize dropdown functionality
  $('#columnToggleBtn').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('Dropdown button clicked');
    $('#columnDropdown').toggle();
  });
  
  // Close dropdown when clicking outside
  $(document).on('click', function(e) {
    if (!$(e.target).closest('.dropdown').length) {
      $('#columnDropdown').hide();
    }
  });
  
  // Custom column visibility functionality
  $('.column-toggle').on('change', function() {
    var columnIndex = parseInt($(this).data('column'));
    var column = table.column(columnIndex);
    var isVisible = $(this).is(':checked');
    column.visible(isVisible);
    console.log('Column', columnIndex, 'visibility set to:', isVisible);
  });
  
  // Restore all columns
  $('#restoreColumns').on('click', function() {
    $('.column-toggle').prop('checked', true).trigger('change');
    console.log('All columns restored');
  });
  
  // Update checkboxes based on current column visibility
  setTimeout(function() {
    table.columns().every(function() {
      var column = this;
      var columnIndex = column.index();
      var checkbox = $('.column-toggle[data-column="' + columnIndex + '"]');
      if (checkbox.length) {
        checkbox.prop('checked', column.visible());
        console.log('Column', columnIndex, 'checkbox updated to:', column.visible());
      }
    });
  }, 100);
  
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });

  // Row highlighting on selection
  table.on('select', function(e, dt, type, indexes) {
    if (type === 'row') {
      dt.rows(indexes).nodes().to$().addClass('user-row-highlighted');
    }
  });

  table.on('deselect', function(e, dt, type, indexes) {
    if (type === 'row') {
      dt.rows(indexes).nodes().to$().removeClass('user-row-highlighted');
    }
  });
})

function changePerPage() {
    const perPage = document.getElementById('per-page-selector').value;
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/users/index.blade.php ENDPATH**/ ?>