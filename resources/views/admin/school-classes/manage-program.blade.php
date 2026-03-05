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
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-{{ $program->type == 'senior_high' ? 'graduation-cap' : 'university' }}"></i>
            {{ $program->name }}
        </li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $program->type == 'senior_high' ? 'graduation-cap' : 'university' }}"></i>
                    {{ $program->name }} Classes
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.school-classes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Programs
                    </a>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="program-section mb-3">
                    <div class="program-header bg-light p-2 mb-2 rounded">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-graduation-cap mr-1"></i>
                            {{ $program->name }} ({{ $program->code }})
                        </h5>
                        <small class="text-muted">{{ $program->description }}</small>
                    </div>
                    
                    <div class="row">
                        @foreach($gradeLevels as $gradeLevel)
                            @php
                                $classesInGrade = $schoolClasses->get($gradeLevel->id, collect());
                            @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <div class="grade-card border rounded p-2 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 text-info">
                                            <i class="fas fa-layer-group mr-1"></i>
                                            {{ $gradeLevel->level_name }}
                                        </h6>
                                        <a href="{{ route('admin.school-classes.program.grade', [$program->type, $gradeLevel->id]) }}" 
                                           class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                    
                                    @if($classesInGrade->count() > 0)
                                        <div class="classes-list">
                                            @foreach($classesInGrade->take(3) as $class)
                                                <div class="class-item mb-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="class-name text-truncate" style="max-width: 200px;" title="{{ $class->name }}">
                                                            {{ $class->name }}
                                                            @if($class->section)
                                                                <small class="text-muted">- {{ $class->section }}</small>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($classesInGrade->count() > 3)
                                                <div class="text-center">
                                                    <small class="text-muted">
                                                        +{{ $classesInGrade->count() - 3 }} more classes
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <small><i class="fas fa-exclamation-triangle"></i> No Sections</small>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-2">
                                        <a href="{{ route('admin.school-classes.program.grade', [$program->type, $gradeLevel->id]) }}" 
                                           class="btn btn-sm btn-outline-info btn-block">
                                            Manage 
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
