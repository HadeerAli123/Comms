@extends('layout.app')

@section('title', 'زيارات المشاهير')

@section('content')

@php
    $user = auth()->user();
    $showActionsColumn = $user && $user->hasAnyRole(['Admin', 'Marketing Manager', 'Employee']);
@endphp

<div class="card p-4 border-0">
    <div class="commission-section">
        <!-- العنوان -->
        <div class="page-title mb-4">
            <h2>زيارات المشاهير</h2>
        </div>

        <!-- أدوات البحث -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <form  class="d-flex gap-2" method="GET" action="{{ route('visits.index') }}">
                <div class="search-box position-relative" style="min-width: 280px;">
                    <input
                        type="text"
                        name="search"
                        class="form-control ps-5"
                        placeholder="ابحث..."
                        value="{{ request('search') }}"
                        aria-label="بحث"
                    />
                    <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                </div>

                <button type="submit" class="btn btn-danger px-4">بحث</button>

                @if(request('search'))
                    <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary px-4">
                        إلغاء
                    </a>
                @endif
            </form>
        </div>


<ul class="nav nav-pills mb-4 commission-tabs" id="visits-tabs" role="tablist">

    <li class="nav-item">
        <button class="nav-link active"
                id="all-tab"
                data-bs-toggle="pill"
                data-bs-target="#all"
                type="button">
            الكل ({{ $visits_all->count() }})
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                id="announced-tab"
                data-bs-toggle="pill"
                data-bs-target="#done"
                type="button">
            تم الإعلان ({{ $visits_done->count() }})
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                id="not-announced-tab"
                data-bs-toggle="pill"
                data-bs-target="#pending"
                type="button">
            لم يتم الإعلان ({{ $visits_pending->count() }})
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                id="pending-tab"
                data-bs-toggle="pill"
                data-bs-target="#not-specified"
                type="button">
            لم يتم التحديد ({{ $visits_not_specified->count() }})
        </button>
    </li>

</ul>

        <!-- Tab Content -->
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle ">
                        <thead class="table-danger">
                            <tr>
                                <th>#</th>
                                <th>المشهور</th>
                                <th>المبلغ</th>
                                <th>عدد الأشخاص</th>
                                <th>الحالة</th>
                                <th class="d-none d-md-table-cell">
                                    <div class="d-flex flex-column align-items-center">
                                        <span>التقييم</span>
                                        <small class="text-muted">من 1 إلى 10</small>
                                    </div>
                                </th>
                                <th class="d-none d-md-table-cell">
                                    <div class="d-flex flex-column align-items-center">
                                        <span>تاريخ الإضافة</span>
                                        <small class="text-muted">YYYY-MM-DD HH:mm</small>
                                    </div>
                                </th>
                                <th class="d-none d-md-table-cell">
                                    <div class="d-flex flex-column align-items-center">
                                        <span>بواسطة</span>
                                        <small class="text-muted">اسم المستخدم</small>
                                    </div>
                                </th>

                                @if($showActionsColumn)
                                    <th class="d-none d-md-table-cell">
                                        <div class="d-flex flex-column align-items-center">
                                            <span>إجراءات</span>
                                            <small class="text-muted">تعديل / إعلان</small>
                                        </div>
                                    </th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($visits as $visit)
                                @php
                                    $isToday = $visit->created_at->isToday();
                                    $canEditToday = $isToday && $user && $user->hasAnyRole(['Admin', 'Marketing Manager', 'Employee']);
                                    $canEditPast  = (! $isToday) && $user && $user->hasRole('Admin');
                                    $canEditThisRow = $canEditToday || $canEditPast;

                                    $toggleRoute = route('visits.toggleStatus', $visit);
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $visit->influencer->name }}</td>
                                    <td>{{ number_format($visit->amount, 2) }}</td>
                                    <td>{{ $visit->people_count ?? '-' }}</td>

                                    <td>
                                        @if($visit->is_announced == 0)
                                            <span class="badge bg-secondary">لم يتم التحديد</span>
                                        @elseif($visit->is_announced == 1)
                                            <span class="badge bg-success mb-2">تم الإعلان</span>

                                        <div class="mt-2 d-flex flex-column align-items-center gap-2">
    <button type="button"
            class="btn btn-sm btn-secondary"
            data-bs-toggle="modal"
            data-bs-target="#detailsModal{{ $visit->id }}">
        اضغط للمشاهدة
    </button>
