<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        Add Room
    </div>

    <div class="card-body">
        <form action="<?php echo e(route("admin.room-management.rooms.store")); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="form-group <?php echo e($errors->has('name') ? 'has-error' : ''); ?>">
                <label for="name">Room Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo e(old('name', isset($room) ? $room->name : '')); ?>" required placeholder="e.g., Room 101, Computer Lab A">
                <?php if($errors->has('name')): ?>
                    <em class="invalid-feedback">
                        <?php echo e($errors->first('name')); ?>

                    </em>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo e($errors->has('description') ? 'has-error' : ''); ?>">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief description of the room's purpose or features"><?php echo e(old('description', isset($room) ? $room->description : '')); ?></textarea>
                <?php if($errors->has('description')): ?>
                    <em class="invalid-feedback">
                        <?php echo e($errors->first('description')); ?>

                    </em>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo e($errors->has('capacity') ? 'has-error' : ''); ?>">
                <label for="capacity">Capacity *</label>
                <input type="number" id="capacity" name="capacity" class="form-control" value="<?php echo e(old('capacity', isset($room) ? $room->capacity : '')); ?>" min="1" max="500" required placeholder="Maximum number of students">
                <?php if($errors->has('capacity')): ?>
                    <em class="invalid-feedback">
                        <?php echo e($errors->first('capacity')); ?>

                    </em>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo e($errors->has('is_lab') ? 'has-error' : ''); ?>">
                <label for="is_lab">Room Type *</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_lab" id="is_lab_classroom" value="0" <?php echo e(old('is_lab', isset($room) ? $room->is_lab : '0') == '0' ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="is_lab_classroom">
                        <strong>Classroom</strong> - Standard teaching room
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_lab" id="is_lab_lab" value="1" <?php echo e(old('is_lab', isset($room) ? $room->is_lab : '0') == '1' ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="is_lab_lab">
                        <strong>Laboratory</strong> - Specialized lab for practical work
                    </label>
                </div>
                <?php if($errors->has('is_lab')): ?>
                    <em class="invalid-feedback">
                        <?php echo e($errors->first('is_lab')); ?>

                    </em>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo e($errors->has('has_equipment') ? 'has-error' : ''); ?>">
                <label for="has_equipment">Equipment Available</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="has_equipment" id="has_equipment" value="1" <?php echo e(old('has_equipment', isset($room) ? $room->has_equipment : false) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="has_equipment">
                        This room has specialized equipment (computers, projectors, lab equipment, etc.)
                    </label>
                </div>
                <?php if($errors->has('has_equipment')): ?>
                    <em class="invalid-feedback">
                        <?php echo e($errors->first('has_equipment')); ?>

                    </em>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> Laboratory rooms are required for subjects that need practical work. Equipment availability helps match subjects with appropriate rooms.
                </div>
            </div>
            
            <div class="form-group">
                <input class="btn btn-success" type="submit" value="Create Room">
                <a href="<?php echo e(route('admin.room-management.rooms.index')); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/rooms/create.blade.php ENDPATH**/ ?>