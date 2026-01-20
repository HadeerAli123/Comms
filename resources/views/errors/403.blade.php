<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 | غير مسموح</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-100 via-white to-blue-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-2xl rounded-3xl px-12 py-14 max-w-lg w-full text-center border-t-8 border-red-500">
        <div class="flex flex-col items-center">
            <div class="bg-red-50 rounded-full p-7 mb-6 shadow-md">
                <svg class="w-20 h-20 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5" fill="none"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2.5"/>
                </svg>
            </div>
            <h1 class="text-7xl font-black text-red-500 mb-2">403</h1>
            <h2 class="text-2xl font-bold text-gray-800 mb-3">غير مسموح</h2>
            <p class="text-gray-600 mb-8 leading-relaxed">عذراً، لا تملك الصلاحية للدخول على هذه الصفحة.</p>
        </div>
        <div class="flex justify-center gap-6">
            <a href="{{ url()->previous() }}"
               class="px-7 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2 shadow">
                <span class="text-lg">🔙</span> رجوع
            </a>
            <a href="{{ url('/dashboard') }}"
               class="px-7 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-800 transition flex items-center gap-2 shadow">
                <span class="text-lg">🏠</span> الرئيسية
            </a>
        </div>
    </div>
</body>
</html>
