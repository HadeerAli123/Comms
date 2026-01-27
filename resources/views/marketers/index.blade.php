@extends('layout.app')

@section('title', 'المسوقين')

@section('content')

    @include('partials.crumb')

    <div class="card p-4 border-0 shadow-sm">
        <div class="commission-section">
            <!-- العنوان + زر الإضافة -->
            <div class="page-title mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h2 class="mb-0">المسوقين</h2>

                @can('marketers.create')
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addMarketerModal">
                        + إضافة مسوق جديد
                    </button>
                @endcan
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <form class="d-flex gap-2" method="GET" action="{{ route('marketers.index') }}">
                    <div class="search-box position-relative" style="min-width: 280px;">
                        <input
                            type="text"
                            name="search"
                            class="form-control ps-5"
                            placeholder="ابحث عن مسوق..."
                            value="{{ request('search') }}"
                            aria-label="بحث"
                            id="marketer-search-input"
                        />
                        <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                    </div>

                    <button type="submit" class="btn btn-danger px-4">بحث</button>

                    @if(request('search'))
                        <a href="{{ route('marketers.index') }}" class="btn btn-outline-secondary px-4">
                            إلغاء البحث
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-danger">
                        <tr>
                            <th>م</th>
                            <th>اسم المسوّق</th>
                            <th>الموظف</th>
                            <th>الرقم التسويقي</th>
                            <th>رقم الهاتف</th>
                            <th>الموقع الرئيسي</th>
                            <th>العمولة</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marketers as $marketer)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $marketer->name }}</td>
                                <td>{{ $marketer->employee->name ?? '—' }}</td>
                                <td>{{ $marketer->marketing_code ?? '—' }}</td>
                                <td dir="ltr">{{ $marketer->phone ?? '—' }}</td>
                                <td>{{ $marketer->site->name ?? '—' }}</td>
                                <td>{{ number_format($marketer->commissions_sum ?? 0, 2) }}</td>
                                <td>
                                    @canany(['marketers.edit', 'marketers.delete'])
                                        <div class="d-flex justify-content-center gap-2">
                                            @can('marketers.edit')
                                                <button class="btn btn-sm btn-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editMarketerModal{{ $marketer->id }}"
                                                        title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endcan

                                            @can('marketers.delete')
                                                <form action="{{ route('marketers.destroy', $marketer) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('هل أنت متأكد من حذف هذا المسوق؟')"
                                                            title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    @endcanany
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted fw-bold">
                                    لا يوجد مسوقين حالياً
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $marketers->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    @can('marketers.create')
        <div class="modal fade" id="addMarketerModal" tabindex="-1" aria-labelledby="addMarketerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMarketerModalLabel">إضافة مسوق جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('marketers.store') }}" method="POST">
                        @csrf

                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">اسم المسوق</label>
                                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">رقم الهاتف</label>
                                    <div class="input-group position-relative">
                                        <span class="input-group-text p-0" style="min-width: 110px;">
                                            <img id="selected-flag-img" src="{{ asset('assets/flags/sa.png') }}" alt="flag" style="width: 24px; height: 18px; margin: 0 8px;">
                                            <select name="country_code" id="country-code-select" class="form-select border-0 bg-transparent px-1" style="max-width: 90px;">
                                                @php
                                                    $codes = [
                                                        '+966' => 'sa',
                                                        '+20' => 'eg',
                                                        '+971' => 'ae',
                                                        '+965' => 'kw',
                                                        '+964' => 'iq',
                                                        '+962' => 'jo',
                                                        '+963' => 'sy',
                                                        '+968' => 'om',
                                                        '+973' => 'bh',
                                                        '+974' => 'qa',
                                                    ];
                                                @endphp
                                                @foreach($codes as $code => $flag)
                                                    <option value="{{ $code }}"
                                                            data-flag-src="{{ asset('assets/flags/' . $flag . '.png') }}"
                                                            {{ old('country_code', '+966') == $code ? 'selected' : '' }}>
                                                        {{ $code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </span>
                                        <input type="tel" name="phone" id="geex-input-phone" class="form-control" required
                                               value="{{ old('phone') }}" placeholder="5xxxxxxxx">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <div id="phone-validation-message" class="mt-2 small"></div>
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الموظف</label>
                                    <select name="employee_id" class="form-select" required>
                                        <option value="">اختر الموظف</option>
                                        @foreach($employees as $id => $name)
                                            <option value="{{ $id }}" {{ old('employee_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الموقع الرئيسي</label>
                                    <select name="site_id" id="geex-input-location" class="form-select" required>
                                        <option value="">اختر الموقع</option>
                                        @foreach($sites as $id => $name)
                                            <option value="{{ $id }}" {{ old('site_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('site_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6" id="branch-dropdown-container" style="display: none;">
                                    <label class="form-label fw-bold">الفرع</label>
                                    <select name="branch_id" id="geex-input-branch" class="form-select">
                                        <option value="">اختر الفرع</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger px-4">إضافة المسوق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const countrySelect = document.getElementById('country-code-select');
                const flagImg = document.getElementById('selected-flag-img');

                if (countrySelect && flagImg) {
                    countrySelect.addEventListener('change', function() {
                        const selected = this.options[this.selectedIndex];
                        flagImg.src = selected.getAttribute('data-flag-src');
                    });
                }

                const phoneInput = document.getElementById('geex-input-phone');
                const validationMsg = document.getElementById('phone-validation-message');

                const patterns = {
                    '+966': /^5\d{8}$/,
                    '+20': /^1\d{9}$/,
                    '+971': /^5\d{8}$/,
                    '+965': /^[569]\d{7}$/,
                    '+964': /^7\d{9}$/,
                    '+962': /^7\d{8}$/,
                    '+963': /^9\d{8}$/,
                    '+968': /^9\d{7}$/,
                    '+973': /^3\d{7}$/,
                    '+974': /^3\d{7}$/
                };

                function validatePhone() {
                    if (!phoneInput || !validationMsg) return;

                    const code = countrySelect.value;
                    const phone = phoneInput.value.trim();
                    if (!phone) {
                        validationMsg.textContent = '';
                        return;
                    }
                    const pattern = patterns[code];
                    const isValid = pattern && pattern.test(phone);
                    validationMsg.textContent = isValid ? 'الرقم صحيح ✓' : 'الرقم غير صحيح ✗';
                    validationMsg.style.color = isValid ? 'green' : 'red';
                }

                if (phoneInput && countrySelect) {
                    phoneInput.addEventListener('input', validatePhone);
                    countrySelect.addEventListener('change', validatePhone);
                }

                const siteSelect = document.getElementById('geex-input-location');
                const branchContainer = document.getElementById('branch-dropdown-container');
                const branchSelect = document.getElementById('geex-input-branch');

                function loadBranches(siteId) {
                    if (!siteId || !branchContainer || !branchSelect) {
                        if (branchContainer) branchContainer.style.display = 'none';
                        return;
                    }

                    fetch(`/api/sites/${siteId}/branches`)
                        .then(res => res.json())
                        .then(data => {
                            branchSelect.innerHTML = '<option value="">اختر الفرع</option>';
                            data.forEach(b => {
                                const option = document.createElement('option');
                                option.value = b.id;
                                option.textContent = b.name;
                                branchSelect.appendChild(option);
                            });
                            branchContainer.style.display = data.length > 0 ? 'block' : 'none';
                        })
                        .catch(() => {
                            branchContainer.style.display = 'none';
                        });
                }

                if (siteSelect) {
                    siteSelect.addEventListener('change', () => loadBranches(siteSelect.value));
                }
            });
        </script>
    @endcan

        @can('marketers.edit')
            <div class="modal fade" id="editMarketerModal{{ $marketer->id }}" tabindex="-1" aria-labelledby="editMarketerModalLabel{{ $marketer->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMarketerModalLabel{{ $marketer->id }}">تعديل بيانات المسوق: {{ $marketer->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('marketers.update', $marketer) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">اسم المسوق</label>
                                        <input type="text" name="name" class="form-control" required value="{{ old('name', $marketer->name) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">الرقم التسويقي</label>
                                        <input type="text" name="marketing_code" class="form-control" value="{{ old('marketing_code', $marketer->marketing_code) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">رقم الهاتف</label>
                                        <div class="input-group position-relative">
                                            <span class="input-group-text p-0" style="min-width: 110px;">
                                                @php
                                                    $currentCode = '+966';
                                                    $phoneDigits = $marketer->phone ?? '';
                                                    if (preg_match('/^(\+\d{2,3})/', $phoneDigits, $matches)) {
                                                        $currentCode = $matches[1];
                                                        $phoneDigits = preg_replace('/^\+\d{2,3}/', '', $phoneDigits);
                                                    }
                                                    $flag = ['+966'=>'sa','+20'=>'eg','+971'=>'ae','+965'=>'kw','+964'=>'iq','+962'=>'jo','+963'=>'sy','+968'=>'om','+973'=>'bh','+974'=>'qa'][$currentCode] ?? 'sa';
                                                @endphp
                                                <img id="edit-flag-{{ $marketer->id }}" src="{{ asset("assets/flags/{$flag}.png") }}" alt="flag" style="width: 24px; height: 18px; margin: 0 8px;">
                                                <select name="country_code" id="edit-country-{{ $marketer->id }}" class="form-select border-0 bg-transparent px-1" style="max-width: 90px;">
                                                    @foreach($codes as $code => $flagCode)
                                                        <option value="{{ $code }}"
                                                                data-flag-src="{{ asset('assets/flags/' . $flagCode . '.png') }}"
                                                                {{ $currentCode == $code ? 'selected' : '' }}>
                                                            {{ $code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </span>
                                            <input type="tel" name="phone" id="edit-phone-{{ $marketer->id }}" class="form-control" required
                                                   value="{{ old('phone', $phoneDigits) }}">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <div id="edit-phone-msg-{{ $marketer->id }}" class="mt-2 small"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">الموظف</label>
                                        <select name="employee_id" class="form-select" required>
                                            <option value="">اختر الموظف</option>
                                            @foreach($employees as $id => $name)
                                                <option value="{{ $id }}" {{ old('employee_id', $marketer->employee_id) == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">الموقع الرئيسي</label>
                                        <select name="site_id" id="edit-site-{{ $marketer->id }}" class="form-select" required>
                                            <option value="">اختر الموقع</option>
                                            @foreach($sites as $id => $name)
                                                <option value="{{ $id }}" {{ old('site_id', $marketer->site_id) == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6" id="edit-branch-container-{{ $marketer->id }}" style="{{ $marketer->branch_id ? '' : 'display:none;' }}">
                                        <label class="form-label fw-bold">الفرع</label>
                                        <select name="branch_id" id="edit-branch-{{ $marketer->id }}" class="form-select">
                                            <option value="">اختر الفرع</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-danger px-4">حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const id = '{{ $marketer->id }}';

                    const countrySelectEdit = document.getElementById(`edit-country-${id}`);
                    const flagImgEdit = document.getElementById(`edit-flag-${id}`);

                    if (countrySelectEdit && flagImgEdit) {
                        countrySelectEdit.addEventListener('change', function() {
                            const selected = this.options[this.selectedIndex];
                            flagImgEdit.src = selected.getAttribute('data-flag-src');
                        });
                    }

                    const phoneEdit = document.getElementById(`edit-phone-${id}`);
                    const msgEdit = document.getElementById(`edit-phone-msg-${id}`);

                    const patterns = {
                        '+966': /^5\d{8}$/,
                        '+20': /^1\d{9}$/,
                        '+971': /^5\d{8}$/,
                        '+965': /^[569]\d{7}$/,
                        '+964': /^7\d{9}$/,
                        '+962': /^7\d{8}$/,
                        '+963': /^9\d{8}$/,
                        '+968': /^9\d{7}$/,
                        '+973': /^3\d{7}$/,
                        '+974': /^3\d{7}$/
                    };

                    function validateEditPhone() {
                        if (!phoneEdit || !msgEdit || !countrySelectEdit) return;

                        const code = countrySelectEdit.value;
                        const phone = phoneEdit.value.trim();
                        if (!phone) {
                            msgEdit.textContent = '';
                            return;
                        }
                        const isValid = patterns[code]?.test(phone);
                        msgEdit.textContent = isValid ? 'الرقم صحيح ✓' : 'الرقم غير صحيح ✗';
                        msgEdit.style.color = isValid ? 'green' : 'red';
                    }

                    if (phoneEdit && countrySelectEdit) {
                        phoneEdit.addEventListener('input', validateEditPhone);
                        countrySelectEdit.addEventListener('change', validateEditPhone);
                    }

                    const siteEdit = document.getElementById(`edit-site-${id}`);
                    const branchContEdit = document.getElementById(`edit-branch-container-${id}`);
                    const branchSelectEdit = document.getElementById(`edit-branch-${id}`);

                    function loadEditBranches(siteId, selectedBranch = '{{ $marketer->branch_id ?? '' }}') {
                        if (!siteId || !branchContEdit || !branchSelectEdit) {
                            if (branchContEdit) branchContEdit.style.display = 'none';
                            return;
                        }

                        fetch(`/api/sites/${siteId}/branches`)
                            .then(res => res.json())
                            .then(data => {
                                branchSelectEdit.innerHTML = '<option value="">اختر الفرع</option>';
                                data.forEach(b => {
                                    const opt = document.createElement('option');
                                    opt.value = b.id;
                                    opt.textContent = b.name;
                                    if (b.id == selectedBranch) opt.selected = true;
                                    branchSelectEdit.appendChild(opt);
                                });
                                branchContEdit.style.display = data.length > 0 ? 'block' : 'none';
                            })
                            .catch(() => branchContEdit.style.display = 'none');
                    }

                    if (siteEdit && siteEdit.value) {
                        loadEditBranches(siteEdit.value);
                    }

                    if (siteEdit) {
                        siteEdit.addEventListener('change', () => loadEditBranches(siteEdit.value));
                    }
                });
            </script>
        @endcan
    @endforeach

@endsection
