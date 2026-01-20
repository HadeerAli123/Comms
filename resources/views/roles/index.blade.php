@extends('layout.app')

@section('title', 'المجموعات')

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
                            <h2>المجموعات</h2>
                        </div>
                        
                        <!-- أدوات البحث و التصدير -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div class="d-flex gap-2 align-items-center">
                                <div class="search-box position-relative">
                                    <input type="text" class="form-control ps-5" placeholder="بحث..." aria-label="بحث" />
                                    <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                                </div>
                                <button class="btn btn-danger">بحث</button>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a class="btn btn-outline-danger" href="{{ route('roles.create') }}">
                                    <i class="fas fa-plus me-1"></i> إضافة مجموعة
                                </a>
                                <a class="btn btn-danger" href="{{ route('permissions.index') }}">
                                    <i class="fas fa-shield-alt me-1"></i> الصلاحيات
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead>
                                    <tr class="table-danger">
                                        <th>م</th>
                                        <th>الاسم</th>
                                        <th>الصلاحيات</th>
                                        <th>المستخدمين</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                            <td>{{ __("roles." . $role->name) ?? $role->name }}</td>
                                            <td>
                                                <div class="bg-success-subtle rounded-2 fw-bold p-2" data-bs-toggle="modal" data-bs-target="#permissionsModal{{ $role->id }}" style="cursor: pointer;">
                                                    الصلاحيات <small>({{ $role->permissions->count() }})</small>
                                                </div>

                                                <!-- Modal -->
                                                <div class="modal fade" id="permissionsModal{{ $role->id }}" tabindex="-1" aria-labelledby="permissionsModalLabel{{ $role->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title fw-bold" id="permissionsModalLabel{{ $role->id }}">صلاحيات : {{ __("roles." . $role->name) ?? $role->name }}</h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if($role->permissions->isEmpty())
                                                                    <div class="text-muted text-center">لا توجد صلاحيات</div>
                                                                @else
                                                                    <div class="row g-3">
                                                                        @foreach($role->permissions as $permission)
                                                                            @php
                                                                                $iconData = match(true) {
                                                                                    str_contains($permission->name, 'view') => ['icon' => 'fa-eye', 'color' => 'text-primary'],
                                                                                    str_contains($permission->name, 'create') => ['icon' => 'fa-plus', 'color' => 'text-success'],
                                                                                    str_contains($permission->name, 'edit') || str_contains($permission->name, 'update') => ['icon' => 'fa-edit', 'color' => 'text-warning'],
                                                                                    str_contains($permission->name, 'delete') => ['icon' => 'fa-trash', 'color' => 'text-danger'],
                                                                                    str_contains($permission->name, 'import') => ['icon' => 'fa-file-import', 'color' => 'text-info'],
                                                                                    str_contains($permission->name, 'export') => ['icon' => 'fa-file-export', 'color' => 'text-secondary'],
                                                                                    str_contains($permission->name, 'print') => ['icon' => 'fa-print', 'color' => 'text-dark'],
                                                                                    str_contains($permission->name, 'download') => ['icon' => 'fa-download', 'color' => 'text-primary'],
                                                                                    str_contains($permission->name, 'upload') => ['icon' => 'fa-upload', 'color' => 'text-success'],
                                                                                    default => ['icon' => 'fa-check', 'color' => 'text-primary'],
                                                                                };
                                                                            @endphp
                                                                            <div class="col-md-4 col-sm-6">
                                                                                <button class="w-100 btn btn-light border d-flex justify-content-between align-items-center py-3 shadow-sm rounded-3" type="button">
                                                                                    <span>{{ __("permissions.$permission->name") !== "permissions.$permission->name" ? __("permissions.$permission->name") : $permission->name }}</span>
                                                                                    <i class="fa {{ $iconData['icon'] }} {{ $iconData['color'] }}"></i>
                                                                                </button>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @foreach($role->users as $user)
                                                    <div class="bg-warning-subtle rounded-2 fw-bold mb-1 p-1">
                                                        {{ $user->name }}
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-success me-1" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button onclick="return confirm('هل تريد الحذف؟')" class="btn btn-sm btn-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $roles->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection