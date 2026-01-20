<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>ZYNIX - Admin Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">

    <!-- ================== CSS FILES ================== -->

    <!-- Bootstrap (ONE ONLY) -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Icons -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

    <!-- Theme Style -->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">

    <!-- Node Waves -->
    <link href="{{ asset('assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">

    <!-- Simplebar -->
    <link href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">

    <!-- Flatpickr -->
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">

    <!-- Color Picker -->
    <link rel="stylesheet" href="{{ asset('assets/libs/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Choices -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">

    <!-- Autocomplete -->
    <link rel="stylesheet" href="{{ asset('assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <!-- ================== CUSTOM CSS ================== -->
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        .breadcrumb .breadcrumb-item {
            display: flex;
            align-items: center;
        }

        .breadcrumb .breadcrumb-item + .breadcrumb-item::before {
            content: "»";
            padding: 0 0.5rem;
            color: #999;
        }

        .commission-tabs .nav-link {
            border-radius: 30px;
            padding: 8px 20px;
            margin: 5px;
            font-weight: 600;
            color: #444;
            border: 1px solid #e2e8f0;
            background: #fff;
            transition: all 0.3s ease;
            width: 100%;
        }

        .commission-tabs .nav-link.active {
            background-color: #dc3545 !important;
            color: #fff !important;
            border-color: #dc3545 !important;
            font-weight: 700;
        }

        .nav-pills {
            padding: 0;
        }
    </style>

    <!-- ================== JS FILES ================== -->

    <!-- Choices JS -->
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}" defer></script>

    <!-- Main Theme JS -->
    <script src="{{ asset('assets/js/main.js') }}" defer></script>

    <!-- Safe DOM Script -->
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('button.btn-close[data-bs-dismiss="modal"]').forEach(function (btn) {
                if (btn.nextElementSibling && btn.nextElementSibling.classList.contains('me-auto')) {
                    btn.nextElementSibling.remove();
                }

                const div = document.createElement('div');
                div.className = 'me-auto';
                btn.parentNode.insertBefore(div, btn);
                div.appendChild(btn);
            });
        });
    </script>

</head>
