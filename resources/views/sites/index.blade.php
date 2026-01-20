@extends('layout.app')

@section('title', 'مواقع التسويق')

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
                        <form method="GET" action="{{ route('sites.index') }}" class="d-flex gap-2" id="site-search-form">
                            <div class="search-box position-relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control ps-5" placeholder="بحث..." aria-label="بحث"
                                    id="site-search-input" autocomplete="off" />
                                <i class="fas fa-search position-absolute"
                                    style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                            </div>
                            <button type="submit" class="btn btn-danger">بحث</button>
                            @if (request('search'))
                                <a href="{{ route('sites.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            @endif
                        </form>
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
                @can('sites.view')
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead>
                                <tr class="table-danger">
                                    <th>م</th>
                                    <th>اسم الموقع</th>
                                    <th>المواقع الفرعية</th>
                                    <th>المسوقون</th>
                                    <th>العملاء</th>
                                    @canany(['sites.edit', 'sites.delete'])
                                        <th>العمليات</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sites as $site)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                        <td>{{ $site->name }}</td>
                                        <td>
                                            @can('sites.view')
                                                <a href="{{ route('sites.subsites.index', $site) }}"
                                                    class="text-primary fw-bold text-decoration-underline">
                                                    {{ $site->subs_count }}
                                                </a>
                                            @else
                                                {{ $site->subs_count }}
                                            @endcan
                                        </td>
                                        <td>
                                            @can('marketers.view')
                                                <a href="{{ route('marketers.index', ['site_id' => $site->id]) }}"
                                                    class="text-primary fw-bold text-decoration-underline">
                                                    {{ $site->marketers_count }}
                                                </a>
                                            @else
                                                {{ $site->marketers_count }}
                                            @endcan
                                        </td>
                                        <td>
                                            <a href="#" class="text-primary fw-bold text-decoration-underline">
                                                {{ $site->clients_count }}
                                            </a>
                                        </td>
                                        @canany(['sites.edit', 'sites.delete'])
                                            <td>
                                                @can('sites.edit')
                                                    <button class="btn btn-sm btn-success me-1" title="تعديل"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editSiteModal-{{ $site->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endcan

                                                @can('sites.delete')
                                                    <form action="{{ route('sites.destroy', $site) }}" method="POST"
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
                                        <div class="modal fade" id="editSiteModal-{{ $site->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('sites.update', $site) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تعديل الموقع</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="name-{{ $site->id }}"
                                                                    class="form-label fw-bold">اسم الموقع</label>
                                                                <input type="text" class="form-control"
                                                                    id="name-{{ $site->id }}"
                                                                    placeholder="أدخل اسم الموقع" name="name"
                                                                    value="{{ $site->name }}" />
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-danger">حفظ
                                                                التعديلات</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            لا توجد مواقع لعرضها
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $sites->onEachSide(1)->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                @else
                    @include('partials.403')
                @endcan
            </div>
        </div>

        <!-- Add Site Modal -->
        @can('sites.create')
            <div class="modal fade" id="addSiteModal" tabindex="-1" aria-labelledby="addSiteModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('sites.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addSiteModalLabel">إضافة موقع جديد</h5>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="siteName" class="form-label fw-bold">اسم الموقع</label>
                                    <input type="text" class="form-control" id="siteName" name="name"
                                        placeholder="أدخل اسم الموقع" required />
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

        <!-- Copyright -->
        <div class="text-center mt-5 pt-3 text-muted">
            <p>Copyright © 2025 All rights reserved</p>
        </div>

    </div>
</div>

<!-- Auto Search Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timer;
        const input = document.getElementById('site-search-input');
        const form = document.getElementById('site-search-form');
        
        if (input && form) {
            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    form.submit();
                }, 500);
            });
        }
    });
</script>

@endsection