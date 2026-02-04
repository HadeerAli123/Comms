@extends('layout.app')

@section('title', 'تعديل الملف الشخصي')

@section('content')
<div id="content" class="@if(session('sidebarCollapsed')) expanded @endif">
    <!-- Header -->
    <div class="dashboard-header d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h2 class="mb-2">الملف الشخصي</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item active">تعديل الملف الشخصي</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-outline-danger d-md-none" id="sidebarCollapse">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Success Alert -->
    @if(session('status') == 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle ms-2"></i>
        تم تحديث الملف الشخصي بنجاح!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Profile Form -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle p-3 ms-3">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">معلومات الحساب</h5>
                            <small class="text-muted">قم بتحديث معلومات حسابك الشخصية</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-user ms-2 text-danger"></i>
                                الاسم الكامل
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $user->name) }}"
                                required 
                                autofocus
                                placeholder="أدخل الاسم الكامل"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope ms-2 text-danger"></i>
                                البريد الإلكتروني
                            </label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $user->email) }}"
                                required
                                placeholder="example@email.com"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($user->email_verified_at === null)
                                <small class="text-warning d-block mt-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    البريد الإلكتروني غير مفعل
                                </small>
                            @endif
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fas fa-save ms-2"></i>
                                حفظ التغييرات
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-times ms-2"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profile Info Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body p-4">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle p-4 mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                    <h5 class="mb-2">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">تاريخ الانضمام:</span>
                            <span class="fw-bold">{{ $user->created_at->format('Y/m/d') }}</span>
                        </div>
                        @if($user->email_verified_at)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">حالة الحساب:</span>
                            <span class="badge bg-success">مفعل</span>
                        </div>
                        @else
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">حالة الحساب:</span>
                            <span class="badge bg-warning">غير مفعل</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-danger border-2">
             
                <div class="card-body p-4">
                    <div class="alert alert-danger d-flex align-items-start mb-3">
                        <i class="fas fa-info-circle ms-2 mt-1"></i>
                        <div>
                            <strong>تحذير:</strong> حذف الحساب عملية لا يمكن التراجع عنها. سيتم حذف جميع بياناتك بشكل نهائي ولن تتمكن من استرجاعها.
                        </div>
                    </div>
                    <button 
                        type="button" 
                        class="btn btn-danger" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteAccountModal"
                    >
                        <i class="fas fa-trash-alt ms-2"></i>
                        حذف الحساب نهائياً
                    </button>
                </div>
            </div>
        </div>
    </div>

    
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle ms-2"></i>
                    تأكيد حذف الحساب
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                
                <div class="modal-body p-4">
                    <div class="alert alert-danger d-flex align-items-start mb-4">
                        <i class="fas fa-exclamation-circle ms-2 mt-1"></i>
                        <div>
                            <strong>تحذير شديد!</strong><br>
                            أنت على وشك حذف حسابك بشكل نهائي. هذا الإجراء لا يمكن التراجع عنه وسيتم حذف جميع بياناتك.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="delete_password" class="form-label fw-semibold">
                            أدخل كلمة المرور للتأكيد
                        </label>
                        <input 
                            type="password" 
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                            id="delete_password" 
                            name="password"
                            required
                            placeholder="كلمة المرور"
                        >
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times ms-2"></i>
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt ms-2"></i>
                        نعم، احذف حسابي
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->userDeletion->any())
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
        deleteModal.show();
    @endif
});
</script>

@endpush

@push('styles')
<style>
    .icon-box {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush