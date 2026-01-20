@extends('layout.app')

@section('title', isset($permission) ? 'تعديل الصلاحية' : 'إضافة صلاحية')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        @include('partials.crumb')
        <div class="row ">
            <div class="col-xl-8">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            {{ isset($permission) ? 'تعديل الصلاحية' : 'إضافة صلاحية' }}
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                              action="{{ isset($permission) ? route('permissions.update',$permission) : route('permissions.store') }}">
                            @csrf
                            @if(isset($permission)) @method('PUT') @endif

                            <div class="mb-3">
                                <label class="form-label">الاسم</label>
                                <input type="text" name="name" value="{{ old('name', $permission->name ?? '') }}" class="form-control" required placeholder="أدخل اسم الصلاحية">
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-center mt-5 gap-2">
                                <button type="submit" class="btn btn-success">
                                    {{ isset($permission) ? 'تعديل' : 'إضافة' }}
                                </button>
                                <a href="{{ route('permissions.index') }}" class="btn btn-light">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
