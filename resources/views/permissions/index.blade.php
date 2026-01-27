@extends('layout.app')

@section('title', 'الصلاحيات')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        @include('partials.crumb')

        <div class="row">
            <div class="col-xl-12">
                <div class="card p-4 border-0">
                    <div class="commission-section">
                        <div class="page-title mb-4">
                            <h2>الصلاحيات</h2>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div class="d-flex gap-2 align-items-center">
                                <form method="GET" action="{{ route('permissions.index') }}" class="d-flex gap-2" id="permission-search-form">
                                    <div class="search-box position-relative">
                                        <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5" placeholder="بحث..." aria-label="بحث" id="permission-search-input" autocomplete="off" />
                                        <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                                    </div>
                                    <button type="submit" class="btn btn-danger">بحث</button>
                                </form>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                                    + إضافة صلاحية
                                </button>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                let timer;
                                const input = document.getElementById('permission-search-input');
                                const form = document.getElementById('permission-search-form');
                                if (input && form) {
                                    input.addEventListener('input', function () {
                                        clearTimeout(timer);
                                        timer = setTimeout(function () {
                                            form.submit();
                                        }, 500);
                                    });
                                }
                            });
                        </script>

                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead>
                                    <tr class="table-danger">
                                        <th>م</th>
                                        <th>الاسم</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $permission)
                                        <tr>
                                            <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                            <td>
                                                {{ __("permissions." . $permission->name) !== "permissions." . $permission->name ? __("permissions." . $permission->name) : $permission->name }}
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#editPermissionModal-{{ $permission->id }}" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button onclick="return confirm('هل تريد الحذف؟')" class="btn btn-sm btn-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>

                                            <!-- Modal  -->
                                            <div class="modal fade" id="editPermissionModal-{{ $permission->id }}" tabindex="-1" aria-labelledby="editPermissionModalLabel-{{ $permission->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <form action="{{ route('permissions.update', $permission) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editPermissionModalLabel-{{ $permission->id }}">تعديل الصلاحية</h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <label for="edit-permission-name-{{ $permission->id }}" class="form-label fw-bold">الاسم</label>
                                                                        <input type="text" id="edit-permission-name-{{ $permission->id }}" name="name" value="{{ $permission->name }}" class="form-control" placeholder="ادخل اسم الصلاحية" required>
                                                                    </div>
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
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <button type="submit" class="btn btn-danger">حفظ</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $permissions->onEachSide(1)->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal  -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addPermissionModalLabel">إضافة صلاحية</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="permission-name" class="form-label fw-bold">الاسم</label>
                            <input type="text" id="permission-name" name="name" class="form-control" placeholder="ادخل اسم الصلاحية" required>
                        </div>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
