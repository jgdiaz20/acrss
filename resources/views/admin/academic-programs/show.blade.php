@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Academic Program Details</h3>
                <div class="card-tools">
                    @can('academic_program_edit')
                        <a class="btn btn-info btn-sm" href="{{ route('admin.academic-programs.edit', $academicProgram->id) }}">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endcan
                    <a href="{{ route('admin.academic-programs.index') }}" class="btn btn-secondary btn-sm">
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
                                <td>{{ $academicProgram->name }}</td>
                            </tr>
                            <tr>
                                <th>Program Code</th>
                                <td>{{ $academicProgram->code }}</td>
                            </tr>
                            <tr>
                                <th>Program Type</th>
                                <td>
                                    <span class="badge badge-{{ $academicProgram->type == 'senior_high' ? 'primary' : 'info' }}">
                                        {{ ucfirst(str_replace('_', ' ', $academicProgram->type)) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Duration</th>
                                <td>{{ $academicProgram->duration_years }} years</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($academicProgram->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
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
                                <p>{{ $academicProgram->description ?? 'No description provided.' }}</p>
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
                                @if($academicProgram->gradeLevels->count() > 0)
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
                                                @foreach($academicProgram->gradeLevels as $gradeLevel)
                                                    <tr>
                                                        <td>{{ $gradeLevel->level_name }}</td>
                                                        <td>{{ $gradeLevel->description ?? 'N/A' }}</td>
                                                        <td>{{ $gradeLevel->order }}</td>
                                                        <td>
                                                            @if($gradeLevel->is_active)
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-secondary">Inactive</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No grade levels found for this program.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
