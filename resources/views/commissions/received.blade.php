@extends('layout.app')

@section('title', 'تسليم العمولة')
<!-- Material Icons CDN -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

@section('content')
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            @include('partials.crumb')

            <!-- Start:: row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card p-4 border-0">
                        <div class="page-title mb-4">
                            <h2>تسليم العمولة</h2>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('commissions.deliverStore', $commission->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <!-- طريقة التسليم -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">طريقة التسليم</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="delivery_method" id="delivery_method_code" value="code" required>
                                            <label class="form-check-label" for="delivery_method_code">الكود المستلم</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="delivery_method" id="delivery_method_promo" value="promo" required>
                                            <label class="form-check-label" for="delivery_method_promo">الرمز التسويقي</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- قسم الكود المستلم -->
                                <div class="mb-4" id="code_section" style="display:none;">
                                    <div class="border-box">
                                        <label class="form-label fw-bold">الكود المستلم</label>
                                        <input type="text" name="delivery_code" class="form-control" id="delivery_code_input" placeholder="أدخل الكود المستلم">
                                        <small id="delivery_code_feedback" class="text-danger"></small>
                                    </div>
                                </div>

                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const deliveryCodeInput = document.getElementById('delivery_code_input');
                                    const deliveryCodeFeedback = document.getElementById('delivery_code_feedback');
                                    const submitBtn = document.querySelector('button[type="submit"]');
                                    let deliveryCodeValid = false;

                                    deliveryCodeInput.addEventListener('input', function() {
                                        const code = deliveryCodeInput.value.trim();
                                        if (code.length === 0) {
                                            deliveryCodeFeedback.textContent = '';
                                            deliveryCodeFeedback.classList.remove('text-success', 'text-danger');
                                            deliveryCodeValid = false;
                                            submitBtn.disabled = true;
                                            return;
                                        }
                                        fetch('{{ route("commissions.checkDeliveryCode") }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ delivery_code: code, commission_id: '{{ $commission->id }}' })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.valid) {
                                                deliveryCodeFeedback.textContent = 'الكود صحيح لهذه العمولة.';
                                                deliveryCodeFeedback.classList.remove('text-danger');
                                                deliveryCodeFeedback.classList.add('text-success');
                                                deliveryCodeValid = true;
                                                submitBtn.disabled = false;
                                            } else {
                                                deliveryCodeFeedback.textContent = 'الكود غير صحيح لهذه العمولة.';
                                                deliveryCodeFeedback.classList.remove('text-success');
                                                deliveryCodeFeedback.classList.add('text-danger');
                                                deliveryCodeValid = false;
                                                submitBtn.disabled = true;
                                            }
                                        })
                                        .catch(() => {
                                            deliveryCodeFeedback.textContent = 'حدث خطأ أثناء التحقق من الكود.';
                                            deliveryCodeFeedback.classList.remove('text-success');
                                            deliveryCodeFeedback.classList.add('text-danger');
                                            deliveryCodeValid = false;
                                            submitBtn.disabled = true;
                                        });
                                    });

                                    document.querySelector('form').addEventListener('submit', function(e) {
                                        const codeRadio = document.getElementById('delivery_method_code');
                                        if (codeRadio.checked && !deliveryCodeValid) {
                                            e.preventDefault();
                                        }
                                    });
                                });
                                </script>

                                <!-- قسم الرمز التسويقي -->
                                <div id="promo_section" style="display:none;">
                                    <div class="border-box mb-4">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">الرمز التسويقي</label>
                                                <input type="text" name="promo_code" class="form-control" id="promo_code_input" placeholder="أدخل الرمز التسويقي">
                                                <small id="promo_code_feedback" class="text-danger"></small>
                                            </div>
                                            
                                            <div class="col-md-8 mb-3">
                                                <label for="promo_image" class="form-label fw-bold">
                                                    رفع صورة بتوقيع المسوق <span style="color: #dc3545;">*</span>
                                                </label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input id="promo_image" name="promo_image" type="file" accept="image/*" class="form-control" style="max-width: 250px;">
                                                    <button type="button" id="open-promo-camera" class="btn btn-outline-danger btn-sm" title="تشغيل الكاميرا">
                                                        <i class="material-icons" style="font-size: 1.3rem;">photo_camera</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- منطقة الكاميرا -->
                                        <div class="col-12 mt-3" id="promo-camera-area" style="display:none;">
                                            <video id="promo-camera-preview" autoplay playsinline muted style="width:100%; max-height:350px; border-radius:12px; background:#000"></video>
                                            <div class="mt-3 d-flex gap-2">
                                                <button type="button" id="promo-snap" class="btn btn-success btn-sm"><i class="fas fa-camera me-1"></i> التقاط</button>
                                                <button type="button" id="close-promo-camera" class="btn btn-secondary btn-sm"><i class="fas fa-times me-1"></i> إغلاق</button>
                                            </div>
                                            <canvas id="promo-snapshot" style="display:none;"></canvas>
                                            <div id="promo-shot-preview-wrap" class="mt-3" style="display:none;">
                                                <div class="mb-2 fw-bold">المعاينة:</div>
                                                <img id="promo-shot-preview" alt="Preview" style="max-width:100%; max-height:300px; border:2px solid #ddd; border-radius:10px;" />
                                                <div class="mt-2 text-success fw-bold" id="promo-shot-ok" style="display:none;">تم تجهيز الصورة للإرسال ✅</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const promoCodeInput = document.getElementById('promo_code_input');
                                    const feedback = document.getElementById('promo_code_feedback');

                                    promoCodeInput.addEventListener('blur', function() {
                                        const code = promoCodeInput.value.trim();
                                        if (code.length === 0) {
                                            feedback.textContent = '';
                                            return;
                                        }
                                        fetch('{{ route("commissions.checkPromoCode") }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                promo_code: code,
                                                commission_id: '{{ $commission->id }}'
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.valid) {
                                                feedback.textContent = 'الرمز صحيح ويخص هذه العمولة ويوجد موظف أو مسوق بهذا الرمز.';
                                                feedback.classList.remove('text-danger');
                                                feedback.classList.add('text-success');
                                            } else {
                                                feedback.textContent = 'الرمز غير صحيح أو لا يخص هذه العمولة.';
                                                feedback.classList.remove('text-success');
                                                feedback.classList.add('text-danger');
                                            }
                                        })
                                        .catch(() => {
                                            feedback.textContent = 'حدث خطأ أثناء التحقق من الرمز.';
                                            feedback.classList.remove('text-success');
                                            feedback.classList.add('text-danger');
                                        });
                                    });
                                });
                                </script>

                                <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const openBtn   = document.getElementById('open-promo-camera');
                                    const closeBtn  = document.getElementById('close-promo-camera');
                                    const snapBtn   = document.getElementById('promo-snap');
                                    const area      = document.getElementById('promo-camera-area');
                                    const video     = document.getElementById('promo-camera-preview');
                                    const canvas    = document.getElementById('promo-snapshot');
                                    const imgPrev   = document.getElementById('promo-shot-preview');
                                    const prevWrap  = document.getElementById('promo-shot-preview-wrap');
                                    const shotOk    = document.getElementById('promo-shot-ok');
                                    const attachInput = document.getElementById('promo_image');

                                    let stream = null;
                                    let capturedBlob = null;

                                    function secureOK() {
                                        return location.protocol === 'https:' ||
                                            location.hostname === 'localhost' ||
                                            location.hostname === '127.0.0.1';
                                    }

                                    function disableSnap(state) {
                                        if (!snapBtn) return;
                                        snapBtn.disabled = state;
                                        snapBtn.classList.toggle('disabled', state);
                                    }

                                    async function startCamera() {
                                        if (!navigator.mediaDevices?.getUserMedia) {
                                            alert('المتصفح لا يدعم الكاميرا من الصفحة. استخدم الرفع العادي.');
                                            return;
                                        }
                                        if (!secureOK()) {
                                            alert('لازم تفتح الصفحة عبر HTTPS (أو localhost) لكي تعمل الكاميرا.');
                                            return;
                                        }
                                        area.style.display = '';
                                        prevWrap.style.display = 'none';
                                        shotOk.style.display = 'none';
                                        capturedBlob = null;
                                        disableSnap(true);

                                        const trials = [
                                            { video: { facingMode: { exact: 'environment' } }, audio: false },
                                            { video: { facingMode: { ideal: 'environment' } }, audio: false },
                                            { video: true, audio: false }
                                        ];

                                        let lastErr;
                                        for (const c of trials) {
                                            try {
                                                stream = await navigator.mediaDevices.getUserMedia(c);
                                                break;
                                            } catch (e) {
                                                lastErr = e;
                                            }
                                        }
                                        if (!stream) {
                                            console.error(lastErr);
                                            alert('تعذر تشغيل الكاميرا. اسمح بالإذن وجرب Chrome/Safari عبر HTTPS، أو استخدم الرفع العادي.');
                                            area.style.display = 'none';
                                            return;
                                        }
                                        video.srcObject = stream;
                                        await new Promise(resolve => {
                                            if (video.readyState >= 1) return resolve();
                                            video.onloadedmetadata = () => resolve();
                                        });
                                        try { await video.play(); } catch (e) {}
                                        const t0 = Date.now();
                                        (function waitForFrame() {
                                            if (video.videoWidth > 0 && video.videoHeight > 0) {
                                                disableSnap(false);
                                                return;
                                            }
                                            if (Date.now() - t0 > 4000) {
                                                alert('الكاميرا بدأت لكن الإطار غير جاهز بعد. انتظر ثانية ثم اضغط التقاط.');
                                                disableSnap(false);
                                                return;
                                            }
                                            requestAnimationFrame(waitForFrame);
                                        })();
                                    }

                                    function stopCamera() {
                                        if (stream) {
                                            stream.getTracks().forEach(t => t.stop());
                                            stream = null;
                                        }
                                        area.style.display = 'none';
                                        disableSnap(true);
                                    }

                                    function doSnap() {
                                        if (!video.videoWidth || !video.videoHeight) {
                                            alert('انتظر لحظة حتى يظهر الفيديو ثم أعد المحاولة.');
                                            return;
                                        }
                                        canvas.width  = video.videoWidth;
                                        canvas.height = video.videoHeight;
                                        const ctx = canvas.getContext('2d');
                                        ctx.drawImage(video, 0, 0);

                                        canvas.toBlob((blob) => {
                                            if (!blob) {
                                                alert('تعذر إنشاء الصورة، حاول مرة أخرى.');
                                                return;
                                            }
                                            capturedBlob = blob;
                                            imgPrev.src = URL.createObjectURL(blob);
                                            prevWrap.style.display = '';
                                            shotOk.style.display = '';
                                            area.style.display = 'none';
                                            const file = new File([blob], `promo_signature_${Date.now()}.jpg`, { type: 'image/jpeg' });
                                            const dt = new DataTransfer();
                                            dt.items.add(file);
                                            attachInput.files = dt.files;
                                        }, 'image/jpeg', 0.92);
                                    }

                                    if (openBtn)  openBtn.addEventListener('click', startCamera);
                                    if (closeBtn) closeBtn.addEventListener('click', stopCamera);
                                    if (snapBtn)  snapBtn.addEventListener('click', doSnap);

                                    document.addEventListener('hidden.bs.modal', function (e) {
                                        stopCamera();
                                    });
                                });
                                </script>

                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const codeRadio = document.getElementById('delivery_method_code');
                                    const promoRadio = document.getElementById('delivery_method_promo');
                                    const codeSection = document.getElementById('code_section');
                                    const promoSection = document.getElementById('promo_section');
                                    const deliveryCodeInput = document.getElementById('delivery_code_input');
                                    const promoCodeInput = document.getElementById('promo_code_input');
                                    const promoImageInput = promoSection.querySelector('input[name="promo_image"]');
                                    const feedback = document.getElementById('promo_code_feedback');
                                    const submitBtn = document.querySelector('button[type="submit"]');
                                    let promoCodeValid = false;

                                    function updateSections() {
                                        if (codeRadio.checked) {
                                            codeSection.style.display = 'block';
                                            promoSection.style.display = 'none';
                                            deliveryCodeInput.required = true;
                                            promoCodeInput.required = false;
                                            promoImageInput.required = false;
                                            submitBtn.disabled = false;
                                        } else if (promoRadio.checked) {
                                            codeSection.style.display = 'none';
                                            promoSection.style.display = 'block';
                                            deliveryCodeInput.required = false;
                                            promoCodeInput.required = true;
                                            promoImageInput.required = promoCodeValid;
                                            submitBtn.disabled = !promoCodeValid;
                                        } else {
                                            codeSection.style.display = 'none';
                                            promoSection.style.display = 'none';
                                            deliveryCodeInput.required = false;
                                            promoCodeInput.required = false;
                                            promoImageInput.required = false;
                                            submitBtn.disabled = false;
                                        }
                                    }

                                    codeRadio.addEventListener('change', updateSections);
                                    promoRadio.addEventListener('change', updateSections);

                                    let lastCheckedValue = '';

                                    promoCodeInput.addEventListener('input', function() {
                                        const code = promoCodeInput.value.trim();
                                        if (code.length === 0) {
                                            feedback.textContent = '';
                                            feedback.classList.remove('text-success', 'text-danger');
                                            lastCheckedValue = '';
                                            promoCodeValid = false;
                                            promoImageInput.required = false;
                                            submitBtn.disabled = true;
                                            return;
                                        }
                                        if (code === lastCheckedValue) return;
                                        lastCheckedValue = code;
                                        fetch('{{ route("commissions.checkPromoCode") }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                promo_code: code,
                                                commission_id: '{{ $commission->id }}'
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.valid) {
                                                feedback.textContent = 'الرمز صحيح ويخص هذه العمولة ويوجد موظف أو مسوق بهذا الرمز.';
                                                feedback.classList.remove('text-danger');
                                                feedback.classList.add('text-success');
                                                promoCodeValid = true;
                                                promoImageInput.required = true;
                                                submitBtn.disabled = false;
                                            } else {
                                                feedback.textContent = 'الرمز غير صحيح أو لا يخص هذه العمولة.';
                                                feedback.classList.remove('text-success');
                                                feedback.classList.add('text-danger');
                                                promoCodeValid = false;
                                                promoImageInput.required = false;
                                                submitBtn.disabled = true;
                                            }
                                        })
                                        .catch(() => {
                                            feedback.textContent = 'حدث خطأ أثناء التحقق من الرمز.';
                                            feedback.classList.remove('text-success');
                                            feedback.classList.add('text-danger');
                                            promoCodeValid = false;
                                            promoImageInput.required = false;
                                            submitBtn.disabled = true;
                                        });
                                    });

                                    document.querySelector('form').addEventListener('submit', function(e) {
                                        if (promoRadio.checked && !promoCodeValid) {
                                            e.preventDefault();
                                        }
                                    });

                                    updateSections();
                                });
                                </script>

                                <!-- المبلغ المُسلّم -->
                                <div class="mb-4">
                                    <div class="border-box">
                                        <label class="form-label fw-bold">المبلغ المُسلّم</label>
                                        <input type="number" name="delivered_amount" class="form-control" step="0.01" value="{{ $commission->commission_amount }}" readonly>
                                    </div>
                                </div>

                                <!-- زر التأكيد -->
                                @can('commissions.deliver')
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-check-circle me-2"></i> تأكيد
                                </button>
                                @else
                                <button type="button" class="btn btn-danger btn-lg" disabled>
                                    <i class="fas fa-lock me-2"></i> تأكيد
                                </button>
                                @endcan
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End:: row-1 -->

        </div>
    </div>
    <!-- End::app-content -->

@endsection