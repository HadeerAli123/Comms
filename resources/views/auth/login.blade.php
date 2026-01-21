<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>تسجيل الدخول - نظام الإدارة</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">

    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('assets/images/logo (1).png') }}" alt="نظام الإدارة" onerror="this.style.display='none'">
                <h2>تسجيل الدخول</h2>
                <p>يجب تسجيل الدخول للوصول إلى لوحة التحكم</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" id="errorAlert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="errorMessage">{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <div class="position-relative">
                        <input 
                            type="text" 
                            class="form-control" 
                            id="username" 
                            name="username"
                            placeholder="أدخل اسم المستخدم" 
                            value="{{ old('username') }}"
                            required
                            autofocus
                        >
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    @error('username')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <div class="position-relative">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password"
                            placeholder="أدخل كلمة المرور" 
                            required
                            autocomplete="current-password"
                        >
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    @error('password')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> تسجيل الدخول
                </button>
            </form>

            <div class="login-footer" style="display: none;">
                <a href="#">نسيت كلمة المرور؟</a>
            </div>
        </div>

        <div class="copyright">
            <p>Copyright © 2025 All rights reserved</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Show Password JS -->
    <script src="{{ asset('assets/js/show-password.js') }}"></script>
</body>
</html>