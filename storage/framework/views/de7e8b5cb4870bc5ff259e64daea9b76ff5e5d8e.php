

<?php $__env->startSection('styles'); ?>
<style>
    /* Remove default body margins and ensure full coverage */
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden;
    }
    
    body.login-page {
        background: url('<?php echo e(asset('images/asian-college-building.jpg')); ?>') no-repeat center center fixed;
        background-size: cover;
    }
    
    /* Override app container styles */
    .app.flex-row {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background: transparent;
    }
    
    .app .container {
        max-width: 100%;
        padding: 0;
        margin: 0;
        width: 100%;
    }
    
    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 20px;
    }
    
    .login-container::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }
    
    .login-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        max-width: 450px;
        width: 100%;
        position: relative;
        z-index: 2;
    }
    
    .login-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }
    
    .login-header h1 {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    
    .login-header .logo-container {
        margin: 0;
    }
    
    .login-header .logo-container img {
        max-width: 300px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    
    .login-body {
        padding: 2.5rem 2rem;
        background: white;
    }
    
    .form-group label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    
    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
    }
    
    .input-icon {
        position: relative;
    }
    
    .input-icon i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
    }
    
    .input-icon .form-control {
        padding-left: 2.75rem;
    }
    
    .btn-login {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 500;
        font-size: 1rem;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
    }
    
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-login:active {
        transform: translateY(0);
    }
    
    .help-text {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 1rem;
        border-radius: 4px;
        margin-top: 1.5rem;
    }
    
    .help-text i {
        color: #667eea;
        margin-right: 0.5rem;
    }
    
    .help-text p {
        margin: 0;
        font-size: 0.875rem;
        color: #495057;
    }
    
    .alert {
        border-radius: 8px;
        border: none;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }
    
    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1><?php echo e(trans('panel.site_title')); ?></h1>
            <div class="logo-container">
                <img src="<?php echo e(asset('images/ACRSS-logo.png')); ?>" alt="ACRSS Logo">
            </div>
            
        </div>
        
        <div class="login-body">
            <?php if(session('status')): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>
            
            <?php if(session('message')): ?>
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> <?php echo e(session('message')); ?>

                </div>
            <?php endif; ?>
            
            <?php if($errors->has('email') || $errors->has('password')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo e($errors->first('email') ?: $errors->first('password')); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" 
                            required 
                            autocomplete="email" 
                            autofocus 
                            placeholder="Enter your email"
                            value="<?php echo e(old('email')); ?>"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" 
                            required 
                            placeholder="Enter your password"
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i> <?php echo e(trans('global.login')); ?>

                </button>
                
                <div class="help-text">
                    <p><strong>Need help?</strong> For any concerns regarding your account, please approach the MIS department.</p>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/auth/login.blade.php ENDPATH**/ ?>