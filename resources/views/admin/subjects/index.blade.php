@extends('layouts.admin')
@section('content')

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.home') }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-book"></i> Subjects Management
        </li>
    </ol>
</nav>

@can('subject_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.subjects.create") }}">
                <i class="fas fa-plus"></i> Add Subject
            </a>
        </div>
    </div>
@endcan

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-book mr-2"></i>
            Subjects Management
        </h3>
        <div class="card-tools">
            <span class="badge badge-primary">Total: {{ $subjects->total() }} subjects</span>
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
                        @foreach(\App\Subject::SUBJECT_TYPES as $key => $type)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control form-control-sm" id="lab-filter">
                        <option value="">Lab/Non-Lab</option>
                        <option value="1" {{ request('lab') == '1' ? 'selected' : '' }}>Lab Required</option>
                        <option value="0" {{ request('lab') == '0' ? 'selected' : '' }}>No Lab</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control form-control-sm" id="equipment-filter">
                        <option value="">Equipment</option>
                        <option value="1" {{ request('equipment') == '1' ? 'selected' : '' }}>Equipment Required</option>
                        <option value="0" {{ request('equipment') == '0' ? 'selected' : '' }}>No Equipment</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control form-control-sm" id="status-filter">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search-input" placeholder="Search subjects..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="filterTable()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary btn-sm btn-block" type="button" onclick="filterTable()">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>

            <!-- Mobile Filters (Collapsible) -->
            <div class="d-md-none">
                <!-- Search Box (Always Visible on Mobile) -->
                <div class="mb-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search-input-mobile" placeholder="Search subjects..." value="{{ request('search') }}">
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
                                @foreach(\App\Subject::SUBJECT_TYPES as $key => $type)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lab Requirement</label>
                            <select class="form-control form-control-sm" id="lab-filter-mobile">
                                <option value="">All Subjects</option>
                                <option value="1" {{ request('lab') == '1' ? 'selected' : '' }}>Lab Required</option>
                                <option value="0" {{ request('lab') == '0' ? 'selected' : '' }}>No Lab Required</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Equipment Requirement</label>
                            <select class="form-control form-control-sm" id="equipment-filter-mobile">
                                <option value="">All Subjects</option>
                                <option value="1" {{ request('equipment') == '1' ? 'selected' : '' }}>Equipment Required</option>
                                <option value="0" {{ request('equipment') == '0' ? 'selected' : '' }}>No Equipment Required</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control form-control-sm" id="status-filter-mobile">
                                <option value="">All Status</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <button class="btn btn-primary btn-sm btn-block" onclick="filterTable()">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-sm btn-block">
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
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="ml-2 text-muted">entries per page</span>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <span class="text-muted">
                    Showing {{ $subjects->firstItem() ?? 0 }} to {{ $subjects->lastItem() ?? 0 }} of {{ $subjects->total() }} subjects
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
                        <th>Requirements</th>
                        <th>Lessons</th>
                        <th>Teachers</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr data-entry-id="{{ $subject->id }}">
                            <td>

                            </td>
                            <td><span class="badge badge-secondary">{{ $subject->id }}</span></td>
                            <td>{{ $subject->name }}</td>
                            <td><span class="badge badge-info">{{ $subject->code }}</span></td>
                            <td>
                                <span class="badge badge-{{ $subject->type === 'core' ? 'primary' : 'secondary' }}">
                                    {{ \App\Subject::SUBJECT_TYPES[$subject->type] }}
                                </span>
                            </td>
                            <td>{{ $subject->credits }}</td>
                            <td>
                                @if($subject->requires_lab)
                                    <span class="badge badge-warning">Lab</span>
                                @endif
                                @if($subject->requires_equipment)
                                    <span class="badge badge-info">Equipment</span>
                                @endif
                            </td>
                            <td>{{ $subject->lessons_count }}</td>
                            <td>{{ $subject->teachers_count }}</td>
                            <td>
                                <span class="badge badge-{{ $subject->is_active ? 'success' : 'danger' }}">
                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @can('subject_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.subjects.show', $subject->id) }}">
                                        View
                                    </a>
                                @endcan
                                @can('subject_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.subjects.edit', $subject->id) }}">
                                        Edit
                                    </a>
                                @endcan
                                @can('subject_edit')
                                    <a class="btn btn-xs btn-warning" href="{{ route('admin.subjects.assign-teachers', $subject->id) }}">
                                        Teachers
                                    </a>
                                @endcan
                                @can('subject_delete')
                                    <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="Delete">
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No subjects found</h5>
                                    <p class="text-muted">
                                        @if(request()->hasAny(['type', 'lab', 'equipment', 'is_active', 'search']))
                                            No subjects found matching your filters. Try adjusting your search criteria.
                                        @else
                                            No subjects have been created yet. Click "Add Subject" to create your first subject.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['type', 'lab', 'equipment', 'is_active', 'search']))
                                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-primary mt-2">
                                            </i> Clear All Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $subjects->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
</div>



@endsection

@section('styles')
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

    /* Pagination uses Bootstrap 4 template; no overrides needed */
</style>
@endsection

@section('scripts')
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

  // Initialize DataTable with select-checkbox first column
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

  @can('subject_delete')
  let deleteButton = {
    text: 'Delete Selected',
    url: "{{ route('admin.subjects.massDestroy') }}",
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
  @endcan

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
    const labFilter = isMobile ? document.getElementById('lab-filter-mobile').value : document.getElementById('lab-filter').value;
    const equipmentFilter = isMobile ? document.getElementById('equipment-filter-mobile').value : document.getElementById('equipment-filter').value;
    const statusFilter = isMobile ? document.getElementById('status-filter-mobile').value : document.getElementById('status-filter').value;
    const searchInput = isMobile ? document.getElementById('search-input-mobile').value : document.getElementById('search-input').value;
    
    const url = new URL(window.location);
    
    // Type filter
    if (typeFilter) {
        url.searchParams.set('type', typeFilter);
    } else {
        url.searchParams.delete('type');
    }
    
    // Lab filter
    if (labFilter) {
        url.searchParams.set('lab', labFilter);
    } else {
        url.searchParams.delete('lab');
    }
    
    // Equipment filter
    if (equipmentFilter) {
        url.searchParams.set('equipment', equipmentFilter);
    } else {
        url.searchParams.delete('equipment');
    }
    
    // Status filter
    if (statusFilter) {
        url.searchParams.set('is_active', statusFilter);
    } else {
        url.searchParams.delete('is_active');
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

    const labFilter = document.getElementById('lab-filter');
    const labFilterMobile = document.getElementById('lab-filter-mobile');
    if (labFilter && labFilterMobile) {
        labFilter.addEventListener('change', function() {
            labFilterMobile.value = this.value;
        });
    }

    const equipmentFilter = document.getElementById('equipment-filter');
    const equipmentFilterMobile = document.getElementById('equipment-filter-mobile');
    if (equipmentFilter && equipmentFilterMobile) {
        equipmentFilter.addEventListener('change', function() {
            equipmentFilterMobile.value = this.value;
        });
    }

    const statusFilter = document.getElementById('status-filter');
    const statusFilterMobile = document.getElementById('status-filter-mobile');
    if (statusFilter && statusFilterMobile) {
        statusFilter.addEventListener('change', function() {
            statusFilterMobile.value = this.value;
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
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        selectedIds.forEach(id => {
            formData.append('ids[]', id);
        });
        
        // Make AJAX request using fetch with POST method (like DataTables)
        fetch('{{ route("admin.subjects.massDestroy") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
@endsection
