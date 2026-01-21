@extends('layout.app')

@section('title', 'موظفين التسويق')

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
                            <!-- Page Title -->
                            <div class="page-title mb-4">
                                <h2>موظفين التسويق</h2>
                            </div>

                            <!-- Search and Actions Bar -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                <!-- Search Section -->
                                <div class="d-flex gap-2 align-items-center">
                                    <form method="GET" action="{{ route('marketing-employees.index') }}" class="d-flex gap-2 align-items-center" id="employee-search-form">
                                        <div class="search-box position-relative">
                                            <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5" placeholder="بحث..." aria-label="بحث" id="employee-search-input" autocomplete="off" />
                                            <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                                        </div>
                                        <button type="submit" class="btn btn-danger">بحث</button>
                                        @if(request('search'))
                                            <a href="{{ route('marketing-employees.index') }}" class="btn btn-secondary">إلغاء</a>
                                        @endif
                                    </form>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        let timer;
                                        const input = document.getElementById('employee-search-input');
                                        const form = document.getElementById('employee-search-form');
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

                                <!-- Actions Section -->
                                <div class="d-flex gap-2">
                                    @can('roles.view')
                                        <a href="{{ route('roles.index') }}" class="btn btn-outline-danger">
                                            مجموعات العمل
                                        </a>
                                    @endcan
                                    @can('marketing-employees.create')
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addMarketerModal">
                                            + إضافة موظف جديد
                                        </button>
                                    @endcan
                                </div>
                            </div>

                            <!-- Table Section -->
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle">
                                    <thead>
                                        <tr class="table-danger">
                                            <th>م</th>
                                            <th>اسم المستخدم</th>
                                            <th>اسم موظف التسويق</th>
                                            <th>المجموعات</th>
                                            <th>الرقم التسويقي</th>
                                            <th>عدد المسوقين</th>
                                            <th>إجمالي عمولات المسوقين</th>
                                            <th>مبلغ العمولة</th>
                                            <th>البريد الإلكتروني</th>
                                            <th>النسبة</th>
                                            @canany(['marketing-employees.update','marketing-employees.delete'])
                                                <th>العمليات</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $emp)
                                            <tr>
                                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                                <td>{{ $emp->username }}</td>
                                                <td>
                                                    {{ $emp->name }}
                                                    @php
                                                        $phone = $emp->phone;
                                                        $countryCodes = [
                                                            '+966' => 'sa', '+20' => 'eg', '+971' => 'ae', '+965' => 'kw',
                                                            '+964' => 'iq', '+962' => 'jo', '+963' => 'sy', '+968' => 'om',
                                                            '+973' => 'bh', '+974' => 'qa',
                                                        ];
                                                        $countryCode = '';
                                                        $phoneNumber = $phone;
                                                        $flag = 'sa';
                                                        if (preg_match('/^(\+966|\+20|\+971|\+965|\+964|\+962|\+963|\+968|\+973|\+974)(\d{7,})$/', $phone, $matches)) {
                                                            $countryCode = $matches[1];
                                                            $phoneNumber = $matches[2];
                                                            $flag = $countryCodes[$countryCode] ?? 'sa';
                                                        }
                                                    @endphp
                                                    <br><small class="text-muted">{{ $countryCode }}{{ $phoneNumber }}</small>
                                                </td>
                                                <td>
                                                    @foreach($emp->roles as $role)
                                                        <div class="bg-danger-subtle rounded-2 fw-bold mb-1 p-1">
                                                            {{ __("roles." . $role->name) ?? $role->name }}
                                                        </div>
                                                    @endforeach
                                                </td>
                                                <td>{{ $emp->marketing_code ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('marketers.index', ['employee_id' => $emp->id]) }}">
                                                        {{ $emp->marketers_count ?? 0 }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('marketers.index', ['employee_id' => $emp->id]) }}">
                                                        {{ number_format($emp->commissions_sum_commission_amount ?? 0, 2) }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @php
                                                        $total = $emp->commissions_sum_commission_amount ?? 0;
                                                        $percentage = $emp->prec ?? 0;
                                                        $after_percentage = $total * ($percentage / 100);
                                                    @endphp
                                                    {{ number_format($after_percentage, 2) }}
                                                </td>
                                                <td>{{ $emp->email }}</td>
                                                <td>{{ $emp->prec }}%</td>
                                                @canany(['marketing-employees.update','marketing-employees.delete'])
                                                    <td>
                                                        @can('marketing-employees.update')
                                                            <button class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#editEmployeeModal-{{ $emp->id }}" title="تعديل">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endcan
                                                        @can('marketing-employees.delete')
                                                            <form action="{{ route('marketing-employees.destroy', $emp) }}" method="POST" class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-sm btn-danger" onclick="return confirm('هل تريد الحذف؟')" title="حذف">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </td>
                                                @endcanany

                                                <!-- Edit Modal for each employee -->
                                                @can('marketing-employees.update')
                                                <div class="modal fade" id="editEmployeeModal-{{ $emp->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <form action="{{ route('marketing-employees.update', $emp) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">تعديل بيانات موظف التسويق</h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label fw-bold">اسم المستخدم</label>
                                                                            <input type="text" name="username" value="{{ $emp->username }}" class="form-control" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label fw-bold">الاسم</label>
                                                                            <input type="text" name="name" value="{{ $emp->name }}" class="form-control" required>
                                                                        </div>
                                                                        <div class="col-md-12 mb-2">
                                                                            <label for="roles-edit-{{ $emp->id }}" class="form-label fw-bold">المجموعات</label>
                                                                            <select name="roles[]" id="roles-edit-{{ $emp->id }}" class="form-select" multiple>
                                                                                @foreach($roles as $role)
                                                                                    <option value="{{ $role->name }}" {{ $emp->roles->contains('name', $role->name) ? 'selected' : '' }}>
                                                                                        {{ $role->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label fw-bold">كلمة المرور الجديدة (اختياري)</label>
                                                                            <input type="password" name="password" class="form-control" placeholder="أدخل كلمة المرور الجديدة">
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label fw-bold">تأكيد كلمة المرور</label>
                                                                            <input type="password" name="password_confirmation" class="form-control" placeholder="أدخل تأكيد كلمة المرور">
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label fw-bold">البريد الإلكتروني</label>
                                                                            <input type="email" name="email" value="{{ $emp->email }}" class="form-control" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label fw-bold">رقم الهاتف</label>
                                                                            <div class="input-group">
                                                                                @php
                                                                                    $countryCodes = ['+966' => 'sa', '+20' => 'eg', '+971' => 'ae', '+965' => 'kw', '+964' => 'iq', '+962' => 'jo', '+963' => 'sy', '+968' => 'om', '+973' => 'bh', '+974' => 'qa'];
                                                                                    $countryCode = '+966';
                                                                                    $phone = $emp->phone;
                                                                                    if (preg_match('/^(\+?\d{2,3})(\d{8,})$/', $phone, $matches)) {
                                                                                        $possibleCode = $matches[1][0] === '+' ? $matches[1] : ('+' . $matches[1]);
                                                                                        if (array_key_exists($possibleCode, $countryCodes)) {
                                                                                            $countryCode = $possibleCode;
                                                                                            $phone = $matches[2];
                                                                                        }
                                                                                    }
                                                                                @endphp
                                                                                <span class="input-group-text p-0" style="min-width: 110px;">
                                                                                    <img id="edit-selected-flag-img-{{ $emp->id }}" src="{{ asset('assets/flags/' . ($countryCodes[$countryCode] ?? 'sa') . '.png') }}" alt="flag" style="width: 24px; height: 18px; margin-right: 5px;">
                                                                                    <select name="country_code" id="edit-country-code-select-{{ $emp->id }}" class="form-select border-0 bg-transparent px-2" style="width: 80px;" required>
                                                                                        @foreach($countryCodes as $code => $flag)
                                                                                            <option value="{{ $code }}" data-flag-src="{{ asset('assets/flags/' . $flag . '.png') }}" {{ $countryCode == $code ? 'selected' : '' }}>{{ $code }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </span>
                                                                                <input id="edit-input-phone-{{ $emp->id }}" type="text" name="phone" value="{{ preg_replace('/^(\+966|\+20|\+971|\+965|\+964|\+962|\+963|\+968|\+973|\+974)/', '', $phone) }}" class="form-control" required>
                                                                            </div>
                                                                            <div id="edit-phone-validation-message-{{ $emp->id }}" class="mt-2"></div>
                                                                        </div>
                                                                        <script>
                                                                            document.addEventListener('DOMContentLoaded', function () {
                                                                                const select = document.getElementById('edit-country-code-select-{{ $emp->id }}');
                                                                                const flagImg = document.getElementById('edit-selected-flag-img-{{ $emp->id }}');
                                                                                const phoneInput = document.getElementById('edit-input-phone-{{ $emp->id }}');
                                                                                const msgDiv = document.getElementById('edit-phone-validation-message-{{ $emp->id }}');

                                                                                function updateFlag() {
                                                                                    const selectedOption = select.options[select.selectedIndex];
                                                                                    const flagSrc = selectedOption.getAttribute('data-flag-src');
                                                                                    if(flagSrc) flagImg.src = flagSrc;
                                                                                }

                                                                                function validatePhone() {
                                                                                    const countryCode = select.value;
                                                                                    const phone = phoneInput.value.trim();
                                                                                    let valid = false;
                                                                                    let regex;
                                                                                    switch(countryCode) {
                                                                                        case '+966': regex = /^5\d{8}$/; break;
                                                                                        case '+20': regex = /^1\d{9}$/; break;
                                                                                        case '+971': regex = /^5\d{8}$/; break;
                                                                                        case '+965': regex = /^[569]\d{7}$/; break;
                                                                                        case '+964': regex = /^7\d{9}$/; break;
                                                                                        case '+962': regex = /^7\d{8}$/; break;
                                                                                        case '+963': regex = /^9\d{8}$/; break;
                                                                                        case '+968': regex = /^9\d{7}$/; break;
                                                                                        case '+973': regex = /^3\d{7}$/; break;
                                                                                        case '+974': regex = /^3\d{7}$/; break;
                                                                                        default: regex = /^\d+$/;
                                                                                    }
                                                                                    if (regex.test(phone)) valid = true;
                                                                                    if (phone.length === 0) {
                                                                                        msgDiv.textContent = '';
                                                                                        msgDiv.classList.remove('text-success', 'text-danger');
                                                                                    } else if (valid) {
                                                                                        msgDiv.textContent = 'الرقم صحيح';
                                                                                        msgDiv.classList.add('text-success');
                                                                                        msgDiv.classList.remove('text-danger');
                                                                                    } else {
                                                                                        msgDiv.textContent = 'الرقم غير صحيح';
                                                                                        msgDiv.classList.add('text-danger');
                                                                                        msgDiv.classList.remove('text-success');
                                                                                    }
                                                                                }

                                                                                select.addEventListener('change', function() {
                                                                                    updateFlag();
                                                                                    validatePhone();
                                                                                });
                                                                                phoneInput.addEventListener('input', validatePhone);
                                                                                updateFlag();
                                                                                validatePhone();
                                                                            });
                                                                        </script>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label fw-bold">النسبة</label>
                                                                            <input type="number" name="percentage" value="{{ $emp->prec }}" class="form-control" min="0" max="100" step="0.01" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label fw-bold">مبلغ عمولة الطاولة</label>
                                                                            <input type="number" name="table_commission" value="{{ $emp->table_commission }}" class="form-control" min="0" step="0.01" required>
                                                                            <span class="text-danger small">عمولة الموظف على الطاولة</span>
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
                                                @endcan
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $employees->onEachSide(1)->links('vendor.pagination.custom') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End:: row-1 -->

            <!-- Add Employee Modal -->
            @can('marketing-employees.create')
            <div class="modal fade" id="addMarketerModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('marketing-employees.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">إضافة موظف تسويق جديد</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-bold">اسم المستخدم</label>
                                        <input type="text" name="username" class="form-control" placeholder="اسم المستخدم" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-bold">الاسم</label>
                                        <input type="text" name="name" class="form-control" placeholder="الاسم" required>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label for="groups" class="form-label fw-bold">المجموعات</label>
                                        <select name="roles[]" id="groups" class="form-select" multiple required>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-bold">كلمة المرور</label>
                                        <input type="password" name="password" class="form-control" placeholder="كلمة المرور" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-bold">تأكيد كلمة المرور</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="تأكيد كلمة المرور" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-bold">البريد الإلكتروني</label>
                                        <input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="geex-input-phone" class="form-label fw-bold">رقم الهاتف</label>
                                        <div class="input-group">
                                            <span class="input-group-text p-0" style="min-width: 110px;">
                                                <img id="selected-flag-img" src="{{ asset('assets/flags/sa.png') }}" alt="flag" style="width: 24px; height: 18px; margin-right: 5px;">
                                                <select name="country_code" id="country-code-select" class="form-select border-0 bg-transparent px-2" style="width: 80px;" required>
                                                    <option value="+966" selected data-flag-src="{{ asset('assets/flags/sa.png') }}">+966</option>
                                                    <option value="+20" data-flag-src="{{ asset('assets/flags/eg.png') }}">+20</option>
                                                    <option value="+971" data-flag-src="{{ asset('assets/flags/ae.png') }}">+971</option>
                                                    <option value="+965" data-flag-src="{{ asset('assets/flags/kw.png') }}">+965</option>
                                                    <option value="+964" data-flag-src="{{ asset('assets/flags/iq.png') }}">+964</option>
                                                    <option value="+962" data-flag-src="{{ asset('assets/flags/jo.png') }}">+962</option>
                                                    <option value="+963" data-flag-src="{{ asset('assets/flags/sy.png') }}">+963</option>
                                                    <option value="+968" data-flag-src="{{ asset('assets/flags/om.png') }}">+968</option>
                                                    <option value="+973" data-flag-src="{{ asset('assets/flags/bh.png') }}">+973</option>
                                                    <option value="+974" data-flag-src="{{ asset('assets/flags/qa.png') }}">+974</option>
                                                </select>
                                            </span>
                                            <input id="geex-input-phone" type="text" name="phone" placeholder="رقم الهاتف" class="form-control" required>
                                        </div>
                                        <span id="phone-validity-msg" class="mt-2 d-block"></span>
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const select = document.getElementById('country-code-select');
                                            const flagImg = document.getElementById('selected-flag-img');
                                            const phoneInput = document.getElementById('geex-input-phone');
                                            const msgSpan = document.getElementById('phone-validity-msg');

                                            function updateFlag() {
                                                const selectedOption = select.options[select.selectedIndex];
                                                const flagSrc = selectedOption.getAttribute('data-flag-src');
                                                if(flagSrc) flagImg.src = flagSrc;
                                            }

                                            function validatePhone() {
                                                const countryCode = select.value;
                                                const phone = phoneInput.value.trim();
                                                let valid = false;
                                                let regex;
                                                switch(countryCode) {
                                                    case '+966': regex = /^5\d{8}$/; break;
                                                    case '+20': regex = /^1\d{9}$/; break;
                                                    case '+971': regex = /^5\d{8}$/; break;
                                                    case '+965': regex = /^[569]\d{7}$/; break;
                                                    case '+964': regex = /^7\d{9}$/; break;
                                                    case '+962': regex = /^7\d{8}$/; break;
                                                    case '+963': regex = /^9\d{8}$/; break;
                                                    case '+968': regex = /^9\d{7}$/; break;
                                                    case '+973': regex = /^3\d{7}$/; break;
                                                    case '+974': regex = /^3\d{7}$/; break;
                                                    default: regex = /^\d+$/;
                                                }
                                                if (regex.test(phone)) valid = true;
                                                if (phone.length === 0) {
                                                    msgSpan.textContent = '';
                                                    msgSpan.classList.remove('text-success', 'text-danger');
                                                } else if (valid) {
                                                    msgSpan.textContent = 'الرقم صحيح';
                                                    msgSpan.classList.add('text-success');
                                                    msgSpan.classList.remove('text-danger');
                                                } else {
                                                    msgSpan.textContent = 'الرقم غير صحيح';
                                                    msgSpan.classList.add('text-danger');
                                                    msgSpan.classList.remove('text-success');
                                                }
                                            }

                                            select.addEventListener('change', function() {
                                                updateFlag();
                                                validatePhone();
                                            });
                                            phoneInput.addEventListener('input', validatePhone);
                                            updateFlag();
                                            validatePhone();
                                        });
                                    </script>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-bold">النسبة</label>
                                        <input type="number" name="percentage" class="form-control" placeholder="النسبة" min="0" max="100" step="0.01" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-bold">مبلغ عمولة الطاولة</label>
                                        <input type="number" name="table_commission" class="form-control" placeholder="مبلغ عمولة الطاولة" min="0" step="0.01" required>
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
            @endcan

           
        </div>
    </div>
    <!-- End::app-content -->

@endsection