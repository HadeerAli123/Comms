
    <!-- محتوى التابات -->
    <div class="tab-content" id="pills-tabContent">
      <div class="tab-pane fade show active" id="all" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-bordered text-center align-middle">
            <thead>
              <tr class="table-danger">
                <th>م</th>
                <th>الموقع</th>
                <th>المسوّق / الموظف</th>
                <th>الرقم التسويقي</th>
                <th>عدد الزوار</th>
                <th class="d-none d-md-table-cell">عدد الأطباق</th>
                <th class="d-none d-md-table-cell">مبلغ العمولة</th>
                <th class="d-none d-md-table-cell">مبلغ الفاتورة</th>
                @if($type === 'all')
                <th class="d-none d-md-table-cell">نوع الزيارة</th>
                @endif
                @if($subTab === 'all')
                <th class="d-none d-md-table-cell">الحالة</th>
                @endif
                <th class="d-none d-md-table-cell">اسم المستخدم</th>
                <th class="d-none d-md-table-cell">تاريخ الإضافة</th>
                @if($subTab === 'all' || $subTab === 'delivered')
                <th class="d-none d-md-table-cell">صورة التوقيع</th>
                @endif
                @if($subTab === 'all' || $subTab === 'pending')
                <th class="d-none d-md-table-cell">تسليم العمولة</th>
                @endif
                <th class="d-md-none">تفاصيل</th>
              </tr>
            </thead>
            <tbody>
              @forelse($commissions as $commission)
              <tr>
                <td class="fw-bold text-muted">
                  @php
                    $iteration = $loop->iteration;
                    if (method_exists($commissions, 'currentPage') && method_exists($commissions, 'perPage')) {
                      $iteration += ($commissions->currentPage() - 1) * $commissions->perPage();
                    }
                  @endphp
                  {{ $iteration }}
                </td>
                <td>{{ $commission->site?->name ?? '—' }}</td>
                <td>
                  {{ $commission->marketer?->name ?? $commission->marketingEmployee?->name ?? '—' }}
                  <br>
                  <small class="text-muted">
                    {{ $commission->marketer?->phone ?? $commission->marketingEmployee?->phone ?? '—' }}
                  </small>
                </td>
                <td>{{ $commission->marketer?->marketing_code ?? $commission->marketingEmployee?->marketing_code ?? '—' }}</td>
                <td>{{ $commission->visitors }}</td>
                <td class="d-none d-md-table-cell">{{ $commission->dishes }}</td>
                <td class="d-none d-md-table-cell">{{ $commission->commission_amount }}</td>
                <td class="d-none d-md-table-cell">{{ $commission->invoice_amount }}</td>
                
                @if($type === 'all')
                <td class="d-none d-md-table-cell">
                  <span class="badge bg-light-danger">
                    {{ $commission->marketer_id ? 'مسوّق' : 'موظف' }}
                  </span>
                </td>
                @endif

                @if($subTab === 'all')
                <td class="d-none d-md-table-cell">
                  @if($commission->received == 1)
                    <span class="badge bg-warning">تم التسليم</span>
                  @else
                    <span class="badge bg-success">لم يتم التسليم</span>
                  @endif
                </td>
                @endif

                <td class="d-none d-md-table-cell">{{ $commission->creator?->name ?? '—' }}</td>
                <td class="d-none d-md-table-cell">{{ $commission->created_at->format('Y-m-d') }}</td>

                @if($subTab === 'all' || $subTab === 'delivered')
                <td class="d-none d-md-table-cell">
                  @if(!empty($commission->promo_image))
                    <a href="#" data-bs-toggle="modal" data-bs-target="#promoImageModal{{ $commission->id }}">
                      <img src="{{ asset('storage/' . $commission->promo_image) }}" alt="صورة التوقيع" height="50" style="object-fit: cover; cursor: pointer;">
                    </a>
                    <!-- Modal -->
                    <div class="modal fade" id="promoImageModal{{ $commission->id }}" tabindex="-1" aria-labelledby="promoImageModalLabel{{ $commission->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="promoImageModalLabel{{ $commission->id }}">صورة التوقيع</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                          </div>
                          <div class="modal-body text-center">
                            <img src="{{ asset('storage/' . $commission->promo_image) }}" alt="صورة التوقيع" class="img-fluid">
                          </div>
                        </div>
                      </div>
                    </div>
                  @else
                    —
                  @endif
                </td>
                @endif

                @if($subTab === 'all' || $subTab === 'pending')
                <td class="d-none d-md-table-cell">
                  @if($commission->received != 1)
                    @can('commissions.deliver')
                    <form action="{{ route('commissions.deliverRequest', $commission->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success">
                        <i class="ri-whatsapp-line"></i> تسليم
                      </button>
                    </form>
                    @else
                    <button type="button" class="btn btn-sm btn-success" disabled>
                      <i class="ri-whatsapp-line"></i> تسليم
                    </button>
                    @endcan
                  @else
                    <span class="badge bg-warning">تم التسليم</span>
                  @endif
                </td>
                @endif

                <td class="d-md-none">
                  <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#detailsRow{{ $commission->id }}" aria-expanded="false" aria-controls="detailsRow{{ $commission->id }}">
                    <i class="fas fa-chevron-down"></i> تفاصيل
                  </button>
                  <div class="collapse mt-2" id="detailsRow{{ $commission->id }}">
                    <div class="card border-0 shadow-sm">
                      <div class="card-body p-2">
                        @if($type === 'all')
                        <div class="mb-2 border-bottom pb-1">
                          <strong>نوع الزيارة:</strong>
                          <span class="badge bg-light-danger ms-1">{{ $commission->marketer_id ? 'مسوّق' : 'موظف' }}</span>
                        </div>
                        @endif
                        @if($subTab === 'all')
                        <div class="mb-2 border-bottom pb-1">
                          <strong>الحالة:</strong>
                          @if($commission->received == 1)
                            <span class="badge bg-warning ms-1">تم التسليم</span>
                          @else
                            <span class="badge bg-success ms-1">لم يتم التسليم</span>
                          @endif
                        </div>
                        @endif
                        <div class="mb-2 border-bottom pb-1">
                          <strong>اسم المستخدم:</strong>
                          <span class="ms-1">{{ $commission->creator?->name ?? '—' }}</span>
                        </div>
                        <div class="mb-2 border-bottom pb-1">
                          <strong>عدد الأطباق:</strong>
                          <span class="ms-1">{{ $commission->dishes }}</span>
                        </div>
                        <div class="mb-2 border-bottom pb-1">
                          <strong>مبلغ العمولة:</strong>
                          <span class="ms-1">{{ $commission->commission_amount }}</span>
                        </div>
                        <div class="mb-2 border-bottom pb-1">
                          <strong>مبلغ الفاتورة:</strong>
                          <span class="ms-1">{{ $commission->invoice_amount }}</span>
                        </div>
                        <div class="mb-2 border-bottom pb-1">
                          <strong>تاريخ الإضافة:</strong>
                          <span class="ms-1">{{ $commission->created_at->format('Y-m-d') }}</span>
                        </div>
                        @if($subTab === 'all' || $subTab === 'delivered')
                        <div class="mb-2 border-bottom pb-1">
                          <strong>صورة التوقيع:</strong>
                          @if(!empty($commission->promo_image))
                            <a href="#" data-bs-toggle="modal" data-bs-target="#promoImageModal{{ $commission->id }}">
                              <img src="{{ asset('storage/' . $commission->promo_image) }}" alt="صورة التوقيع" height="40" style="object-fit: cover; cursor: pointer;" class="ms-1 border rounded">
                            </a>
                          @else
                            <span class="ms-1">—</span>
                          @endif
                        </div>
                        @endif
                        @if($subTab === 'all' || $subTab === 'pending')
                        <div class="mb-2">
                          <strong>تسليم العمولة:</strong>
                          @if($commission->received != 1)
                            @can('commissions.deliver')
                            <form action="{{ route('commissions.deliverRequest', $commission->id) }}" method="POST" class="d-inline ms-1">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-success">
                                <i class="ri-whatsapp-line"></i> تسليم
                              </button>
                            </form>
                            @else
                            <button type="button" class="btn btn-sm btn-success ms-1" disabled>
                              <i class="ri-whatsapp-line"></i> تسليم
                            </button>
                            @endcan
                          @else
                            <span class="badge bg-secondary ms-1">تم التسليم</span>
                          @endif
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="14" class="text-center">لا توجد بيانات</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
          @if(method_exists($commissions, 'links'))
            {{ $commissions->links('pagination::custom') }}
          @endif
        </div>
      </div>
    </div>
  