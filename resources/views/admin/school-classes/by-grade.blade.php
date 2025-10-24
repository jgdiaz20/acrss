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
        <li class="breadcrumb-item">
            <a href="{{ route('admin.school-classes.index') }}">
                <i class="fas fa-school"></i> School Classes
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.school-classes.program', $program->type) }}">
                <i class="fas fa-{{ $program->type == 'senior_high' ? 'graduation-cap' : 'university' }}"></i>
                {{ $program->type == 'senior_high' ? 'Senior High School' : 'College' }}
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-layer-group"></i> {{ $gradeLevel->level_name }} Classes
        </li>
    </ol>
</nav>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-layer-group"></i>
                    {{ $gradeLevel->level_name }} Classes
                    <small class="text-muted">({{ $program->type == 'senior_high' ? 'Senior High School' : 'College' }})</small>
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.school-classes.program', $program->type) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to {{ $program->type == 'senior_high' ? 'Senior High School' : 'College' }}
                    </a>
                    @can('school_class_create')
                        <a class="btn btn-success btn-sm" href="{{ route('admin.school-classes.create', ['program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]) }}">
                            <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('cruds.schoolClass.title_singular') }}
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body p-2">
                @if($schoolClasses->count() > 0)
                    <div class="row">
                        @foreach($schoolClasses as $class)
                            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                <div class="class-card border rounded p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1 text-primary">
                                                <strong>{{ $class->name }}</strong>
                                                @if($class->section)
                                                    <small class="text-muted">- {{ $class->section }}</small>
                                                @endif
                                            </h6>
                                            <small class="text-muted">ID: {{ $class->id }}</small>
                                        </div>
                                        <div class="text-right">
                                            @if($class->is_active)
                                                <span class="badge badge-success badge-sm">Active</span>
                                            @else
                                                <span class="badge badge-secondary badge-sm">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="class-info mb-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="info-item">
                                                    <div class="info-value text-primary">{{ $class->classUsers->count() }}</div>
                                                    <div class="info-label">Current</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="info-item">
                                                    <div class="info-value text-info">{{ $class->max_students ?? '∞' }}</div>
                                                    <div class="info-label">Max</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="class-actions">
                                        <div class="btn-group w-100" role="group">
                                            @can('school_class_show')
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.school-classes.show', $class->id) }}" title="View Schedule">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('school_class_edit')
                                                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.school-classes.edit', ['school_class' => $class->id, 'program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]) }}" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('school_class_delete')
                                                <button type="button" class="btn btn-sm btn-outline-danger trigger-class-delete" data-class-id="{{ $class->id }}" data-class-name="{{ $class->name }}" data-class-section="{{ $class->section }}" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                                    
                                    <!-- Hidden delete form -->
                                    @can('school_class_delete')
                                        <form id="delete-form-{{ $class->id }}" action="{{ route('admin.school-classes.destroy', $class->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5 class="text-warning">No Sections Available</h5>
                            <p class="text-muted mb-3">
                                No sections have been created for {{ $gradeLevel->level_name }} in {{ $program->name }} yet.
                            </p>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Note:</strong> Please create sections first before managing classes for this grade level.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).on('click', '.trigger-class-delete', function(){
        const $btn = $(this);
        const classId = $btn.data('class-id');
        const name = $btn.data('class-name');
        const section = $btn.data('class-section') || '';
        $('#classDeleteId').text(classId);
        $('#classDeleteName').text(name + (section ? ' - ' + section : ''));
        $('#confirmClassDeleteBtn').off('click').on('click', function(){
            $('#delete-form-' + classId).trigger('submit');
        });
        $('#classDeleteModal').modal('show');
    });
});
</script>

<!-- Class/Section Delete Confirmation Modal -->
<div class="modal fade" id="classDeleteModal" tabindex="-1" role="dialog" aria-labelledby="classDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="classDeleteModalLabel"><i class="fas fa-trash-alt mr-1"></i> Confirm Delete Class/Section</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>You're about to delete the following class/section:</p>
        <ul class="list-unstyled mb-0">
          <li><strong>ID:</strong> <span id="classDeleteId"></span></li>
          <li><strong>Name:</strong> <span id="classDeleteName"></span></li>
        </ul>
        <div class="alert alert-warning mt-3">
          <i class="fas fa-exclamation-triangle mr-1"></i> This action cannot be undone. If this class has enrolled students or scheduled lessons, deletion will be blocked with details.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmClassDeleteBtn"><i class="fas fa-trash-alt"></i> Delete</button>
      </div>
    </div>
  </div>
</div>
@endsection
