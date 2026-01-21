@extends('layout.app')

@section('title', 'مواقع التسويق')

<style>
.breadcrumb .breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb .breadcrumb-item + .breadcrumb-item::before {
    content: "»";
    padding: 0 0.5rem;
    color: #999;
    display: inline-block;
}

/* تصميم الكارد الرئيسي */
.card {
    border: 0;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* عنوان الصفحة */
.page-title h2 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #333;
}

/* صندوق البحث */
.search-box {
    position: relative;
}

.search-box input {
    padding-left: 45px;
}

.search-box .fa-search {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
}

/* الأزرار */
.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

/* الجدول */
.table-danger {
    background-color: #dc3545;
    color: white;
}

.table-danger th {
    border-color: #dc3545;
    font-weight: 600;
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

/* الروابط */
.text-primary {
    color: #007bff !important;
}

.text-decoration-underline {
    text-decoration: underline;
}

/* أزرار العمليات */
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* المودال */
.modal-content {
    border-radius: 0.5rem;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-title {
    font-weight: 600;
    color: #333;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* الزر الثانوي */
.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}
</style>

@section('content')
<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        @include('partials.crumb')

        <!-- Card Container -->
        <div class="card p-4 border-0">
            <div class="commission-section">
                <!-- Page Title -->
                <div class="page-title mb-4">
                    <h2>مواقع التسويق</h2>
                </div>

                <!-- Search & Actions -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <!-- Search Box -->
                    <div class="d-flex gap-2 align-items-center">
                        <div class="search-box position-relative">
                            <input type="text" class="form-control ps-5" placeholder="بحث..." aria-label="بحث" />
                            <i class="fas fa-search position-absolute"
                                style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                        </div>
                        <button class="btn btn-danger">بحث</button>
                    </div>

                    <!-- Add Button -->
                    <div class="d-flex gap-2">
                        @can('sites.create')
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addSiteModal">
                                + إضافة موقع جديد
                            </button>
                        @endcan
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead>
                            <tr class="table-danger">
                                <th>م</th>
                                <th>اسم الموقع الفرعي</th>
                                <th>عدد المسوقين</th>
                                <th>عدد العملاء</th>
                                @canany(['sites.edit', 'sites.delete'])
                                    <th>العمليات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subsites as $sub)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                    <td>{{ $sub->name }}</td>
                                    <td>
                                        @can('marketers.view')
                                            <a href="{{ route('marketers.index', ['site_id' => $site->id, 'subsite_id' => $sub->id]) }}"
                                                class="text-primary fw-bold text-decoration-underline">
                                                {{ $sub->marketers_count }}
                                            </a>
                                        @else
                                            {{ $sub->marketers_count }}
                                        @endcan
                                    </td>
                                    <td>
                                        <a href="#" class="text-primary fw-bold text-decoration-underline">
                                            {{ $sub->clients_count }}
                                        </a>
                                    </td>
                                    @canany(['sites.edit', 'sites.delete'])
                                        <td>
                                            @can('sites.edit')
                                                <button class="btn btn-sm btn-success me-1" title="تعديل"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editSiteModal-{{ $sub->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endcan

                                            @can('sites.delete')
                                                <form action="{{ route('sites.destroy', $sub) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('هل تريد الحذف؟')"
                                                        class="btn btn-sm btn-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    @endcanany
                                </tr>

                                <!-- Edit Site Modal -->
                                @can('sites.edit')
                                    <div class="modal fade" id="editSiteModal-{{ $sub->id }}" tabindex="-1"
                                        aria-labelledby="editSiteModalLabel-{{ $sub->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('sites.update', $sub) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editSiteModalLabel-{{ $sub->id }}">
                                                            تعديل الموقع
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="إغلاق"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="name-{{ $sub->id }}" class="form-label">اسم الموقع</label>
                                                            <input id="name-{{ $sub->id }}" name="name"
                                                                value="{{ $sub->name }}" placeholder="أدخل اسم الموقع"
                                                                class="form-control" required />
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-danger">حفظ التعديلات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Site Modal -->
        @can('sites.create')
            <div class="modal fade" id="addSiteModal" tabindex="-1" aria-labelledby="addSiteModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('sites.store') }}" method="POST" class="geex-content__form">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addSiteModalLabel">إضافة موقع جديد</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                            </div>
                            <div class="modal-body px-4">
                                <div class="row gy-2">
                                    <div class="col-xl-12">
                                        <label for="name" class="form-label">اسم الموقع</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="أدخل اسم الموقع" required>
                                    </div>
                                    <input type="hidden" name="parent_id" value="{{ request()->segment(2) }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-danger">حفظ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

     

    </div>
</div>
<!-- End::app-content -->

@endsection