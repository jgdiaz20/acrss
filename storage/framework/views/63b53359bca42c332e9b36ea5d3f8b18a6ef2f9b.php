
<?php $__env->startSection('content'); ?>

<div class="card">
    <div class="card-header">
        <?php echo e(trans('global.show')); ?> <?php echo e(trans('cruds.lesson.title')); ?>

    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="<?php echo e(route('admin.lessons.index')); ?>">
                    <?php echo e(trans('global.back_to_list')); ?>

                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.id')); ?>

                        </th>
                        <td>
                            <?php echo e($lesson->id); ?>

                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.class')); ?>

                        </th>
                        <td>
                            <?php echo e($lesson->class->name ?? ''); ?>

                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.teacher')); ?>

                        </th>
                        <td>
                            <?php echo e($lesson->teacher->name ?? ''); ?>

                        </td>
                    </tr>
                    <tr>
                        <th>
                            Subject
                        </th>
                        <td>
                            <?php echo e($lesson->subject->name ?? 'No Subject'); ?>

                        </td>
                    </tr>
                    <tr>
                        <th>
                            Room
                        </th>
                        <td>
                            <?php echo e($lesson->room->display_name ?? ''); ?>

                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.weekday')); ?>

                        </th>
                        <td>
                            <?php echo e(\App\Lesson::WEEK_DAYS[$lesson->weekday] ?? ''); ?>

                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.start_time')); ?>

                        </th>
                        <td>
                            <?php echo e(\Carbon\Carbon::parse($lesson->start_time)->format('g:i A')); ?>

                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo e(trans('cruds.lesson.fields.end_time')); ?>

                        </th>
                        <td>
                            <?php echo e(\Carbon\Carbon::parse($lesson->end_time)->format('g:i A')); ?>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/lessons/show.blade.php ENDPATH**/ ?>