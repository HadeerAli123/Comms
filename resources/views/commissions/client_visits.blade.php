@extends('layout.app')

@section('title', 'زيارات العملاء')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        @include('partials.crumb')

        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 p-4">
                    <div class="commission-section">
                        <div class="page-title mb-4">
                            <h2>زيارات العملاء</h2>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <form method="GET" action="{{ route('commissions.index') }}" class="d-flex gap-2 align-items-center">
                                    <div class="search-box position-relative">
                                        <input type="text" name="search" value="{{ request('search') }}" 
                                               class="form-control ps-5" placeholder="بحث..." aria-label="بحث" />
                                        <i class="fas fa-search position-absolute" 
                                           style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                                    </div>
                                    <input type="date" name="created_at" value="{{ request('created_at') }}" 
                                           class="form-control" style="max-width: 200px;" />
                                    <button type="submit" class="btn btn-danger">بحث</button>
                                    @if(request('search') || request('created_at'))
                                        <a href="{{ route('commissions.index') }}" class="btn btn-secondary">إلغاء</a>
                                    @endif
                                </form>
                            </div>

                            <div class="d-flex gap-2">
                                @can('commissions.create')
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addSiteModal">
                                    + إضافة زيارة جديدة
                                </button>
                                @endcan
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead>
                                    <tr class="table-danger">
                                        <th>م</th>
                                        <th>الموقع</th>
                                        <th>المسوّق</th>
                                        <th>عدد الزوار</th>
                                        <th>عدد الأطباق</th>
                                        <th>رصيد العمولات الحالية</th>
                                        <th>مبلغ الفاتورة</th>
                                        <th>اسم المستخدم</th>
                                        <th>تاريخ الإضافة</th>
                                        <th>صورة الزيارة</th>
                                        <th>إضافة عمولة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($commissions as $index => $commission)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                        <td>{{ $commission->site?->name ?? '—' }}</td>
                                        <td>
                                            {{ $commission->marketer?->name ?? '—' }}
                                            @if($commission->marketer?->phone)
                                            <br><small class="text-muted">{{ $commission->marketer->phone }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $commission->visitors }}</td>
                                        <td>{{ $commission->dishes }}</td>
                                        <td>{{ number_format($commission->received == 1 ? 0 : $commission->commission_amount, 2) }}</td>
                                        <td>{{ number_format($commission->invoice_amount, 2) }}</td>
                                        <td>{{ $commission->creator?->name ?? '—' }}</td>
                                        <td>
                                            {{ $commission->created_at->format('Y-m-d') }}<br>
                                            <small>{{ $commission->created_at->translatedFormat('h:i A') }}</small>
                                        </td>
                                        <td>
                                            @if($commission->attach)
                                            <img src="{{ asset('storage/' . $commission->attach) }}" 
                                                 alt="صورة الزيارة" 
                                                 height="50" 
                                                 class="open-img" 
                                                 style="cursor: pointer;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal-{{ $commission->id }}">
                                            @else
                                            <span class="text-muted">لا يوجد</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('commissions.complete')
                                            <button class="btn btn-sm btn-success" 
                                                    title="إضافة عمولة"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#commissionModal-{{ $commission->id }}">
                                                إضافة عمولة
                                            </button>
                                            @else
                                            <button class="btn btn-sm btn-success" disabled>
                                                إضافة عمولة
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>

                                    @if($commission->attach)
                                    <div class="modal fade" id="imageModal-{{ $commission->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content bg-transparent border-0 shadow-none">
                                                <div class="modal-body text-center p-0">
                                                    <img src="{{ asset('storage/' . $commission->attach) }}" 
                                                         class="img-fluid rounded" alt="عرض الصورة">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="modal fade" id="commissionModal-{{ $commission->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('commissions.complete', $commission->id) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">إضافة بيانات العمولة - {{ $commission->marketer?->name ?? '—' }}</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">عدد الأطباق</label>
                                                            <input type="number" name="dishes_count" class="form-control" 
                                                                   value="{{ $commission->dishes }}" min="0">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">مبلغ العمولة</label>
                                                            <input type="number" name="commission_amount" class="form-control" 
                                                                   value="{{ $commission->commission_amount }}" min="0" step="0.01">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">مبلغ الفاتورة</label>
                                                            <input type="number" name="invoice_amount" class="form-control" 
                                                                   value="{{ $commission->invoice_amount }}" min="0" step="0.01">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">رقم الفاتورة</label>
                                                            <input type="text" name="invoice_number" class="form-control" 
                                                                   value="{{ $commission->invoice_number }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">صورة الفاتورة</label>
                                                            <input type="file" name="invoice_image" class="form-control" accept="image/*">
                                                            @if($commission->invoice_image)
                                                            <div class="mt-2">
                                                                <a href="{{ asset('storage/' . $commission->invoice_image) }}" 
                                                                   target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-image"></i> عرض الصورة الحالية
                                                                </a>
                                                            </div>
                                                            @endif
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
                                    @empty
                                    <tr>
                                        <td colspan="11" class="text-center">لا توجد بيانات حتى الآن.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $commissions->onEachSide(1)->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addSiteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="commission-create-form" action="{{ route('commissions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">إضافة زيارة جديدة</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center mb-4">
                        <label class="fw-bold mb-2">اختر نوع الشخص:</label>
                        <div class="col-12 d-flex align-items-center gap-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="type_marketer" name="person_type" value="marketer">
                                <label class="form-check-label" for="type_marketer">مسوق</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="type_employee" name="person_type" value="employee">
                                <label class="form-check-label" for="type_employee">موظف</label>
                            </div>
                        </div>
                    </div>

                    <div id="marketer-input-box" class="mb-3" style="display:none;">
                        <label for="marketer_code" class="form-label fw-bold">الرقم التسويقي للمسوق</label>
                        <input id="marketer_code" name="marketer_code" class="form-control" 
                               placeholder="أدخل الرقم التسويقي للمسوق">
                    </div>

                    <div id="employee-input-box" class="mb-3" style="display:none;">
                        <label for="employee_code" class="form-label fw-bold">الرقم التسويقي للموظف</label>
                        <input id="employee_code" name="employee_code" class="form-control" 
                               placeholder="أدخل الرقم التسويقي للموظف">
                    </div>

                    <div id="marketer-details" class="mb-3" style="display:none;"></div>

                    <div id="extra-fields" style="display:none;">
                        <div class="row" id="employee-fields" style="display:none;">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">عدد الأشخاص</label>
                                <input name="visitors_count_employee" type="number" class="form-control" 
                                       placeholder="أدخل عدد الأشخاص" min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">مبلغ الشيك</label>
                                <input name="cheque_amount" type="number" class="form-control" 
                                       placeholder="أدخل مبلغ الشيك" min="0" step="0.01">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">مبلغ الفاتورة</label>
                                <input name="invoice_amount" type="number" class="form-control" 
                                       placeholder="أدخل مبلغ الفاتورة" min="0" step="0.01">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">عدد الأطباق</label>
                                <input name="dishes_count" type="number" class="form-control" 
                                       placeholder="أدخل عدد الأطباق" min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">نسبة الخصم (إن وجدت)</label>
                                <input name="discount_rate" type="number" class="form-control" 
                                       placeholder="أدخل نسبة الخصم" min="0" max="100" step="0.01">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">مبلغ العمولة</label>
                                <input name="commission_amount" type="number" class="form-control" 
                                       placeholder="أدخل مبلغ العمولة" min="0" step="0.01">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">رقم الفاتورة</label>
                                <input name="invoice_number" type="text" class="form-control" 
                                       placeholder="أدخل رقم الفاتورة">
                            </div>
                        </div>

                        <div class="row" id="marketer-fields">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">عدد الأشخاص</label>
                                <input name="visitors_count" type="number" class="form-control" 
                                       placeholder="أدخل عدد الأشخاص" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    رفع صورة الزيارة <span style="color: red;">*</span>
                                </label>
                                <input id="attach_marketer" name="attach" type="file" 
                                       accept="image/*" class="form-control">
                                <div class="mt-2">
                                    <button type="button" id="open-camera" class="btn btn-outline-primary btn-sm">
                                        تشغيل الكاميرا والتقاط صورة
                                    </button>
                                </div>
                                <div class="form-text mt-2 text-danger fw-bold">
                                    يرجى تصوير سائق التاكسي بجانب السيارة مع وضوح لوحة السيارة
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2" id="camera-area" style="display:none;">
                            <video id="camera-preview" autoplay playsinline muted 
                                   style="width:100%; max-height:320px; border-radius:12px; background:#000"></video>
                            <div class="mt-2 d-flex gap-2">
                                <button type="button" id="snap" class="btn btn-success btn-sm">التقاط</button>
                                <button type="button" id="close-camera" class="btn btn-secondary btn-sm">إغلاق</button>
                            </div>
                            <canvas id="snapshot" style="display:none;"></canvas>
                            <div id="shot-preview-wrap" class="mt-2" style="display:none;">
                                <div class="mb-2">المعاينة:</div>
                                <img id="shot-preview" alt="Preview" 
                                     style="max-width:100%; border:1px solid #ddd; border-radius:10px;">
                                <div class="mt-2 text-success" id="shot-ok" style="display:none;">
                                    تم تجهيز الصورة للإرسال ✅
                                </div>
                            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const marketerRadio = document.getElementById('type_marketer');
    const employeeRadio = document.getElementById('type_employee');
    const marketerBox = document.getElementById('marketer-input-box');
    const employeeBox = document.getElementById('employee-input-box');
    const marketerInput = document.getElementById('marketer_code');
    const employeeInput = document.getElementById('employee_code');
    const detailsDiv = document.getElementById('marketer-details');
    const extraFields = document.getElementById('extra-fields');
    const marketerFields = document.getElementById('marketer-fields');
    const employeeFields = document.getElementById('employee-fields');

    // Only show extra fields after marketer/employee is found (to prevent submitting incomplete/invalid data)
    function showMarketer() {
        marketerBox.style.display = 'block';
        employeeBox.style.display = 'none';
        detailsDiv.style.display = 'none';
        extraFields.style.display = 'none';
        marketerFields.style.display = 'flex';
        employeeFields.style.display = 'none';
    }

    function showEmployee() {
        marketerBox.style.display = 'none';
        employeeBox.style.display = 'block';
        detailsDiv.style.display = 'none';
        extraFields.style.display = 'none';
        marketerFields.style.display = 'none';
        employeeFields.style.display = 'flex';
    }

    marketerRadio.addEventListener('change', function() {
        if (this.checked) showMarketer();
    });

    employeeRadio.addEventListener('change', function() {
        if (this.checked) showEmployee();
    });

    marketerInput.addEventListener('input', function() {
        if (!marketerRadio.checked) return;
        const code = this.value.trim();
        if (!code) {
            detailsDiv.style.display = 'none';
            extraFields.style.display = 'none';
            return;
        }
        fetch("{{ url('marketers/details') }}/" + code)
            .then(resp => resp.ok ? resp.json() : Promise.reject())
            .then(data => {
                detailsDiv.innerHTML = `
                    <div class="alert alert-info">
                        <strong>اسم المسوق:</strong> ${data.name}<br>
                        <strong>رقم الهاتف:</strong> ${data.phone}<br>
                        <strong>كود التسويق:</strong> ${data.marketing_code}
                    </div>
                `;
                detailsDiv.style.display = 'block';
                extraFields.style.display = 'block';
            })
            .catch(() => {
                detailsDiv.innerHTML = '<div class="alert alert-danger">لم يتم العثور على المسوق</div>';
                detailsDiv.style.display = 'block';
                extraFields.style.display = 'none';
            });
    });

    // Camera integration allows direct photo capture for verification and fraud prevention
    const openBtn = document.getElementById('open-camera');
    const closeBtn = document.getElementById('close-camera');
    const snapBtn = document.getElementById('snap');
    const area = document.getElementById('camera-area');
    const video = document.getElementById('camera-preview');
    const canvas = document.getElementById('snapshot');
    const attachInput = document.getElementById('attach_marketer');
    let stream = null;

    if (openBtn) {
        openBtn.addEventListener('click', async function() {
            area.style.display = 'block';
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' }, 
                    audio: false 
                });
                video.srcObject = stream;
            } catch (e) {
                alert('تعذر تشغيل الكاميرا');
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }
            area.style.display = 'none';
        });
    }

    if (snapBtn) {
        snapBtn.addEventListener('click', function() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            canvas.toBlob(blob => {
                const file = new File([blob], `visit_${Date.now()}.jpg`, { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(file);
                attachInput.files = dt.files;
                document.getElementById('shot-preview').src = URL.createObjectURL(blob);
                document.getElementById('shot-preview-wrap').style.display = 'block';
                document.getElementById('shot-ok').style.display = 'block';
                area.style.display = 'none';
            }, 'image/jpeg');
        });
    }
});
</script>

@endsection
