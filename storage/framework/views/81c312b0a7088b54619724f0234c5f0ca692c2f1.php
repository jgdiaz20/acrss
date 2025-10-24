
<?php $__env->startSection('content'); ?>

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('admin.home')); ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('admin.users.index')); ?>">
                <i class="fas fa-users"></i> User Management
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-edit"></i> Edit User
        </li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-edit mr-2"></i>
            <?php echo e(trans('global.edit')); ?> <?php echo e(trans('cruds.user.title_singular')); ?>

        </h3>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo e(route("admin.users.update", [$user->id])); ?>" enctype="multipart/form-data">
            <?php echo method_field('PUT'); ?>
            <?php echo csrf_field(); ?>
            <input type="hidden" id="currentPasswordHash" value="<?php echo e($currentPasswordHash); ?>">
            <div class="form-group">
                <label class="required" for="name"><?php echo e(trans('cruds.user.fields.name')); ?></label>
                <input class="form-control <?php echo e($errors->has('name') ? 'is-invalid' : ''); ?>" type="text" name="name" id="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                <?php if($errors->has('name')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('name')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block"><?php echo e(trans('cruds.user.fields.name_helper')); ?></span>
            </div>
            <div class="form-group">
                <label class="required" for="email"><?php echo e(trans('cruds.user.fields.email')); ?></label>
                <input class="form-control <?php echo e($errors->has('email') ? 'is-invalid' : ''); ?>" type="text" name="email" id="email" value="<?php echo e(old('email', $user->email)); ?>" required>
                <?php if($errors->has('email')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('email')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block"><?php echo e(trans('cruds.user.fields.email_helper')); ?></span>
            </div>
            <div class="form-group">
                <label for="password"><?php echo e(trans('cruds.user.fields.password')); ?></label>
                <div class="input-group">
                    <input class="form-control <?php echo e($errors->has('password') ? 'is-invalid' : ''); ?>" type="password" name="password" id="password" placeholder="Leave blank to keep current password">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>
                <?php if($errors->has('password')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('password')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Leave blank to keep current password. Click the eye icon to show/hide password.
                        <br><i class="fas fa-exclamation-triangle text-warning"></i> New password must be different from your current password.
                    </small>
                </span>
            </div>
            
            <div class="form-group" id="passwordConfirmationGroup" style="display: none;">
                <label for="password_confirmation">Confirm New Password</label>
                <div class="input-group">
                    <input class="form-control <?php echo e($errors->has('password_confirmation') ? 'is-invalid' : ''); ?>" type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm new password">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                            <i class="fas fa-eye" id="togglePasswordConfirmationIcon"></i>
                        </button>
                    </div>
                </div>
                <?php if($errors->has('password_confirmation')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('password_confirmation')); ?>

                    </div>
                <?php endif; ?>
                <span class="help-block">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Re-enter the new password to confirm.
                    </small>
                </span>
            </div>
            <div class="form-group">
                <label class="required" for="roles"><?php echo e(trans('cruds.user.fields.roles')); ?></label>
                <select class="form-control select2 <?php echo e($errors->has('roles') ? 'is-invalid' : ''); ?>" name="roles[]" id="roles" multiple required <?php echo e($user->is_student ? 'disabled' : ''); ?>>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $roles): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php echo e((in_array($id, old('roles', [])) || $user->roles->contains($id)) ? 'selected' : ''); ?>><?php echo e($roles); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($user->is_student): ?>
                    <input type="hidden" name="roles[]" value="4">
                    <small class="form-text text-muted">
                        <i class="fas fa-lock mr-1"></i> Role is locked for students
                    </small>
                <?php else: ?>
                    <span class="help-block">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Select the appropriate role(s) for this user. Avoid selecting multiple conflicting roles.
                        </small>
                    </span>
                <?php endif; ?>
                <?php if($errors->has('roles')): ?>
                    <div class="invalid-feedback">
                        <?php echo e($errors->first('roles')); ?>

                    </div>
                <?php endif; ?>
            </div>
            <?php if($user->is_student): ?>
                <div class="form-group">
                    <label for="class_id"><?php echo e(trans('cruds.user.fields.class')); ?></label>
                    <select class="form-control select2 <?php echo e($errors->has('class') ? 'is-invalid' : ''); ?>" name="class_id" id="class_id">
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>" <?php echo e(($user->class ? $user->class->id : old('class_id')) == $id ? 'selected' : ''); ?>><?php echo e($class); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php if($errors->has('class')): ?>
                        <div class="invalid-feedback">
                            <?php echo e($errors->first('class')); ?>

                        </div>
                    <?php endif; ?>
                    <span class="help-block"><?php echo e(trans('cruds.user.fields.class_helper')); ?></span>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> <?php echo e(trans('global.save')); ?>

                </button>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </form>
    </div>
</div>



<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>
<script>
$(document).ready(function() {
    // Password visibility toggle for main password field
    $('#togglePassword').click(function() {
        const passwordField = $('#password');
        const passwordIcon = $('#togglePasswordIcon');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Password visibility toggle for confirmation field
    $('#togglePasswordConfirmation').click(function() {
        const passwordField = $('#password_confirmation');
        const passwordIcon = $('#togglePasswordConfirmationIcon');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Show/hide password confirmation field based on password input
    $('#password').on('input', function() {
        const passwordValue = $(this).val();
        const confirmationGroup = $('#passwordConfirmationGroup');
        
        if (passwordValue.length > 0) {
            confirmationGroup.show();
            $('#password_confirmation').prop('required', true);
        } else {
            confirmationGroup.hide();
            $('#password_confirmation').prop('required', false).val('');
        }
    });
    
    // Real-time password confirmation validation
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();
        const field = $(this);
        
        if (confirmation.length > 0 && password !== confirmation) {
            field.addClass('is-invalid');
            if (!field.siblings('.invalid-feedback').length) {
                field.after('<div class="invalid-feedback">Passwords do not match</div>');
            }
        } else {
            field.removeClass('is-invalid');
            field.siblings('.invalid-feedback').remove();
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>