</div>


                                                <!-- Details Modal -->
                                                <div class="modal fade" id="detailsModal{{ $visit->id }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $visit->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="detailsModalLabel{{ $visit->id }}">تفاصيل الإعلان</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="d-flex flex-column gap-3">
                                                                    @if($visit->media)
                                                                        @php
                                                                            $mediaUrl = asset('storage/' . $visit->media);
                                                                            $mediaExt = pathinfo($visit->media, PATHINFO_EXTENSION);
                                                                        @endphp
                                                                        <div>
                                                                            @if(in_array(strtolower($mediaExt), ['jpg','jpeg','png','gif','webp']))
                                                                                <a href="{{ $mediaUrl }}" target="_blank" class="d-inline-block">
                                                                                    <img src="{{ $mediaUrl }}" alt="مرفق الإعلان" class="rounded shadow-sm border" style="max-width: 150px; max-height: 150px;">
                                                                                </a>
                                                                            @elseif(in_array(strtolower($mediaExt), ['mp4','webm','ogg']))
                                                                                <a href="{{ $mediaUrl }}" target="_blank" class="d-inline-block">
                                                                                    <video src="{{ $mediaUrl }}" class="rounded shadow-sm border" style="max-width: 150px; max-height: 150px;" controls muted></video>
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ $mediaUrl }}" target="_blank" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                                                                    <i class="bi bi-file-earmark me-1"></i> عرض الملف المرفق
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    @endif

                                                                    @if($visit->accept_notes)
                                                                        <div>
                                                                            <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                                                                                <strong>ملاحظة:</strong> {{ $visit->accept_notes }}
                                                                            </span>
                                                                        </div>
                                                                    @endif

                                                                    @if($visit->rating)
                                                                        <div>
                                                                            <span class="badge  text-dark px-3 py-2 fs-6">
                                                                                <strong>التقييم:</strong> {{ $visit->rating }}
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">إغلاق</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($visit->is_announced == 2)
                                            <span class="badge bg-danger">لم يتم الإعلان</span>
                                        @endif
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        @if($visit->is_announced == 1)
                                            <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                                {{ $visit->rating ?? '-' }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="fw-bold">{{ $visit->created_at->format('Y-m-d') }}</span>
                                            <small class="text-muted">{{ $visit->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="fw-bold">{{ $visit->user->name ?? '-' }}</span>
                                            <small class="text-muted">مستخدم</small>
                                        </div>
                                    </td>

                                    @if($showActionsColumn)
                                        <td class="d-none d-md-table-cell">
                                            <div class="d-flex flex-column gap-2 align-items-center">
                                                @if($canEditThisRow)
                                                    @if($visit->is_announced == 0)
                                                        <button type="button" class="btn btn-sm btn-success w-100"
                                                                data-bs-toggle="modal" data-bs-target="#announceModal{{ $visit->id }}">
                                                            تم الإعلان
                                                        </button>

                                                        <button type="submit" name="is_announced" value="2"
                                                                class="btn btn-sm btn-danger w-100"
                                                                form="notAnnouncedForm{{ $visit->id }}">
                                                            لم يتم الإعلان
                                                        </button>

                                                        <form id="notAnnouncedForm{{ $visit->id }}" action="{{ $toggleRoute }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                        </form>

                                                        <!-- Announce Modal -->
                                                        <div class="modal fade" id="announceModal{{ $visit->id }}" tabindex="-1" aria-labelledby="announceModalLabel{{ $visit->id }}" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <form action="{{ $toggleRoute }}" method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="is_announced" value="1">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="announceModalLabel{{ $visit->id }}">تقييم الإعلان</h5>
                                                                            <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">التقييم ( من 1 إلى 10):</label>
                                                                                <div class="d-flex flex-row gap-1 flex-wrap justify-content-center">
                                                                                    @for($i = 1; $i <= 10; $i++)
                                                                                        <input type="radio" class="btn-check" name="rating" id="star{{ $visit->id }}{{ $i }}" value="{{ $i }}" autocomplete="off">
                                                                                        <label class="btn btn-outline-warning" for="star{{ $visit->id }}{{ $i }}">{{ $i }}</label>
                                                                                    @endfor
                                                                                </div>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="accept_notes{{ $visit->id }}" class="form-label">ملاحظة <span class="text-danger">*</span>:</label>
                                                                                <textarea class="form-control" name="accept_notes" id="accept_notes{{ $visit->id }}" rows="2" required></textarea>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label class="form-label">التقاط صورة أو فيديو من الكاميرا:</label>
                                                                                <input type="file" class="form-control" name="media" accept="image/*,video/*" capture="environment">
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-success">تأكيد الإعلان</button>
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @elseif($visit->is_announced == 1)
                                                        <span class="badge bg-success w-100">تم الإعلان</span>
                                                    @elseif($visit->is_announced == 2)
                                                        <span class="badge bg-danger w-100">لم يتم الإعلان</span>
                                                    @endif
                                                @else
                                                    @if($visit->is_announced == 0)
                                                        <span class="badge bg-secondary w-100">لم يتم التحديد</span>
                                                    @elseif($visit->is_announced == 1)
                                                        <span class="badge bg-success w-100">تم الإعلان</span>
                                                    @elseif($visit->is_announced == 2)
                                                        <span class="badge bg-danger w-100">لم يتم الإعلان</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    @endif

                                    <!-- الجوال -->
                                    <td class="d-md-none">
                                        <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#visitDetails{{ $visit->id }}" aria-expanded="false" aria-controls="visitDetails{{ $visit->id }}">
                                            <i class="bi bi-chevron-down"></i> تفاصيل
                                        </button>
                                        <div class="collapse mt-2" id="visitDetails{{ $visit->id }}">
                                            <ul class="list-group text-end">
                                                <li class="list-group-item">
                                                    <strong>التقييم:</strong>
                                                    @if($visit->is_announced == 1)
                                                        <span class="badge bg-warning text-dark">{{ $visit->rating ?? '-' }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>تاريخ الإضافة:</strong>
                                                    <span>{{ $visit->created_at->format('Y-m-d H:i') }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>بواسطة:</strong>
                                                    <span>{{ $visit->user->name ?? '-' }}</span>
                                                </li>
                                                @if($showActionsColumn)
                                                    @php
                                                        $isToday_m = $visit->created_at->isToday();
                                                        $canEditToday_m = $isToday_m && $user && $user->hasAnyRole(['Admin', 'Marketing Manager', 'Employee']);
                                                        $canEditPast_m  = (! $isToday_m) && $user && $user->hasRole('Admin');
                                                        $canEditThisRow_m = $canEditToday_m || $canEditPast_m;
                                                    @endphp
                                                    <li class="list-group-item">
                                                        <strong>إجراءات:</strong>
                                                        <div class="d-flex flex-column gap-2 mt-2">
                                                            @if($canEditThisRow_m)
                                                                @if($visit->is_announced == 0)
                                                                    <button type="button" class="btn btn-sm btn-success w-100"
                                                                            data-bs-toggle="modal" data-bs-target="#announceModal{{ $visit->id }}">
                                                                        تم الإعلان
                                                                    </button>
                                                                    <button type="submit" name="is_announced" value="2"
                                                                            class="btn btn-sm btn-danger w-100"
                                                                            form="notAnnouncedForm{{ $visit->id }}">
                                                                        لم يتم الإعلان
                                                                    </button>
                                                                @elseif($visit->is_announced == 1)
                                                                    <span class="badge bg-success w-100">تم الإعلان</span>
                                                                @elseif($visit->is_announced == 2)
                                                                    <span class="badge bg-danger w-100">لم يتم الإعلان</span>
                                                                @endif
                                                            @else
                                                                @if($visit->is_announced == 0)
                                                                    <span class="badge bg-secondary w-100">لم يتم التحديد</span>
                                                                @elseif($visit->is_announced == 1)
                                                                    <span class="badge bg-success w-100">تم الإعلان</span>
                                                                @elseif($visit->is_announced == 2)
                                                                    <span class="badge bg-danger w-100">لم يتم الإعلان</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $showActionsColumn ? 9 : 8 }}" class="text-center py-5">
                                        لا توجد بيانات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- التبويبات الأخرى حالياً فارغة (يمكن تطويرها لاحقاً) -->
            <div class="tab-pane fade" id="announced" role="tabpanel">
                <div class="text-center py-5 text-muted">جاري عرض الزيارات التي تم الإعلان عنها...</div>
            </div>
            <div class="tab-pane fade" id="not-announced" role="tabpanel">
                <div class="text-center py-5 text-muted">جاري عرض الزيارات التي لم يتم الإعلان عنها...</div>
            </div>
            <div class="tab-pane fade" id="pending" role="tabpanel">
                <div class="text-center py-5 text-muted">جاري عرض الزيارات التي لم يتم تحديدها...</div>
            </div>
        </div>
    </div>
</div>

@endsection