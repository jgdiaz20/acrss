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
            <i class="fas fa-school"></i> School Classes
        </li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-school mr-2"></i>
                    {{ trans('cruds.schoolClass.title') }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-secondary">{{ $academicPrograms->count() }} Programs</span>
                </div>
            </div>
            
            <!-- Program Filter Section -->
            <div class="filter-section">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="filter-controls">
                            <label class="filter-label">
                                <i class="fas fa-filter mr-1"></i>
                                Filter by Program Type:
                            </label>
                            <div class="filter-buttons">
                                <button type="button" class="filter-btn active" data-filter="all">
                                    <i class="fas fa-th mr-1"></i>
                                    All Programs
                                    <span class="badge badge-light">{{ $academicPrograms->count() }}</span>
                                </button>
                                <button type="button" class="filter-btn" data-filter="senior_high">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    Senior High
                                    <span class="badge badge-success">{{ $academicPrograms->where('type', 'senior_high')->count() }}</span>
                                </button>
                                <button type="button" class="filter-btn" data-filter="diploma">
                                    <i class="fas fa-award mr-1"></i>
                                    Diploma
                                    <span class="badge badge-warning">{{ $academicPrograms->where('type', 'diploma')->count() }}</span>
                                </button>
                                <button type="button" class="filter-btn" data-filter="college">
                                    <i class="fas fa-university mr-1"></i>
                                    College
                                    <span class="badge badge-primary">{{ $academicPrograms->where('type', 'college')->count() }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="search-controls">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="programSearch" placeholder="Search programs or classes...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body school-classes-overview">
                <div class="row" id="programsContainer">
                    @foreach($academicPrograms as $program)
                        <div class="col-lg-6 mb-4 program-item" data-program-type="{{ $program->type }}" data-program-name="{{ strtolower($program->name) }}" data-class-names="{{ strtolower($program->schoolClasses->pluck('name')->implode(' ')) }}">
                            <div class="program-card {{ $program->type == 'senior_high' ? 'senior-high-card' : ($program->type == 'diploma' ? 'diploma-card' : 'college-card') }}">
                                <div class="program-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="program-info">
                                            <div class="program-icon {{ $program->type == 'senior_high' ? 'senior-high-icon' : ($program->type == 'diploma' ? 'diploma-icon' : 'college-icon') }}">
                                                <i class="fas fa-{{ $program->type == 'senior_high' ? 'graduation-cap' : ($program->type == 'diploma' ? 'award' : 'university') }}"></i>
                                            </div>
                                            <div class="program-details">
                                                <h4 class="program-title">{{ $program->name }}</h4>
                                                <span class="program-type">{{ $program->type == 'senior_high' ? 'Senior High School' : ($program->type == 'diploma' ? 'Diploma Program' : 'College') }}</span>
                                            </div>
                                        </div>
                                        <div class="program-stats">
                                            <span class="class-count">{{ $program->schoolClasses->count() }}</span>
                                            <small>Classes</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="program-content">
                                    @if($program->description)
                                        <p class="program-description">{{ $program->description }}</p>
                                    @endif
                                    
                                    @if($program->schoolClasses->count() > 0)
                                        <div class="classes-grid">
                                            @foreach($program->schoolClasses->take(5) as $class)
                                                <div class="class-item">
                                                    <div class="class-icon {{ $program->type == 'senior_high' ? 'senior-high-icon' : ($program->type == 'diploma' ? 'diploma-icon' : 'college-icon') }}">
                                                        <i class="fas fa-chalkboard"></i>
                                                    </div>
                                                    <div class="class-info">
                                                        <span class="class-name">{{ $class->name }}</span>
                                                        @if($class->section)
                                                            <span class="class-details">
                                                                Section: {{ $class->section }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($program->schoolClasses->count() > 5)
                                                    <div class="text-center">
                                                        <small class="text-muted">
                                                            +{{ $program->schoolClasses->count() - 5 }} more sections
                                                        </small>
                                                    </div>
                                                @endif
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fas fa-chalkboard"></i>
                                            <p>No sections configured for this program yet.</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="program-footer">
                                    <a href="{{ route('admin.school-classes.manage', $program->id) }}" class="btn btn-block {{ $program->type == 'senior_high' ? 'btn-success' : ($program->type == 'diploma' ? 'btn-warning' : 'btn-primary') }}">
                                        <i class="fas fa-eye mr-2"></i>
                                        Manage {{ $program->name }} Sections
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($academicPrograms->count() == 0)
                    <div class="text-center py-5">
                        <i class="fas fa-school fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Academic Programs Found</h5>
                        <p class="text-muted">Create academic programs first to manage school classes.</p>
                        <a href="{{ route('admin.academic-programs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>
                            Create Academic Program
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Program filtering functionality
    $('.filter-btn').click(function() {
        // Update active state
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Get filter value
        var filter = $(this).data('filter');
        
        // Filter programs
        filterPrograms(filter);
    });
    
    // Search functionality
    $('#programSearch').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        searchPrograms(searchTerm);
    });
    
    function filterPrograms(filter) {
        $('.program-item').each(function() {
            var programType = $(this).data('program-type');
            
            if (filter === 'all' || programType === filter) {
                $(this).fadeIn(300);
            } else {
                $(this).fadeOut(300);
            }
        });
        
        // Update results count
        updateResultsCount();
    }
    
    function searchPrograms(searchTerm) {
        if (searchTerm === '') {
            // If search is empty, show all programs based on current filter
            var activeFilter = $('.filter-btn.active').data('filter');
            filterPrograms(activeFilter);
            return;
        }
        
        $('.program-item').each(function() {
            var programName = $(this).data('program-name');
            var classNames = $(this).data('class-names');
            var searchableText = programName + ' ' + classNames;
            
            if (searchableText.includes(searchTerm)) {
                $(this).fadeIn(300);
            } else {
                $(this).fadeOut(300);
            }
        });
        
        // Update results count
        updateResultsCount();
    }
    
    function updateResultsCount() {
        var visibleCount = $('.program-item:visible').length;
        var totalCount = $('.program-item').length;
        
        // Update the programs count in the header
        $('.card-tools .badge').text(visibleCount + ' Programs');
        
        // Show/hide no results message
        if (visibleCount === 0) {
            if ($('#noResultsMessage').length === 0) {
                $('#programsContainer').append(`
                    <div class="col-12" id="noResultsMessage">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Programs Found</h5>
                            <p class="text-muted">Try adjusting your search or filter criteria.</p>
                            <button type="button" class="btn btn-outline-primary" onclick="clearFilters()">
                                <i class="fas fa-times mr-1"></i>
                                Clear Filters
                            </button>
                        </div>
                    </div>
                `);
            }
            $('#noResultsMessage').show();
        } else {
            $('#noResultsMessage').hide();
        }
    }
    
    // Clear filters function
    window.clearFilters = function() {
        $('#programSearch').val('');
        $('.filter-btn').removeClass('active');
        $('.filter-btn[data-filter="all"]').addClass('active');
        $('.program-item').fadeIn(300);
        updateResultsCount();
    };
    
    // Initialize results count
    updateResultsCount();
});
</script>
@endsection