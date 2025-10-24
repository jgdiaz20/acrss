@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.lesson.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.lessons.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.lesson.fields.id') }}
                        </th>
                        <td>
                            {{ $lesson->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.lesson.fields.class') }}
                        </th>
                        <td>
                            {{ $lesson->class->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.lesson.fields.teacher') }}
                        </th>
                        <td>
                            {{ $lesson->teacher->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Subject
                        </th>
                        <td>
                            {{ $lesson->subject->name ?? 'No Subject' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Room
                        </th>
                        <td>
                            {{ $lesson->room->display_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.lesson.fields.weekday') }}
                        </th>
                        <td>
                            {{ \App\Lesson::WEEK_DAYS[$lesson->weekday] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.lesson.fields.start_time') }}
                        </th>
                        <td>
                            {{ \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.lesson.fields.end_time') }}
                        </th>
                        <td>
                            {{ \Carbon\Carbon::parse($lesson->end_time)->format('g:i A') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection