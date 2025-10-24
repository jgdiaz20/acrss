<div class="sidebar">
    <nav class="sidebar-nav">

        <ul class="nav">
            <?php if(auth()->user()->is_admin): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("admin.home")); ?>" class="nav-link">
                        <i class="nav-icon fas fa-fw fa-tachometer-alt">

                        </i>
                        <?php echo e(trans('global.dashboard')); ?>

                    </a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->is_teacher && !auth()->user()->is_admin): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("teacher.dashboard")); ?>" class="nav-link <?php echo e(request()->is('teacher') || request()->is('teacher/') ? 'active' : ''); ?>">
                        <i class="fa-fw fas fa-tachometer-alt nav-icon">
                        </i>
                        My Dashboard
                    </a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->is_student): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("student.calendar.index")); ?>" class="nav-link <?php echo e(request()->is('student/calendar') || request()->is('student/calendar/*') ? 'active' : ''); ?>">
                        <i class="fa-fw fas fa-calendar nav-icon">
                        </i>
                        My Schedule
                    </a>
                </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_management_access')): ?>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        <?php echo e(trans('cruds.userManagement.title')); ?>

                    </a>
                    <ul class="nav-dropdown-items">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permission_access')): ?>
                            <?php if(config('app.env') !== 'production'): ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route("admin.permissions.index")); ?>" class="nav-link <?php echo e(request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : ''); ?>">
                                        <i class="fa-fw fas fa-unlock-alt nav-icon">

                                        </i>
                                        <?php echo e(trans('cruds.permission.title')); ?>

                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('role_access')): ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route("admin.roles.index")); ?>" class="nav-link <?php echo e(request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : ''); ?>">
                                    <i class="fa-fw fas fa-briefcase nav-icon">

                                    </i>
                                    <?php echo e(trans('cruds.role.title')); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_access')): ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route("admin.users.index")); ?>" class="nav-link <?php echo e(request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : ''); ?>">
                                    <i class="fa-fw fas fa-user nav-icon">

                                    </i>
                                    <?php echo e(trans('cruds.user.title')); ?>

                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo e(route("admin.users.index")); ?>?role=3" class="nav-link <?php echo e(request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : ''); ?>">
                                    <i class="fa-fw fas fa-user nav-icon">

                                    </i>
                                    Teachers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo e(route("admin.users.index")); ?>?role=4" class="nav-link <?php echo e(request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : ''); ?>">
                                    <i class="fa-fw fas fa-user nav-icon">

                                    </i>
                                    Students
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('school_class_access')): ?>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-school nav-icon">

                        </i>
                        <?php echo e(trans('cruds.schoolClass.title')); ?>

                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.school-classes.program', 'senior_high')); ?>" class="nav-link <?php echo e(request()->is('admin/school-classes/senior-high*') ? 'active' : ''); ?>">
                                <i class="fa-fw fas fa-graduation-cap nav-icon">

                                </i>
                                Senior High School
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.school-classes.program', 'diploma')); ?>" class="nav-link <?php echo e(request()->is('admin/school-classes/diploma*') ? 'active' : ''); ?>">
                                <i class="fa-fw fas fa-award nav-icon">

                                </i>
                                Diploma Program
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.school-classes.program', 'college')); ?>" class="nav-link <?php echo e(request()->is('admin/school-classes/college*') ? 'active' : ''); ?>">
                                <i class="fa-fw fas fa-university nav-icon">

                                </i>
                                College
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.academic-programs.index')); ?>" class="nav-link <?php echo e(request()->is('admin/academic-programs*') ? 'active' : ''); ?>">
                                <i class="fa-fw fas fa-cogs nav-icon">

                                </i>
                                Manage Programs
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('subject_access')): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("admin.subjects.index")); ?>" class="nav-link <?php echo e(request()->is('admin/subjects') || request()->is('admin/subjects/*') ? 'active' : ''); ?>">
                        <i class="fa-fw fas fa-book nav-icon">

                        </i>
                        Subjects
                    </a>
                </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('lesson_access')): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("admin.lessons.index")); ?>" class="nav-link <?php echo e(request()->is('admin/lessons') || request()->is('admin/lessons/*') ? 'active' : ''); ?>">
                        <i class="fa-fw fas fa-clock nav-icon">

                        </i>
                        <?php echo e(trans('cruds.lesson.title')); ?>

                    </a>
                </li>
            <?php endif; ?>
           
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('room_access')): ?>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-building nav-icon">

                        </i>
                        Room Management
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a href="<?php echo e(route("admin.room-management.rooms.index")); ?>" class="nav-link <?php echo e(request()->is('admin/room-management/rooms') || request()->is('admin/room-management/rooms/*') ? 'active' : ''); ?>">
                                <i class="fa-fw fas fa-door-open nav-icon">

                                </i>
                                Rooms
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("admin.room-management.room-timetables.index")); ?>" class="nav-link <?php echo e(request()->is('admin/room-management/room-timetables') || request()->is('admin/room-management/room-timetables/*') ? 'active' : ''); ?>">
                                <i class="fa-fw fas fa-calendar-alt nav-icon">

                                </i>
                                Room Timetables
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("admin.room-management.master-timetable.index")); ?>" class="nav-link <?php echo e(request()->is('admin/room-management/master-timetable') || request()->is('admin/room-management/master-timetable/*') ? 'active' : ''); ?>">
                                <i class="fa-fw fas fa-th nav-icon">

                                </i>
                                Master Timetable
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
           
           <!-- <li class="nav-item">
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="nav-icon fas fa-fw fa-sign-out-alt">

                    </i>
                    <?php echo e(trans('global.logout')); ?>

                </a>
            </li> -->
        </ul>

    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
<?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/partials/menu.blade.php ENDPATH**/ ?>