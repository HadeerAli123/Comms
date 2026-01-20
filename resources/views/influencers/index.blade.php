@extends('layout.app')

@section('title', 'مشاهير الاعلانات')

@section('content')
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            @include('partials.crumb')

            <!-- Start:: row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card border-0 p-4">
                        <div class="commission-section">
                            <div class="page-title mb-4">
                                <h2>مشاهير الاعلانات</h2>
                            </div>

                            <!-- أدوات البحث و التصدير -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                <div class="d-flex gap-2 align-items-center">
                                    <form method="GET" action="{{ route('influencers.index') }}" class="d-flex gap-2" id="marketer-search-form">
                                        <div class="search-box position-relative">
                                            <input
                                                type="text"
                                                name="search"
                                                value="{{ request('search') }}"
                                                class="form-control ps-5"
                                                placeholder="بحث..."
                                                aria-label="بحث"
                                                id="marketer-search-input"
                                                autocomplete="off"
                                            />
                                            <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                                        </div>
                                        <button type="submit" class="btn btn-danger">بحث</button>
                                    </form>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    @role('Admin')
                                    <a class="btn btn-outline-danger" href="{{ route('countries.index') }}">الدول</a>
                                    @endrole
                                    
                                    @can('influencers.create')
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addMarketerModal">
                                        + إضافة مشهور جديد
                                    </button>
                                    @endcan
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    let timer;
                                    const input = document.getElementById('marketer-search-input');
                                    const form = document.getElementById('marketer-search-form');
                                    if(input && form) {
                                        input.addEventListener('input', function () {
                                            clearTimeout(timer);
                                            timer = setTimeout(function () {
                                                form.submit();
                                            }, 500);
                                        });
                                    }
                                });
                            </script>

                            <!-- الجدول -->
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle">
                                    <thead>
                                        <tr class="table-danger">
                                            <th>م</th>
                                            <th>اسم المشهور</th>
                                            <th>الموظف</th>
                                            <th>الدولة</th>
                                            <th>الرصيد الافتتاحي</th>
                                            <th>الرصيد الحالي</th>
                                            <th>الرصيد المسحوب</th>
                                            <th>إجمالي الزيارات</th>
                                            <th>متوسط التقييم (مُعلَن)</th>
                                            <th>اخر عملية تمت</th>
                                            <th>روابط التواصل</th>
                                            <th>ملاحظات</th>
                                            @canany(['influencers.add_visit','influencers.recharge_balance'])
                                            <th>إجراءات</th>
                                            @endcanany
                                            @canany(['influencers.edit','influencers.delete'])
                                            <th>عمليات</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($influencers as $influencer)
                                        <tr>
                                            <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                            <td>{{ $influencer->name }}</td>
                                            <td>{{ $influencer->employee->name ?? '-' }}</td>
                                            <td>{{ $influencer->country->name ?? '-' }}</td>
                                            <td>{{ number_format($influencer->basic_balance, 2) }}</td>
                                            <td>{{ number_format($influencer->balance, 2) }}</td>
                                            <td>{{ number_format(($influencer->basic_balance - $influencer->balance), 2) }}</td>
                                            <td>
                                                <div>
                                                    <span>مُعلَن: {{ $influencer->announced_visits_count ?? 0 }}</span><br>
                                                    <span>غير مُعلَن: {{ $influencer->visits_count - ($influencer->announced_visits_count ?? 0) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if(!is_null($influencer->announced_avg_rating))
                                                    {{ number_format($influencer->announced_avg_rating, 2) }}
                                                    <small class="text-muted">({{ $influencer->announced_visits_count }} زيارة)</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($influencer->latestOperation)
                                                    {{ $influencer->latestOperation->operation_type === 'recharge' ? 'شحن' : 'زيارة' }}
                                                    <br/>{{ $influencer->latestOperation->created_at->format('Y-m-d H:i') }}
                                                @else
                                                    لا يوجد عمليات
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    @if($influencer->ads_link)
                                                        <a href="{{ $influencer->ads_link }}" target="_blank" title="رابط الإعلانات">
                                                            <i class="bi bi-link-45deg fs-5"></i>
                                                        </a>
                                                    @endif
                                                    @if($influencer->whatsapp_link)
                                                        <a href="{{ $influencer->whatsapp_link }}" target="_blank" title="واتساب">
                                                            <i class="bi bi-whatsapp fs-5"></i>
                                                        </a>
                                                    @endif
                                                    @if($influencer->instagram_link)
                                                        <a href="{{ $influencer->instagram_link }}" target="_blank" title="انستجرام">
                                                            <i class="bi bi-instagram fs-5"></i>
                                                        </a>
                                                    @endif
                                                    @if($influencer->tiktok_link)
                                                        <a href="{{ $influencer->tiktok_link }}" target="_blank" title="تيك توك">
                                                            <i class="bi bi-tiktok fs-5"></i>
                                                        </a>
                                                    @endif
                                                    @if($influencer->snap_link)
                                                        <a href="{{ $influencer->snap_link }}" target="_blank" title="سناب شات">
                                                            <i class="bi bi-snapchat fs-5"></i>
                                                        </a>
                                                    @endif
                                                    @if($influencer->phone)
                                                        <a href="tel:{{ $influencer->phone }}" title="اتصال">
                                                            <i class="bi bi-telephone fs-5"></i>
                                                        </a>
                                                    @endif
                                                    @if($influencer->pdf)
                                                        <a href="{{ asset('storage/' . $influencer->pdf) }}" target="_blank" title="ملف PDF">
                                                            <i class="bi bi-file-earmark-pdf fs-5"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $influencer->notes }}</td>
                                            @canany(['influencers.add_visit','influencers.recharge_balance'])
                                            <td>
                                                <div class="d-flex flex-column gap-2">
                                                    @can('influencers.add_visit')
                                                    <button class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#visitModal-{{ $influencer->id }}">
                                                        <i class="bi bi-box-arrow-up-right ms-1"></i> زيارة جديدة
                                                    </button>
                                                    @endcan
                                                    @can('influencers.recharge_balance')
                                                    <button class="btn btn-sm btn-outline-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#chargeModal-{{ $influencer->id }}">
                                                        <i class="bi bi-cash-stack ms-1"></i> شحن رصيد
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                            @endcanany
                                            @canany(['influencers.edit','influencers.delete'])
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @can('influencers.edit')
                                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editInfluencerModal-{{ $influencer->id }}" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @endcan
                                                    @can('influencers.delete')
                                                    <form action="{{ route('influencers.destroy', $influencer) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button onclick="return confirm('هل تريد الحذف؟')" class="btn btn-sm btn-danger" title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                            @endcanany
                                        </tr>

                                        <!-- زيارة جديدة Modal -->
                                        @can('influencers.add_visit')
                                        <div class="modal fade" id="visitModal-{{ $influencer->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('influencers.addVisit', $influencer->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">إضافة زيارة - {{ $influencer->name }}</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">المبلغ</label>
                                                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">عدد الأشخاص</label>
                                                                <input type="number" min="0" name="people_count" class="form-control">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">ملاحظات</label>
                                                                <textarea name="notes" class="form-control" maxlength="1000" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-danger">زيارة</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endcan

                                        <!-- شحن رصيد Modal -->
                                        @can('influencers.recharge_balance')
                                        <div class="modal fade" id="chargeModal-{{ $influencer->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('influencers.chargeBalance', $influencer->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">شحن رصيد - {{ $influencer->name }}</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label class="form-label fw-bold">المبلغ</label>
                                                            <input type="number" step="0.01" min="0" name="charge_amount" class="form-control" required>
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

                                        <!-- Edit Influencer Modal -->
                                        @can('influencers.edit')
                                        <div class="modal fade" id="editInfluencerModal-{{ $influencer->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('influencers.update', $influencer) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تعديل المشهور</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">اسم المشهور</label>
                                                                    <input type="text" name="name" value="{{ $influencer->name }}" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">رابط الإعلانات</label>
                                                                    <input type="url" name="ads_link" value="{{ $influencer->ads_link }}" class="form-control">
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">الدولة</label>
                                                                    <select name="country_id" class="form-control">
                                                                        <option value="" disabled>اختر الدولة</option>
                                                                        @foreach($countries as $country)
                                                                            <option value="{{ $country->id }}" {{ $influencer->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">رابط واتساب</label>
                                                                    <input type="url" name="whatsapp_link" value="{{ $influencer->whatsapp_link }}" class="form-control">
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">رابط انستجرام</label>
                                                                    <input type="url" name="instagram_link" value="{{ $influencer->instagram_link }}" class="form-control">
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">رابط تيك توك</label>
                                                                    <input type="url" name="tiktok_link" value="{{ $influencer->tiktok_link }}" class="form-control">
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">سناب شات</label>
                                                                    <input type="text" name="snap" value="{{ $influencer->snap }}" class="form-control" maxlength="255">
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">رابط سناب شات</label>
                                                                    <input type="url" name="snap_link" value="{{ $influencer->snap_link }}" class="form-control" maxlength="255">
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">رقم الهاتف</label>
                                                                    <input type="text" name="phone" value="{{ $influencer->phone }}" class="form-control" maxlength="20">
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">الموظف</label>
                                                                    <select name="employee_id" class="form-control" required>
                                                                        <option value="" disabled>اختر الموظف</option>
                                                                        @foreach($employees as $id => $emp)
                                                                            <option value="{{ $id }}" {{ $influencer->employee_id == $id ? 'selected' : '' }}>{{ $emp }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">الرصيد</label>
                                                                    <input type="number" name="balance" value="{{ $influencer->balance }}" class="form-control" step="0.01" min="0">
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label fw-bold">ملف PDF</label>
                                                                    <input type="file" name="pdf" class="form-control" accept="application/pdf">
                                                                    <small class="text-muted">الحد الأقصى 20MB</small>
                                                                    @if($influencer->pdf)
                                                                        <div class="mt-2">
                                                                            <a href="{{ asset('storage/' . $influencer->pdf) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                                <i class="bi bi-file-earmark-pdf"></i> عرض الملف الحالي
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label fw-bold">ملاحظات</label>
                                                                    <textarea name="notes" class="form-control" maxlength="2000">{{ $influencer->notes }}</textarea>
                                                                </div>
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
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $influencers->onEachSide(1)->links('vendor.pagination.custom') }}
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

    <!-- Start:: Add Influencer Modal -->
    @can('influencers.create')
    <div class="modal fade" id="addMarketerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('influencers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة مشهور جديد</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">اسم المشهور</label>
                                <input type="text" name="name" placeholder="أدخل اسم المشهور" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">رابط الإعلانات</label>
                                <input type="url" name="ads_link" placeholder="رابط الإعلانات" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">الدولة</label>
                                <select name="country_id" class="form-control">
                                    <option value="" disabled selected>اختر الدولة</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">رابط واتساب</label>
                                <input type="url" name="whatsapp_link" placeholder="رابط واتساب" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">رابط انستجرام</label>
                                <input type="url" name="instagram_link" placeholder="رابط انستجرام" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">رابط تيك توك</label>
                                <input type="url" name="tiktok_link" placeholder="رابط تيك توك" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">سناب شات</label>
                                <input type="text" name="snap" placeholder="سناب شات" class="form-control" maxlength="255">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">رابط سناب شات</label>
                                <input type="url" name="snap_link" placeholder="رابط سناب شات" class="form-control" maxlength="255">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">رقم الهاتف</label>
                                <input type="text" name="phone" placeholder="رقم الهاتف" class="form-control" maxlength="20">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">الموظف</label>
                                <select name="employee_id" class="form-control" required>
                                    <option value="" disabled selected>اختر الموظف</option>
                                    @foreach($employees as $id => $emp)
                                        <option value="{{ $id }}">{{ $emp }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">الرصيد</label>
                                <input type="number" name="balance" placeholder="الرصيد" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">ملف PDF</label>
                                <input type="file" name="pdf" class="form-control" accept="application/pdf">
                                <small class="text-muted">الحد الأقصى 20MB</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">ملاحظات</label>
                                <textarea name="notes" class="form-control" placeholder="ملاحظات" maxlength="2000"></textarea>
                            </div>
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
    <!-- End:: Add Influencer Modal -->

@endsection