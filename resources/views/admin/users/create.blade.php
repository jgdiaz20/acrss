@extends('layouts.admin')
@section('content')

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.home') }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.users.index') }}">
                <i class="fas fa-users"></i> User Management
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-plus"></i> Create User
        </li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-plus mr-2"></i>
            {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
        </h3>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.users.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" id="email" value="{{ old('email') }}" required>
                @if($errors->has('email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="password">{{ trans('cruds.user.fields.password') }}</label>
                <div class="input-group">
                    <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>
                @if($errors->has('password'))
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.password_helper') }}</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="password_confirmation">Confirm Password</label>
                <div class="input-group">
                    <input class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" type="password" name="password_confirmation" id="password_confirmation" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                            <i class="fas fa-eye" id="togglePasswordConfirmationIcon"></i>
                        </button>
                    </div>
                </div>
                @if($errors->has('password_confirmation'))
                    <div class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                @endif
                <span class="help-block">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Re-enter the password to confirm.
                    </small>
                </span>
            </div>
            <div class="form-group">
                <label class="required" for="roles">{{ trans('cruds.user.fields.roles') }}</label>
                <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}" name="roles[]" id="roles" multiple required>
                    @foreach($roles as $id => $roles)
                        <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $roles }}</option>
                    @endforeach
                </select>
                @if($errors->has('roles'))
                    <div class="invalid-feedback">
                        {{ $errors->first('roles') }}
                    </div>
                @endif
                <span class="help-block">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Select the appropriate role(s) for this user. Avoid selecting multiple conflicting roles.
                    </small>
                </span>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> {{ trans('global.save') }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
@parent
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
    
    // Clear validation errors on input for all form fields
    $('input, select, textarea').on('input change', function() {
        const field = $(this);
        const fieldName = field.attr('name');
        
        // Remove is-invalid class and error message when user starts typing
        if (field.hasClass('is-invalid')) {
            field.removeClass('is-invalid');
            
            // Remove the invalid-feedback div if it exists
            field.siblings('.invalid-feedback').remove();
            field.next('.invalid-feedback').remove();
        }
    });
    
    // Special handling for email field - validate .edu.ph format
    $('#email').on('input', function() {
        const email = $(this).val();
        const field = $(this);
        const eduPhPattern = /^[\w\.-]+@[\w\.-]+\.edu\.ph$/i;
        
        // Clear previous errors first
        field.removeClass('is-invalid');
        field.siblings('.invalid-feedback').remove();
        
        // If email is not empty and doesn't match pattern, show error
        if (email.length > 0 && !eduPhPattern.test(email)) {
            field.addClass('is-invalid');
            if (!field.siblings('.invalid-feedback').length) {
                field.after('<div class="invalid-feedback">Email must be a valid institutional email address ending with .edu.ph</div>');
            }
        }
    });
});
</script>
@endsection
