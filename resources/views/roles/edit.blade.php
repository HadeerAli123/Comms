@extends('layout.app')

@section('title', isset($role) ? 'تعديل المجموعة' : 'إضافة مجموعة')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        @include('partials.crumb')

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            {{ isset($role) ? 'تعديل المجموعة' : 'إضافة مجموعة' }}
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                              action="{{ isset($role) ? route('roles.update',$role) : route('roles.store') }}">
                            @csrf
                            @if(isset($role)) @method('PUT') @endif

                            <div class="mb-3">
                                <label class="form-label">الاسم</label>
                                <input type="text" name="name" value="{{ $role->name ?? '' }}" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">الصلاحيات</label>
                                <div class="row">
                                    @foreach($permissions as $index => $permission)
                                        @php
                                            $isLastColumn = (($index + 1) % 4 == 0); // 4 columns per row
                                        @endphp
                                        <div class="col-md-4 col-lg-3 mb-2" style="{{ $isLastColumn ? '' : 'border-left: 0.5px solid #19191a;' }}">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                    style="border: 1px solid;"
                                                    id="perm-{{ $permission->id }}"
                                                    {{ isset($role) && $role->permissions->contains('name',$permission->name) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                    {{ __('permissions.' . $permission->name) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-5 gap-2">
                                <button type="submit" class="btn btn-success">حفظ</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-light">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
