@extends('layout.app')

@section('title', isset($role) ? 'تعديل المجموعة' : 'إضافة مجموعة')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        @include('partials.crumb')

        <div class="row">
            <div class="col-xl-12">
                <!-- داخل #content -->
                <div class="card p-4 border-0">
                    <div class="commission-section">
                        <div class="page-title mb-4">
                            <h2>{{ isset($role) ? 'تعديل المجموعة' : 'إضافة مجموعة' }}</h2>
                        </div>

                        <form method="POST" action="{{ isset($role) ? route('roles.update',$role) : route('roles.store') }}">
                            @csrf
                            @if(isset($role)) @method('PUT') @endif

                            <!-- الاسم -->
                            <div class="mb-3">
                                <label for="groupName" class="form-label fw-bold">الاسم</label>
                                <input type="text" id="groupName" name="name" value="{{ $role->name ?? '' }}" class="form-control" placeholder="أدخل اسم المجموعة" required>
                            </div>

                            <!-- الصلاحيات -->
                            <h5 class="fw-bold mb-3">الصلاحيات</h5>
                            <div class="row g-5">
                                @php
                                    $chunkedPermissions = $permissions->chunk(ceil($permissions->count() / 4));
                                @endphp

                                @foreach($chunkedPermissions as $chunk)
                                    <div class="col-md-5 col-sm-6 border-box">
                                        @foreach($chunk as $permission)
                                            <div class="form-check {{ !$loop->first ? 'my-1' : '' }}">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm-{{ $permission->id }}" {{ isset($role) && $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                    {{ __('permissions.' . $permission->name) !== 'permissions.' . $permission->name ? __('permissions.' . $permission->name) : $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger mt-3">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $msg)
                                            <li>{{ $msg }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- الأزرار -->
                            <div class="mt-4 gap-4">
                                <button type="submit" class="btn btn-danger">حفظ</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection