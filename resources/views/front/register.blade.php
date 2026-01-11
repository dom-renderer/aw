@extends('front.layout')

@push('css')
<style>
    .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .login-card {
        width: 100%;
        max-width: 420px;
        border-radius: 12px;
        border: none;
    }

    .login-header img {
        height: 50px;
    }

    .form-control {
        height: 46px;
        border-radius: 8px;
    }

    .btn-primary {
        background-color: #1f3b73;
        border-color: #1f3b73;
        height: 46px;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-primary:hover {
        background-color: #162b56;
    }

    .login-footer a {
        color: #1f3b73;
        text-decoration: none;
        font-weight: 500;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }

    .password-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
    }
</style>
@endpush

@section('content')

<div class="login-container">
    <div class="card login-card">

        <div class="login-header text-center mb-4">
            <img src="{{ asset('front-theme/images/aw-log.svg') }}" alt="ANJO Wholesale">
        </div>

        <h5 class="text-center mb-1">Create an account</h5>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}"> @csrf
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="John Doe" value="{{ old('name') }}">
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="john.doe@gmail.com" value="{{ old('email') }}">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password">
                    <i class="bi bi-eye password-toggle" onclick="togglePassword('password', this)"></i>
                </div>
                @error('password')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                    <i class="bi bi-eye password-toggle" onclick="togglePassword('password_confirmation', this)"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                Sign Up
            </button>
        </form>

        <!-- Footer -->
        <div class="login-footer text-center">
            <small class="text-muted">
                Already have an account?
                <a href="{{ route('login') }}">Sign In</a>
            </small>
        </div>

    </div>
</div>


@endsection

@push('js')
<script>
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>
@endpush
