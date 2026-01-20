@extends('layout.app')

@section('title', 'الدول')

@section('content')
<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        @include('partials.crumb')

        <!-- Start:: row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card p-4 border-0">
                    <div class="commission-section">
                        <div class="page-title mb-4">
                            <h2>الدول</h2>
                        </div>

                        <!-- أدوات البحث و التصدير -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                           
                            
                            @role('Admin')
                            <div class="d-flex gap-2">
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                                    + إضافة دولة جديدة
                                </button>
                            </div>
                            @endrole
                        </div>

                        <!-- Start:: Add Country Modal -->
                        @role('Admin')
                        <div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="addCountryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('countries.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addCountryModalLabel">إضافة دولة جديدة</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="siteName" class="form-label fw-bold">اسم الدولة</label>
                                                <input type="text" class="form-control" id="siteName" name="name" placeholder="أدخل اسم الدولة" required />
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
                        @endrole
                        <!-- End:: Add Country Modal -->

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead>
                                    <tr class="table-danger">
                                        <th>م</th>
                                        <th>اسم الدولة</th>
                                        @role('Admin')
                                        <th>العمليات</th>
                                        @endrole
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($countries as $country)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                        <td>{{ $country->name }}</td>
                                        
                                        @role('Admin')
                                        <td>
                                            <button class="btn btn-sm btn-success me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editCountryModal-{{ $country->id }}" 
                                                    title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <form action="{{ route('countries.destroy', $country) }}" method="POST" class="d-inline">
                                                @csrf 
                                                @method('DELETE')
                                                <button onclick="return confirm('هل تريد الحذف؟')" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                        @endrole
                                    </tr>

                                    <!-- Edit Country Modal -->
                                    @role('Admin')
                                    <div class="modal fade" id="editCountryModal-{{ $country->id }}" tabindex="-1" aria-labelledby="editCountryModalLabel-{{ $country->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('countries.update', $country) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editCountryModalLabel-{{ $country->id }}">تعديل الدولة</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="edit-country-name-{{ $country->id }}" class="form-label fw-bold">اسم الدولة</label>
                                                            <input id="edit-country-name-{{ $country->id }}" 
                                                                   type="text" 
                                                                   name="name" 
                                                                   value="{{ $country->name }}" 
                                                                   placeholder="أدخل اسم الدولة" 
                                                                   class="form-control" 
                                                                   required>
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
                                    @endrole
                                    <!-- End:: Edit Country Modal -->
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $countries->onEachSide(1)->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End:: row-1 -->

    </div>
</div>
<!-- End::app-content -->
@endsection