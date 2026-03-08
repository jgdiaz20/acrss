@extends('layouts.app')

@section('styles')
<style>
    /* Remove default body margins and ensure full coverage */
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    body.login-page {
        background: #f8f9fa;
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
        position: relative;
    }
    
    /* Left side - Image */
    .login-image {
        flex: 1.5;
        background: url('{{ asset('images/back_g.jpg') }}') no-repeat center center;
        background-size: cover;
        position: relative;
        display: flex;
        align-items: center;    
        justify-content: center;
    }
    
    .login-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(44, 44, 45, 0.7);
    }
    
    .login-image-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: white;
        padding: 2rem;
    }
    
    .login-image-content h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .login-image-content p {
        font-size: 1.1rem;
        opacity: 0.9;
        line-height: 1.6;
        max-width: 400px;
        margin: 0 auto;
    }
    
    /* Right side - Login form */
    .login-form-side {
        flex: 0.8;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: #ffffff;
    }
    
    .login-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    max-width: 480px;
    width: 100%;
    background: #ffffff;
    position: relative;
    }
    
    .login-header {
        background: #dfe1e2;
        color: #white;
        padding: 2.2rem 2rem;
        text-align: center;
    }
    
    
    
    .login-header .logo-container {
        margin: 0 0 1.5rem 0;
    }
    
    .login-header .logo-container img {
        max-width: 320px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    
    .login-body {
       padding: 2.5rem 2rem;
    background: #ffffff;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: block;
    }
    
    .form-control {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    
    .form-control:focus {
        border-color: #2c3e50;
        box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        outline: none;
    }
    
    .form-control.is-invalid {
        border-color: #e74c3c;
        background-color: #fdf2f2;
    }
    
    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
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
        background: #1e3a8a;
        border: none;
        border-radius: 8px;
        padding: 0.875rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }
    
    .btn-login:hover {
        background: #34495e;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(44, 62, 80, 0.3);
        color: white;
    }
    
    .btn-login:active {
        transform: translateY(0);
    }
    
    .help-text {
        background: #f8f9fa;
        border-left: 4px solid #1e3a8a;
        padding: 1.25rem;
        border-radius: 8px;
        margin-top: 2rem;
        border: 1px solid #e9ecef;
    }
    
    .help-text i {
        color: #1e3a8a;
        margin-right: 0.75rem;
    }
    
    .help-text p {
        margin: 0;
        font-size: 0.9rem;
        color: #495057;
        line-height: 1.6;
    }
    
    .alert {
        border-radius: 8px;
        border: none;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
    
    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }
    
    /* Responsive - Stack on mobile */
    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
        }
        
        .login-image {
            display: none; /* Hide the left image side completely */
        }
        
        .login-form-side {
            flex: 1; /* Take full width */
            padding: 1rem;
            min-height: 100vh; /* Full height on mobile */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            max-width: 100%; /* Full width on mobile */
            margin: 0;
        }
        
        .login-header {
            padding: 1.5rem 1rem;
        }
        
        .login-body {
            padding: 1.5rem 1rem;
        }
        
        .login-header .logo-container img {
            max-width: 200px;
        }
    }
    
    /* Animation for page load */
    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .login-image {
        animation: fadeInLeft 0.8s ease-out;
    }
    
    .login-form-side {
        animation: fadeInRight 0.8s ease-out;
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <!-- Left side - Image -->
    <div class="login-image">
        <div class="login-image-content">
            <h2>Welcome</h2>
            <p>This capstone project aims to significantly lower administrative workloads by using intelligent automation to handle complex scheduling tasks. By automatically detecting conflicts between teachers, rooms, and classes before they happen, administrators no longer need to spend hours on manual corrections. Features like automated hours tracking and a centralized dashboard ensure curriculum compliance and provide instant access to data without the need for multiple spreadsheets. Additionally, the use of QR codes allows students and staff to check schedules independently, while automated reporting and validation rules prevent common errors. By transforming these repetitive manual processes into a streamlined digital workflow, the system allows school leaders to focus on strategic planning rather than time-consuming coordination.</p>
        </div>
    </div>
    
    <!-- Right side - Login form -->
    <div class="login-form-side">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="{{ asset('images/ACRSS LOGO_NEW (3).svg') }}" alt="ACRSS Logo">
                </div>
            </div>
            
            <div class="login-body">
                @if(session('status'))
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('status') }}
                    </div>
                @endif
                
                @if(session('message'))
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i> {{ session('message') }}
                    </div>
                @endif
                
                @if($errors->has('email') || $errors->has('password'))
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> 
                        {{ $errors->first('email') ?: $errors->first('password') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                                required 
                                autocomplete="email" 
                                autofocus 
                                placeholder="Enter your email"
                                value="{{ old('email') }}"
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
                                class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                required 
                                placeholder="Enter your password"
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt mr-2"></i> {{ trans('global.login') }}
                    </button>
                    
                    <div class="help-text">
                        <p><strong>Need help?</strong> For any concerns regarding your account, please approach the MIS department.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection