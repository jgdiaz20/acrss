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
            <i class="fas fa-book"></i> Subjects Management
        </li>
    </ol>
</nav>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('subject_create')): ?>
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="<?php echo e(route("admin.subjects.create")); ?>">
                <i class="fas fa-plus"></i> Add Subject
            </a>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-book mr-2"></i>
            Subjects Management
        </h3>
        <div class="card-tools">
            <span class="badge badge-primary">Total: <?php echo e($subjects->total()); ?> subjects</span>
        </div>
    </div>

    <div class="card-body">
        <!-- Filters Section -->
        <div class="filters-section mb-3">
            <!-- Desktop Filters -->
            <div class="row d-none d-md-flex">
                <div class="col-md-2">
                    <select class="form-control form-control-sm" id="type-filter">
                        <option value="">All Types</option>
                        <?php $__currentLoopData = \App\Subject::SUBJECT_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('type') == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search-input" placeholder="Search subjects..." value="<?php echo e(request('search')); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="filterTable()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-sm btn-block" type="button" onclick="filterTable()">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="<?php echo e(route('admin.subjects.index')); ?>" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>

            <!-- Mobile Filters (Collapsible) -->
            <div class="d-md-none">
                <!-- Search Box (Always Visible on Mobile) -->
                <div class="mb-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search-input-mobile" placeholder="Search subjects..." value="<?php echo e(request('search')); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="filterTable()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Filters -->
                <button class="btn btn-outline-secondary btn-block mb-2" type="button" data-toggle="collapse" data-target="#mobileFilters" aria-expanded="false">
                    <i class="fas fa-filter"></i> Show Filters
                </button>
                <div class="collapse" id="mobileFilters">
                    <div class="card card-body">
                        <div class="form-group">
                            <label>Subject Type</label>
                            <select class="form-control form-control-sm" id="type-filter-mobile">
                                <option value="">All Types</option>
                                <?php $__currentLoopData = \App\Subject::SUBJECT_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e(request('type') == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button class="btn btn-primary btn-sm btn-block" onclick="filterTable()">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="<?php echo e(route('admin.subjects.index')); ?>" class="btn btn-secondary btn-sm btn-block">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
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
                    Showing <?php echo e($subjects->firstItem() ?? 0); ?> to <?php echo e($subjects->lastItem() ?? 0); ?> of <?php echo e($subjects->total()); ?> subjects
                </span>
            </div>
        </div>
        
        <!-- Subjects Table -->
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Subject">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Credits</th>
                        <th>Schedules</th>
                        <th>Teachers</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-entry-id="<?php echo e($subject->id); ?>">
                            <td>

                            </td>
                            <td><span class="badge badge-secondary"><?php echo e($subject->id); ?></span></td>
                            <td>
                                <?php echo e($subject->name); ?>

                                <?php echo $subject->mode_badge; ?>

                            </td>
                            <td><span class="badge badge-info"><?php echo e($subject->code); ?></span></td>
                            <td>
                                <span class="badge badge-<?php echo e($subject->type === 'core' ? 'primary' : 'secondary'); ?>">
                                    <?php echo e(\App\Subject::SUBJECT_TYPES[$subject->type]); ?>

                                </span>
                            </td>
                            <td>
                                <span data-toggle="tooltip" data-html="true" 
                                      title="<strong>Total:</strong> <?php echo e($subject->total_hours); ?>h<br><strong>Lecture:</strong> <?php echo e($subject->lecture_units); ?>u (<?php echo e($subject->total_lecture_hours); ?>h)<br><strong>Lab:</strong> <?php echo e($subject->lab_units); ?>u (<?php echo e($subject->total_lab_hours); ?>h)">
                                    <?php echo e($subject->credits); ?> 
                                </span>
                            </td>
                            <td><?php echo e($subject->lessons_count); ?></td>
                            <td><?php echo e($subject->teachers_count); ?></td>
                            <td>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('subject_show')): ?>
                                    <a class="btn btn-xs btn-primary" href="<?php echo e(route('admin.subjects.show', $subject->id)); ?>">
                                        View
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('subject_edit')): ?>
                                    <a class="btn btn-xs btn-info" href="<?php echo e(route('admin.subjects.edit', $subject->id)); ?>">
                                        Edit
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('subject_edit')): ?>
                                    <a class="btn btn-xs btn-warning" href="<?php echo e(route('admin.subjects.assign-teachers', $subject->id)); ?>">
                                        Teachers
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('subject_delete')): ?>
                                    <form action="<?php echo e(route('admin.subjects.destroy', $subject->id)); ?>" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                        <input type="submit" class="btn btn-xs btn-danger" value="Delete">
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No subjects found</h5>
                                    <p class="text-muted">
                                        <?php if(request()->hasAny(['type', 'search'])): ?>
                                            No subjects found matching your filters. Try adjusting your search criteria.
                                        <?php else: ?>
                                            No subjects have been created yet. Click "Add Subject" to create your first subject.
                                        <?php endif; ?>
                                    </p>
                                    <?php if(request()->hasAny(['type', 'search'])): ?>
                                        <a href="<?php echo e(route('admin.subjects.index')); ?>" class="btn btn-primary mt-2">
                                            </i> Clear All Filters
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($subjects->hasPages()): ?>
            <div class="d-flex justify-content-center mt-3">
            <?php echo e($subjects->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4')); ?>

            </div>
        <?php endif; ?>
</div>



<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .subject-row-highlighted {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3 !important;
        transition: all 0.3s ease;
    }
    
    .subject-row-highlighted:hover {
        background-color: #bbdefb !important;
    }
    
    /* Center selection handle */
    table th:first-child,
    table td:first-child {
        text-align: center;
        vertical-align: middle;
        width: 24px;
        padding: 8px 4px;
    }
    
    /* Ensure proper spacing */
    table th:first-child {
        border-right: 1px solid #dee2e6;
    }
    
    table td:first-child {
        border-right: 1px solid #dee2e6;
    }

    
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(function () {
  // Show success message from previous AJAX action (if any)
  const priorSuccess = localStorage.getItem('flash_success');
  if (priorSuccess) {
      const alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">'
          + priorSuccess
          + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
          + '<span aria-hidden="true">&times;</span>'
          + '</button>'
          + '</div>';
      $('.card').first().before(alertHtml);
      localStorage.removeItem('flash_success');
  }
  // Pagination
  //pagination
    function changePerPage() {
    const perPage = document.getElementById('per-page-selector').value;
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
    }
  // Initialize DataTable with select-checkbox first column
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('subject_delete')): ?>
  let deleteButton = {
    text: 'Delete Selected',
    url: "<?php echo e(route('admin.subjects.massDestroy')); ?>",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('No rows selected')
        return
      }

      if (confirm('Are you sure you want to delete the selected ' + ids.length + ' subjects?')) {
        $.ajax({
          headers: {'x-csrf-token': $('meta[name="csrf-token"]').attr('content')},
          method: 'POST',
          url: config.url,
          dataType: 'json',
          data: { ids: ids, _method: 'DELETE' }})
          .done(function (response) { 
              if (response && response.message) {
                  localStorage.setItem('flash_success', response.message);
              } else {
                  localStorage.setItem('flash_success', 'Selected subjects have been successfully deleted!');
              }
              location.reload();
          })
          .fail(function (jqXHR) {
              let errorMsg = 'An error occurred while deleting the subjects.';
              if (jqXHR.responseJSON && (jqXHR.responseJSON.error || jqXHR.responseJSON.message)) {
                  errorMsg = jqXHR.responseJSON.error || jqXHR.responseJSON.message;
              }
              alert(errorMsg);
          });
      }
    }
  }
  dtButtons.push(deleteButton)
  <?php endif; ?>

  let table = $('.datatable-Subject').DataTable({
    buttons: dtButtons,
    order: [[ 1, 'asc' ]],
    pageLength: 20,
    columnDefs: [
      { orderable: false, className: 'select-checkbox', targets: 0, defaultContent: '' }
    ],
    select: {
      style: 'multi',
      selector: 'td.select-checkbox'
    },
    paging: false,       // Use Laravel paginator
    searching: false,    // Server-side filters above
    info: false,
    lengthChange: false
  });

  // Wire external mass-delete button to DataTable action for consistency
  $('#subjects-mass-delete-btn').on('click', function() {
    let button = table.button(table.buttons().length - 1); // last pushed button
    if (button) button.trigger();
  });
});

function filterTable() {
    const isMobile = window.innerWidth < 768;
    const typeFilter = isMobile ? document.getElementById('type-filter-mobile').value : document.getElementById('type-filter').value;
    const searchInput = isMobile ? document.getElementById('search-input-mobile').value : document.getElementById('search-input').value;
    
    const url = new URL(window.location);
    
    // Type filter
    if (typeFilter) {
        url.searchParams.set('type', typeFilter);
    } else {
        url.searchParams.delete('type');
    }
    
    // Search
    if (searchInput) {
        url.searchParams.set('search', searchInput);
    } else {
        url.searchParams.delete('search');
    }
    
    window.location.href = url.toString();
}

// Allow Enter key in search inputs
document.addEventListener('DOMContentLoaded', function() {
    const searchInputs = document.querySelectorAll('#search-input, #search-input-mobile');
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.which === 13 || e.keyCode === 13) {
                e.preventDefault();
                applySearch();
            }
        });
    });

    // Sync mobile and desktop filters
    const typeFilter = document.getElementById('type-filter');
    const typeFilterMobile = document.getElementById('type-filter-mobile');
    if (typeFilter && typeFilterMobile) {
        typeFilter.addEventListener('change', function() {
            typeFilterMobile.value = this.value;
        });
    }

});

function massDestroy() {
    const selectedIds = Array.from(document.querySelectorAll('input[name="ids[]"]:checked')).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one subject to delete.');
        return;
    }
    
    if (confirm('Are you sure you want to delete the selected subjects?')) {
        // Create URLSearchParams for form data (matching DataTables pattern)
        const formData = new URLSearchParams();
        formData.append('_token', '<?php echo e(csrf_token()); ?>');
        formData.append('_method', 'DELETE');
        selectedIds.forEach(id => {
            formData.append('ids[]', id);
        });
        
        // Make AJAX request using fetch with POST method (like DataTables)
        fetch('<?php echo e(route("admin.subjects.massDestroy")); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.json().then(errorData => {
                    throw new Error(JSON.stringify(errorData));
                });
            }
        })
        .then(data => {
            // Success - show message and reload page
            alert(data.message || 'Subjects deleted successfully!');
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            try {
                const errorData = JSON.parse(error.message);
                let errorMessage = errorData.message || 'An error occurred while deleting subjects.';
                
                if (errorData.errors && errorData.errors.length > 0) {
                    errorMessage += '\n\nDetails:\n' + errorData.errors.join('\n');
                }
                
                alert(errorMessage);
            } catch (parseError) {
                alert('An error occurred while deleting subjects. Please try again.');
            }
        });
    }
}

document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
        toggleRowHighlight(cb);
    });
});

// Add event listeners to all subject checkboxes for highlighting
document.addEventListener('DOMContentLoaded', function() {
    const subjectCheckboxes = document.querySelectorAll('.subject-checkbox');
    subjectCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleRowHighlight(this);
        });
        
        // Initialize highlighting for already checked boxes
        toggleRowHighlight(checkbox);
    });

    // Initialize tooltips for credit breakdown
    $('[data-toggle="tooltip"]').tooltip();
});

function toggleRowHighlight(checkbox) {
    const row = checkbox.closest('tr');
    if (checkbox.checked) {
        row.classList.add('subject-row-highlighted');
    } else {
        row.classList.remove('subject-row-highlighted');
    }
}

function changePerPage() {
    const perPage = document.getElementById('per-page-selector').value;
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/subjects/index.blade.php ENDPATH**/ ?>