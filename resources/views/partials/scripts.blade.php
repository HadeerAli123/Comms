<!-- ================== CORE LIBS ================== -->

<!-- jQuery  -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper + Bootstrap  -->
<script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>


<!-- ================== UI HELPERS ================== -->

<!-- Waves Effect -->
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

<!-- Sticky Header -->
<script src="{{ asset('assets/js/sticky.js') }}"></script>

<!-- Simplebar  -->
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const simplebarEl = document.querySelector('[data-simplebar]');
    if (simplebarEl && typeof SimpleBar !== 'undefined') {
        new SimpleBar(simplebarEl);
    }
});
</script>


<!-- ================== FORM PLUGINS ================== -->

<!-- Auto Complete -->
<script src="{{ asset('assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script>

<!-- Color Picker -->
<script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

<!-- Date Picker -->
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>


<!-- ================== CHARTS (SAFE) ================== -->

<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-apex-chart]').forEach(function (el) {
        try {
            const options = JSON.parse(el.dataset.options || '{}');
            new ApexCharts(el, options).render();
        } catch (e) {
            console.warn('ApexChart skipped:', e);
        }
    });
});
</script>


<!-- ================== CUSTOM SCRIPTS ================== -->

<!-- Custom JS  -->
<script src="{{ asset('assets/js/custom.js') }}"></script>

<!-- Theme Switcher -->
<script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script>


<!-- ================== PAGINATION (SAFE) ================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('pagination');
    if (!container) return;

    const totalPages = 50;
    let currentPage = 1;

    function renderPagination() {
        container.innerHTML = '';

        container.appendChild(createButton('‹', currentPage === 1, () => goToPage(currentPage - 1)));

        container.appendChild(createButton('1', false, () => goToPage(1), currentPage === 1));

        if (currentPage > 3) {
            container.appendChild(createDots());
        }

        const start = Math.max(2, currentPage - 1);
        const end = Math.min(totalPages - 1, currentPage + 1);

        for (let i = start; i <= end; i++) {
            container.appendChild(
                createButton(i, false, () => goToPage(i), i === currentPage)
            );
        }

        if (currentPage < totalPages - 2) {
            container.appendChild(createDots());
        }

        container.appendChild(
            createButton(totalPages, false, () => goToPage(totalPages), currentPage === totalPages)
        );

        container.appendChild(createButton('›', currentPage === totalPages, () => goToPage(currentPage + 1)));
    }

    function createButton(text, disabled, onClick, active = false) {
        const btn = document.createElement('button');
        btn.textContent = text;
        btn.className = 'pagination-btn';
        if (active) btn.classList.add('active');
        btn.disabled = disabled;
        if (!disabled) btn.addEventListener('click', onClick);
        return btn;
    }

    function createDots() {
        const span = document.createElement('span');
        span.textContent = '...';
        span.className = 'pagination-dots';
        return span;
    }

    function goToPage(page) {
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderPagination();
        }
    }

    renderPagination();
});
</script>
