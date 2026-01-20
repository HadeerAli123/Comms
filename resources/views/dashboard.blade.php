@extends('layout.app')

@section('title', 'لوحة التحكم')

@section('content')
<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        @include('partials.crumb')

        <!-- Welcome Section -->
        <div class="welcome-section d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-2 fw-bold text-danger mt-5">مــــــــــــــرحـــبًــا بــك {{ auth()->user()->name ?? 'ادمن' }}</h1>
                <h4 class="text-muted">
                    هنا يمكنك متابعة جميع البيانات والإحصائيات المهمة لإدارة النظام بكل سهولة.
                </h4>
                <h5 class="text-muted">
                    النظام الخاص بالتسويق و المسوقين و الزيارات
                </h5>
            </div>
            <div>
                <img src="{{ asset('assets/images/info.png') }}" alt="" height="220">
            </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="row g-4">
            <!-- Card 1 - عدد المواقع الرئيسية -->
            <div class="col-md-6 col-lg-4">
                <div class="stats-card-new">
                    <div class="chart-container">
                        <div class="chart-data">
                            <div class="title small mb-1">عدد المواقع الرئيسية</div>
                            <div class="value">{{ $mainSitesCount }}</div>
                            <div class="percentage up">
                                <i class="fas fa-arrow-up"></i> 2.45%
                            </div>
                        </div>
                        <div class="ring-chart">
                            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#e9ecef" stroke-width="8" />
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#d80c0c" stroke-width="8" 
                                        stroke-dasharray="251.2" stroke-dashoffset="175.84" stroke-linecap="round" />
                            </svg>
                            <div class="ring-chart-inner">
                                <span>40%</span>
                            </div>
                        </div>
                    </div>
                    <div class="icon-new mt-3">
                        <i class="fas fa-globe"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2 - عدد المسوّقين -->
            <div class="col-md-6 col-lg-4">
                <div class="stats-card-new">
                    <div class="chart-container">
                        <div class="chart-data">
                            <div class="title small mb-1">عدد المسوّقين</div>
                            <div class="value">{{ $marketersCount }}</div>
                            <div class="percentage up">
                                <i class="fas fa-arrow-up"></i> 1.95%
                            </div>
                        </div>
                        <div class="ring-chart">
                            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#e9ecef" stroke-width="8" />
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#4361ee" stroke-width="8" 
                                        stroke-dasharray="251.2" stroke-dashoffset="125.6" stroke-linecap="round" />
                            </svg>
                            <div class="ring-chart-inner">
                                <span>20%</span>
                            </div>
                        </div>
                    </div>
                    <div class="icon-new mt-3">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3 - إجمالي العمولات -->
            <div class="col-md-6 col-lg-4">
                <div class="stats-card-new">
                    <div class="chart-container">
                        <div class="chart-data">
                            <div class="title small mb-1">إجمالي العمولات</div>
                            <div class="value">{{ number_format($totalCommissions, 2) }}</div>
                            <div class="percentage up">
                                <i class="fas fa-arrow-up"></i> 2.5%
                            </div>
                        </div>
                        <div class="ring-chart">
                            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#e9ecef" stroke-width="8" />
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#28a745" stroke-width="8" 
                                        stroke-dasharray="251.2" stroke-dashoffset="50.24" stroke-linecap="round" />
                            </svg>
                            <div class="ring-chart-inner">
                                <span>50%</span>
                            </div>
                        </div>
                    </div>
                    <div class="icon-new mt-3">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>

     

    </div>
</div>
<!-- End::app-content -->
@endsection