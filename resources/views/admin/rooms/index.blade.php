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
            <i class="fas fa-door-open"></i> Rooms Management
        </li>
    </ol>
</nav>

@can('room_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.room-management.rooms.create") }}">
                <i class="fas fa-plus"></i> Add Room
            </a>
            <button onclick="showAllQRCodes()" class="btn btn-info ml-2">
                <i class="fas fa-qrcode mr-1"></i> All QR Codes
            </button>
        </div>
    </div>
@endcan

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-door-open mr-2"></i>
            Room Management
        </h3>
        <div class="card-tools">
            <span class="badge badge-primary mr-2">Total: {{ $rooms->count() }} rooms</span>
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
                            <label class="form-check-label">Description</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="4" type="checkbox" checked>
                            <label class="form-check-label">Capacity</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" data-column="5" type="checkbox" checked>
                            <label class="form-check-label">Type</label>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="form-check">
                                                        <input class="form-check-input column-toggle" data-column="6" type="checkbox" checked>
                            <label class="form-check-label">Timetable</label>
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
                    <select class="form-control form-control-sm" id="type-filter">
                        <option value="">All Types</option>
                        <option value="lab" {{ request('type') == 'lab' ? 'selected' : '' }}>Laboratory</option>
                        <option value="classroom" {{ request('type') == 'classroom' ? 'selected' : '' }}>Classroom</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control form-control-sm" id="capacity-min" placeholder="Min" value="{{ request('capacity_min') }}">
                </div>
                <div class="col-md-1">
                    <input type="number" class="form-control form-control-sm" id="capacity-max" placeholder="Max" value="{{ request('capacity_max') }}">
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search-input" placeholder="Search rooms..." value="{{ request('search') }}">
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
                    <a href="{{ route('admin.room-management.rooms.index') }}" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>

            <!-- Mobile Filters (Collapsible) -->
            <div class="d-md-none">
                <!-- Search Box (Always Visible on Mobile) -->
                <div class="mb-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search-input-mobile" placeholder="Search rooms..." value="{{ request('search') }}">
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
                            <label>Room Type</label>
                            <select class="form-control form-control-sm" id="type-filter-mobile">
                                <option value="">All Types</option>
                                <option value="lab" {{ request('type') == 'lab' ? 'selected' : '' }}>Laboratory</option>
                                <option value="classroom" {{ request('type') == 'classroom' ? 'selected' : '' }}>Classroom</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Capacity Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" id="capacity-min-mobile" placeholder="Min" value="{{ request('capacity_min') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" id="capacity-max-mobile" placeholder="Max" value="{{ request('capacity_max') }}">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm btn-block" onclick="applyFilters()">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.room-management.rooms.index') }}" class="btn btn-secondary btn-sm btn-block">
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
                    Showing {{ $rooms->firstItem() ?? 0 }} to {{ $rooms->lastItem() ?? 0 }} of {{ $rooms->total() }} rooms
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Room">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            ID
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Description
                        </th>
                        <th>
                            Capacity
                        </th>
                        <th>
                            Type
                        </th>
                        <th>
                            Timetable
                        </th>
                        
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $key => $room)
                        <tr data-entry-id="{{ $room->id }}" data-room-id="{{ $room->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $room->id ?? '' }}
                            </td>
                            <td>
                                {{ $room->name ?? '' }}
                            </td>
                            <td>
                                {{ $room->description ?? '' }}
                            </td>
                            <td>
                                {{ $room->capacity ?? 'N/A' }}
                            </td>
                            <td>
                                @if($room->is_lab)
                                    <span class="badge badge-warning">
                                        <i class="fas fa-flask"></i> Laboratory
                                    </span>
                                @else
                                    <span class="badge badge-primary">
                                        <i class="fas fa-chalkboard"></i> Classroom
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.room-management.room-timetables.show', $room->id) }}" class="btn btn-sm btn-info">
                                    View Timetable
                                </a>
                            </td>
                            <td>
                                @can('room_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.room-management.rooms.show', $room->id) }}">
                                        View
                                    </a>
                                @endcan

                                @can('room_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.room-management.rooms.edit', $room->id) }}">
                                        Edit
                                    </a>
                                @endcan

                                @can('room_show')
                                    <button onclick="showQRCode({{ $room->id }}, '{{ $room->name }}')" class="btn btn-xs btn-outline-success" title="View QR Code">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                @endcan

                                @can('room_delete')
                                    <form action="{{ route('admin.room-management.rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="Delete">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No rooms found</h5>
                                    <p class="text-muted">
                                        @if(request()->hasAny(['type', 'capacity_min', 'capacity_max', 'search']))
                                            No rooms found matching your filters. Try adjusting your search criteria.
                                        @else
                                            No rooms have been created yet. Click "Add Room" to create your first room.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['type', 'capacity_min', 'capacity_max', 'search']))
                                        <a href="{{ route('admin.room-management.rooms.index') }}" class="btn btn-primary mt-2">
                                             Clear All Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($rooms->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $rooms->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>



@endsection

@section('styles')
<style>
    /* These styles are safe and provide good UX for row selection. */
    .room-row-highlighted {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3 !important;
        transition: all 0.3s ease;
    }
    
    .room-row-highlighted:hover {
        background-color: #bbdefb !important;
    }
    
</style>
@endsection

@section('scripts')
@parent
<script>
    $(function () {
  // Display success message from previous AJAX action (if any)
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
  // Get the default buttons from the global config
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

  // --- OVERRIDE 'SELECT ALL' AND 'SELECT NONE' BEHAVIOR ---
  let selectAllButton = dtButtons.find(btn => btn.extend === 'selectAll');
  if (selectAllButton) {
      selectAllButton.action = function (e, dt) {
          dt.rows({ search: 'applied' }).select(); 
      };
  }
  let selectNoneButton = dtButtons.find(btn => btn.extend === 'selectNone');
  if (selectNoneButton) {
      selectNoneButton.action = function (e, dt) {
          dt.rows().deselect();
      };
  }

@can('room_delete')
  let deleteButtonTrans = 'Delete Selected'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.room-management.rooms.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('No rows selected')
        return
      }

      if (confirm('Are you sure you want to delete the selected ' + ids.length + ' rooms?')) {
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
                  localStorage.setItem('flash_success', 'Selected rooms have been successfully deleted!');
              }
              location.reload();
          })
          // --- THE FIX IS HERE ---
          // Add a .fail() block to catch and display server errors
          .fail(function (jqXHR, textStatus, errorThrown) {
              // Log the full error to the console for debugging
              console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
              
              // Default error message
              let errorMsg = 'An error occurred while deleting the rooms. Please check the console for details.';
              
              // Try to parse a more specific error from the server's JSON response
              if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                  errorMsg = jqXHR.responseJSON.error;
              }
              
              // Display the error message to the user
              alert(errorMsg);
          });
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  // Initialize the DataTable
  let table = $('.datatable-Room').DataTable({
    buttons: dtButtons,
    order: [[ 1, 'desc' ]], 
    paging: false,       // Use Laravel paginator instead of DataTables paging
    searching: false,    // Disable DataTables default search
    info: false,        // Hide DataTables info summary
    lengthChange: false, // Hide page length selector
    columnDefs: [
        { orderable: false, className: 'select-checkbox', targets: 0, defaultContent: '' }
    ],
    select: {
        style: 'multi',
        selector: 'td.select-checkbox'
    },
  });

  // Initialize dropdown functionality
  $('#columnToggleBtn').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
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
    let columnIndex = parseInt($(this).data('column'));
    let column = table.column(columnIndex);
    let isVisible = $(this).is(':checked');
    column.visible(isVisible);
  });
  
  // Restore all columns
  $('#restoreColumns').on('click', function() {
    $('.column-toggle').prop('checked', true).trigger('change');
  });
  
  // Update checkboxes based on current column visibility
  setTimeout(function() {
    table.columns().every(function() {
      let column = this;
      let columnIndex = column.index();
      let checkbox = $('.column-toggle[data-column="' + columnIndex + '"]');
      if (checkbox.length) {
        checkbox.prop('checked', column.visible());
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
      dt.rows(indexes).nodes().to$().addClass('room-row-highlighted');
    }
  });

  table.on('deselect', function(e, dt, type, indexes) {
    if (type === 'row') {
      dt.rows(indexes).nodes().to$().removeClass('room-row-highlighted');
    }
  });
});

function applyFilters() {
    const isMobile = window.innerWidth < 768;
    const typeFilter = isMobile ? document.getElementById('type-filter-mobile').value : document.getElementById('type-filter').value;
    const capacityMin = isMobile ? document.getElementById('capacity-min-mobile').value : document.getElementById('capacity-min').value;
    const capacityMax = isMobile ? document.getElementById('capacity-max-mobile').value : document.getElementById('capacity-max').value;
    const searchInput = isMobile ? document.getElementById('search-input-mobile').value : document.getElementById('search-input').value;
    
    const url = new URL(window.location);
    
    // Type filter
    if (typeFilter) {
        url.searchParams.set('type', typeFilter);
    } else {
        url.searchParams.delete('type');
    }
    
    
    // Capacity min
    if (capacityMin) {
        url.searchParams.set('capacity_min', capacityMin);
    } else {
        url.searchParams.delete('capacity_min');
    }
    
    // Capacity max
    if (capacityMax) {
        url.searchParams.set('capacity_max', capacityMax);
    } else {
        url.searchParams.delete('capacity_max');
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
                applyFilters();
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

function changePerPage() {
    const perPage = document.getElementById('per-page-selector').value;
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}

</script>
@endsection

@include('partials.qr-code-modal')

