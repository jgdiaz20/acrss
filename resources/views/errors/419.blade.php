@extends('layouts.app')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header" style="background: linear-gradient(135deg, #f39c12 0%, #e74c3c 100%);">
            <i class="fas fa-clock fa-3x mb-3"></i>
            <h1>Session Expired</h1>
        </div>
        
        <div class="login-body">
            <div class="alert alert-warning" style="border-radius: 8px; border: none; padding: 1rem; margin-bottom: 1.5rem;">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Your session has expired due to inactivity.</strong>
            </div>
            
            <p class="text-muted mb-4">
                For your security, we automatically log you out after a period of inactivity. 
                Please log in again to continue using the system.
            </p>
            
            <a href="{{ route('login') }}" class="btn btn-primary btn-block" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px; padding: 0.75rem 2rem; font-weight: 500;">
                <i class="fas fa-sign-in-alt mr-2"></i> Return to Login
            </a>
            
            <div class="help-text" style="background: #f8f9fa; border-left: 4px solid #667eea; padding: 1rem; border-radius: 4px; margin-top: 1.5rem;">
                <i class="fas fa-lightbulb" style="color: #667eea; margin-right: 0.5rem;"></i>
                <p style="margin: 0; font-size: 0.875rem; color: #495057;">
                    <strong>Tip:</strong> Sessions last for 3 hours of inactivity. Keep the system active to avoid automatic logout.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Reuse login page styles */
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden;
    }
    
    body {
        background: url('{{ asset('images/asian-college-building.jpg') }}') no-repeat center center fixed;
        background-size: cover;
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
        background: white;
    }
    
    .login-header {
        color: white;
        padding: 2rem;
        text-align: center;
    }
    
    .login-header h1 {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0;
    }
    
    .login-body {
        padding: 2.5rem 2rem;
        background: white;
    }
</style>
@endsection
