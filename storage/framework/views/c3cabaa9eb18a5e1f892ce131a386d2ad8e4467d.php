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
            <i class="fas fa-clock"></i> Lessons Management
        </li>
    </ol>
</nav>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('lesson_create')): ?>
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="<?php echo e(route("admin.lessons.create")); ?>">
                <i class="fas fa-plus"></i> Add Class Schedule
            </a>
        </div>
    </div>
<?php endif; ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clock mr-2"></i>
            <?php echo e(trans('cruds.lesson.title_singular')); ?> <?php echo e(trans('global.list')); ?>

        </h3>
        <div class="card-tools">
            <span class="badge badge-primary mr-2">Total: <?php echo e($lessons->total()); ?> lessons</span>
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
                            <label class="form-check-label">Class</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="3" type="checkbox" checked>
                            <label class="form-check-label">Teacher</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="4" type="checkbox" checked>
                            <label class="form-check-label">Subject</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="5" type="checkbox" checked>
                            <label class="form-check-label">Room</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="6" type="checkbox" checked>
                            <label class="form-check-label">Weekday</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="7" type="checkbox" checked>
                            <label class="form-check-label">Start Time</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="8" type="checkbox" checked>
                            <label class="form-check-label">End Time</label>
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
        <!-- Filters Section -->
        <div class="filters-section mb-3">
            <!-- Desktop Filters -->
            <div class="row d-none d-md-flex">
                <div class="col-md-2">
                    <select class="form-control form-control-sm" id="class-filter">
                        <option value="">All Classes</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>" <?php echo e((isset($filters['class_id']) && $filters['class_id'] == $id) ? 'selected' : ''); ?>>
                                <?php echo e($name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control form-control-sm" id="teacher-filter">
                        <option value="">All Teachers</option>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>" <?php echo e((isset($filters['teacher_id']) && $filters['teacher_id'] == $id) ? 'selected' : ''); ?>>
                                <?php echo e($name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control form-control-sm" id="subject-filter">
                        <option value="">All Subjects</option>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>" <?php echo e((isset($filters['subject_id']) && $filters['subject_id'] == $id) ? 'selected' : ''); ?>>
                                <?php echo e($name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control form-control-sm" id="weekday-filter">
                        <option value="">All Weekdays</option>
                        <?php $__currentLoopData = \App\Lesson::WEEK_DAYS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e((isset($filters['weekday']) && $filters['weekday'] == $key) ? 'selected' : ''); ?>>
                                <?php echo e($day); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search-input" placeholder="Search lessons..." value="<?php echo e($filters['search'] ?? ''); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary btn-sm btn-block" type="button" onclick="applyFilters()">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="<?php echo e(route('admin.lessons.index', ['clear_filters' => 1])); ?>" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>

            <!-- Mobile Filters (Collapsible) -->
            <div class="d-md-none">
                <!-- Search Box (Always Visible on Mobile) -->
                <div class="mb-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search-input-mobile" placeholder="Search lessons..." value="<?php echo e($filters['search'] ?? ''); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
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
                            <label>Class</label>
                            <select class="form-control form-control-sm" id="class-filter-mobile">
                                <option value="">All Classes</option>
                                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($id); ?>" <?php echo e((isset($filters['class_id']) && $filters['class_id'] == $id) ? 'selected' : ''); ?>>
                                        <?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Teacher</label>
                            <select class="form-control form-control-sm" id="teacher-filter-mobile">
                                <option value="">All Teachers</option>
                                <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($id); ?>" <?php echo e((isset($filters['teacher_id']) && $filters['teacher_id'] == $id) ? 'selected' : ''); ?>>
                                        <?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <select class="form-control form-control-sm" id="subject-filter-mobile">
                                <option value="">All Subjects</option>
                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($id); ?>" <?php echo e((isset($filters['subject_id']) && $filters['subject_id'] == $id) ? 'selected' : ''); ?>>
                                        <?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Weekday</label>
                            <select class="form-control form-control-sm" id="weekday-filter-mobile">
                                <option value="">All Weekdays</option>
                                <?php $__currentLoopData = \App\Lesson::WEEK_DAYS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e((isset($filters['weekday']) && $filters['weekday'] == $key) ? 'selected' : ''); ?>>
                                        <?php echo e($day); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button class="btn btn-primary btn-sm btn-block" onclick="applyFilters()">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="<?php echo e(route('admin.lessons.index', ['clear_filters' => 1])); ?>" class="btn btn-secondary btn-sm btn-block">
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
                        <option value="10" <?php echo e($perPage == 10 ? 'selected' : ''); ?>>10</option>
                        <option value="20" <?php echo e($perPage == 20 ? 'selected' : ''); ?>>20</option>
                        <option value="50" <?php echo e($perPage == 50 ? 'selected' : ''); ?>>50</option>
                        <option value="100" <?php echo e($perPage == 100 ? 'selected' : ''); ?>>100</option>
                    </select>
                    <span class="ml-2 text-muted">entries per page</span>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <span class="text-muted">
                    Showing <?php echo e($lessons->firstItem() ?? 0); ?> to <?php echo e($lessons->lastItem() ?? 0); ?> of <?php echo e($lessons->total()); ?> lessons
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.id')); ?>

                        </th>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.class')); ?>

                        </th>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.teacher')); ?>

                        </th>
                        <th>
                            Subject
                        </th>
                        <th>
                            Room
                        </th>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.weekday')); ?>

                        </th>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.start_time')); ?>

                        </th>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.end_time')); ?>

                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-entry-id="<?php echo e($lesson->id); ?>" data-lesson-id="<?php echo e($lesson->id); ?>">
                            <td>

                            </td>
                            <td>
                                <?php echo e($lesson->id ?? ''); ?>

                            </td>
                            <td>
                                <?php echo e($lesson->class->name ?? ''); ?>

                            </td>
                            <td>
                                <?php echo e($lesson->teacher->name ?? ''); ?>

                            </td>
                            <td>
                                <?php echo e($lesson->subject->name ?? 'No Subject'); ?>

                            </td>
                            <td>
                                <?php echo e($lesson->room->display_name ?? ''); ?>

                            </td>
                            <td>
                                <?php echo e(\App\Lesson::WEEK_DAYS[$lesson->weekday] ?? ''); ?>

                            </td>
                            <td>
                                <?php echo e($lesson->start_time ? \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') : ''); ?>

                            </td>
                            <td>
                                <?php echo e($lesson->end_time ? \Carbon\Carbon::parse($lesson->end_time)->format('g:i A') : ''); ?>

                            </td>
                            <td>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('lesson_show')): ?>
                                    <a class="btn btn-xs btn-primary" href="<?php echo e(route('admin.lessons.show', $lesson->id)); ?>">
                                        <?php echo e(trans('global.view')); ?>

                                    </a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('lesson_edit')): ?>
                                    <a class="btn btn-xs btn-info" href="<?php echo e(route('admin.lessons.edit', $lesson->id)); ?>">
                                        <?php echo e(trans('global.edit')); ?>

                                    </a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('lesson_delete')): ?>
                                    <form action="<?php echo e(route('admin.lessons.destroy', $lesson)); ?>" method="POST" class="delete-lesson-form" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                        <button type="button"
                                            class="btn btn-xs btn-danger trigger-delete-modal"
                                            data-lesson-id="<?php echo e($lesson->id); ?>"
                                            data-class="<?php echo e($lesson->class->name ?? 'No Class'); ?>"
                                            data-teacher="<?php echo e($lesson->teacher->name ?? 'No Teacher'); ?>"
                                            data-subject="<?php echo e($lesson->subject->name ?? 'No Subject'); ?>"
                                            data-room="<?php echo e($lesson->room->display_name ?? ($lesson->room->name ?? 'No Room')); ?>"
                                            data-weekday="<?php echo e(\App\Lesson::WEEK_DAYS[$lesson->weekday] ?? ''); ?>"
                                            data-start="<?php echo e($lesson->start_time ? \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') : ''); ?>"
                                            data-end="<?php echo e($lesson->end_time ? \Carbon\Carbon::parse($lesson->end_time)->format('g:i A') : ''); ?>">
                                            <?php echo e(trans('global.delete')); ?>

                                        </button>
                                    </form>
                                <?php endif; ?>

                            </td>

                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No lessons found</h5>
                                    <p class="text-muted">
                                        <?php if(!empty(array_filter($filters ?? []))): ?>
                                            No lessons found matching your filters. Try adjusting your search criteria.
                                        <?php else: ?>
                                            No lessons have been created yet. Click "Add Class Schedule" to create your first lesson.
                                        <?php endif; ?>
                                    </p>
                                    <?php if(!empty(array_filter($filters ?? []))): ?>
                                        <a href="<?php echo e(route('admin.lessons.index', ['clear_filters' => 1])); ?>" class="btn btn-primary mt-2">
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
        
        <!-- Pagination -->
        <?php if($lessons->hasPages()): ?>
            <div class="d-flex justify-content-center mt-3">
            <?php echo e($lessons->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4')); ?>

            </div>
        <?php endif; ?>
    </div>
</div>



<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .lesson-row-highlighted {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3 !important;
        transition: all 0.3s ease;
    }
    
    .lesson-row-highlighted:hover {
        background-color: #bbdefb !important;
    }
    
    .lesson-checkbox:checked + td,
    .lesson-checkbox:checked ~ td {
        position: relative;
    }
    
    /* Ensure checkboxes are visible */
    .lesson-checkbox, #select-all-lessons {
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
    //pagination
    function changePerPage() {
    const perPage = document.getElementById('per-page-selector').value;
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}
    // Filter functions
    function applyFilters() {
        const isMobile = window.innerWidth < 768;
        const classId = isMobile ? $('#class-filter-mobile').val() : $('#class-filter').val();
        const teacherId = isMobile ? $('#teacher-filter-mobile').val() : $('#teacher-filter').val();
        const subjectId = isMobile ? $('#subject-filter-mobile').val() : $('#subject-filter').val();
        const weekday = isMobile ? $('#weekday-filter-mobile').val() : $('#weekday-filter').val();
        const search = isMobile ? $('#search-input-mobile').val() : $('#search-input').val();
        const perPage = $('#per-page-selector').val();

        const params = new URLSearchParams();
        if (classId) params.append('class_id', classId);
        if (teacherId) params.append('teacher_id', teacherId);
        if (subjectId) params.append('subject_id', subjectId);
        if (weekday) params.append('weekday', weekday);
        if (search) params.append('search', search);
        if (perPage) params.append('per_page', perPage);

        window.location.href = '<?php echo e(route("admin.lessons.index")); ?>?' + params.toString();
    }

    function changePerPage() {
        applyFilters();
    }

    // Allow Enter key in search inputs
    $('#search-input, #search-input-mobile').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            applyFilters();
        }
    });

    // Sync mobile and desktop filters
    $('#class-filter').on('change', function() {
        $('#class-filter-mobile').val($(this).val());
    });
    $('#teacher-filter').on('change', function() {
        $('#teacher-filter-mobile').val($(this).val());
    });
    $('#subject-filter').on('change', function() {
        $('#subject-filter-mobile').val($(this).val());
    });
    $('#weekday-filter').on('change', function() {
        $('#weekday-filter-mobile').val($(this).val());
    });

    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('lesson_delete')): ?>
  let deleteButtonTrans = '<?php echo e(trans('global.datatables.delete')); ?>'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "<?php echo e(route('admin.lessons.massDestroy')); ?>",
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
    pageLength: 100,
  });
  var table = $('.datatable-Lesson:not(.ajaxTable)').DataTable({ 
    buttons: dtButtons,
    select: {
      style: 'multi',
      selector: 'td.select-checkbox'
    },
    columnDefs: [
      { targets: 0, orderable: false, searchable: false, className: 'select-checkbox', defaultContent: '' }
    ],
    paging: false,       // Use Laravel paginator instead of DataTables paging
    searching: false,    // Use server-side filters/search if any
    info: false,         // Hide DataTables info summary
    lengthChange: false  // Hide page length selector
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
      dt.rows(indexes).nodes().to$().addClass('lesson-row-highlighted');
    }
  });

  table.on('deselect', function(e, dt, type, indexes) {
    if (type === 'row') {
      dt.rows(indexes).nodes().to$().removeClass('lesson-row-highlighted');
    }
  });

  // Delete confirmation modal logic
  $(document).on('click', '.trigger-delete-modal', function() {
    const $btn = $(this);
    const $form = $btn.closest('form.delete-lesson-form');
    const details = {
      id: $btn.data('lesson-id'),
      className: $btn.data('class'),
      teacher: $btn.data('teacher'),
      subject: $btn.data('subject'),
      room: $btn.data('room'),
      weekday: $btn.data('weekday'),
      start: $btn.data('start'),
      end: $btn.data('end')
    };

    // Populate modal
    $('#deleteLessonId').text(details.id);
    $('#deleteLessonClass').text(details.className);
    $('#deleteLessonTeacher').text(details.teacher);
    $('#deleteLessonSubject').text(details.subject);
    $('#deleteLessonRoom').text(details.room);
    $('#deleteLessonWhen').text(details.weekday + ' ' + details.start + ' - ' + details.end);

    // Bind confirm action
    $('#confirmDeleteLesson').off('click').on('click', function() {
      $form.trigger('submit');
    });

    $('#deleteLessonModal').modal('show');
  });
})

</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteLessonModal" tabindex="-1" role="dialog" aria-labelledby="deleteLessonModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteLessonModalLabel"><i class="fas fa-trash-alt mr-1"></i> Confirm Delete Class Schedule</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>You're about to delete the following class schedule:</p>
        <ul class="list-unstyled mb-0">
          <li><strong>ID:</strong> <span id="deleteLessonId"></span></li>
          <li><strong>Class:</strong> <span id="deleteLessonClass"></span></li>
          <li><strong>Teacher:</strong> <span id="deleteLessonTeacher"></span></li>
          <li><strong>Subject:</strong> <span id="deleteLessonSubject"></span></li>
          <li><strong>Room:</strong> <span id="deleteLessonRoom"></span></li>
          <li><strong>When:</strong> <span id="deleteLessonWhen"></span></li>
        </ul>
        <div class="alert alert-warning mt-3">
          <i class="fas fa-exclamation-triangle mr-1"></i> This action cannot be undone.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteLesson"><i class="fas fa-trash-alt"></i> Delete</button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/lessons/index.blade.php ENDPATH**/ ?>