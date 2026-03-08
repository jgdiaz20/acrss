<?php $attributes = $attributes->exceptProps(['width' => 40, 'height' => 40, 'class' => '']); ?>
<?php foreach (array_filter((['width' => 40, 'height' => 40, 'class' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<img src="<?php echo e(asset('images/acrss-logo-new.svg')); ?>" 
     style="width: <?php echo e($width); ?>px; height: <?php echo e($height); ?>px;" 
     class="<?php echo e($class); ?>" 
     alt="ACRSS Logo" /><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/components/logo.blade.php ENDPATH**/ ?>