@extends('layouts.erp')

@section('title', 'Dashboard')
@section('page-title', 'DASHBOARD')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboards</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    {{-- Left Col: Welcome Profile Card --}}
    <div class="col-xl-4">
        <div class="card overflow-hidden card-welcome-widget">
            <div class="bg-primary-subtle p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-7">
                        <div class="text-primary">
                            <h5 class="font-size-16 mb-1 text-primary font-weight-700">Xoş gəlmisiniz!</h5>
                            <p class="font-size-13 text-muted mb-0">Portal ERP İdarəetmə Paneli</p>
                        </div>
                    </div>
                    <div class="col-5 align-self-end text-end">
                        <div class="welcome-badge-icon">
                            <svg width="68" height="68" viewBox="0 0 24 24" fill="none" stroke="#556ee6" stroke-width="1.3" opacity="0.6">
                                <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                <line x1="2" x2="22" y1="10" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="avatar-md profile-user-wid mb-3">
                            <div class="avatar-title rounded-circle bg-light border border-2 border-white shadow-sm font-size-22 font-weight-bold text-primary">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                        </div>
                        <h5 class="font-size-15 text-truncate mb-1 font-weight-600">{{ auth()->user()->name ?? 'ERP Admin' }}</h5>
                        <p class="text-muted mb-0 font-size-12">{{ auth()->user()->roles->first()->name ?? 'Super Admin' }}</p>
                    </div>

                    <div class="col-sm-7">
                        <div class="pt-4">
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="font-size-15 mb-1 font-weight-600">Portal</h5>
                                    <p class="text-muted mb-0 font-size-12">Layihə</p>
                                </div>
                                <div class="col-6">
                                    <h5 class="font-size-15 mb-1 font-weight-600 text-success">Aktiv</h5>
                                    <p class="text-muted mb-0 font-size-12">Sistem</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="javascript:void(0);" class="btn btn-primary btn-sm waves-effect waves-light font-size-12">
                                    Profilə bax
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="ms-1"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Status / Info widget --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4 font-size-14 font-weight-600">Aylıq Əməliyyat Xülasəsi</h4>
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text-muted font-size-13 mb-1">Cari Ayın Dövriyyəsi</p>
                        <h3 class="font-weight-700 mb-0 font-size-20">0.00 ₼</h3>
                        <p class="text-muted font-size-12 mt-1 mb-0">
                            <span class="badge bg-soft-info text-info font-size-11">İnteqrasiya gözlənilir</span>
                        </p>
                    </div>
                    <div class="col-sm-6 text-center">
                        <div class="circular-indicator mt-2">
                            <svg width="74" height="74" viewBox="0 0 36 36" class="circular-chart">
                                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#f1f1f5" stroke-width="3.5" />
                                <path class="circle" stroke-dasharray="85, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#556ee6" stroke-width="3.5" stroke-linecap="round" />
                                <text x="18" y="20.35" class="percentage" text-anchor="middle" font-size="7.5" font-weight="700" fill="#495057">100%</text>
                            </svg>
                            <span class="font-size-11 text-muted d-block mt-1">Sistem Hazır</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Col: Quick Metric Cards & Next Steps Roadmap --}}
    <div class="col-xl-8">
        {{-- KPI Summary Row --}}
        <div class="row">
            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted font-weight-500 font-size-13 mb-2">Bu Ay Satışlar</p>
                                <h4 class="mb-0 font-weight-700 font-size-20">{{ number_format($summary['total_sales'] ?? 0) }}</h4>
                            </div>
                            <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
                                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                        <path d="M3 9h18"></path>
                                        <path d="M9 21V9"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted font-weight-500 font-size-13 mb-2">Aylıq Dövriyyə</p>
                                <h4 class="mb-0 font-weight-700 font-size-20 text-success">{{ number_format($summary['total_revenue'] ?? 0, 2) }} ₼</h4>
                            </div>
                            <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
                                        <line x1="12" x2="12" y1="2" y2="22"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted font-weight-500 font-size-13 mb-2">Orta Qiymət</p>
                                <h4 class="mb-0 font-weight-700 font-size-20">{{ number_format($summary['average_price'] ?? 0, 2) }} ₼</h4>
                            </div>
                            <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
                                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Next Phase Roadmap Banner --}}
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title m-0 font-size-15 font-weight-600">
                        📊 Mühasibatlıq və 32 Sütunlu Hesabat
                    </h4>
                    <span class="badge {{ $connected ? 'bg-success' : 'bg-soft-warning text-dark' }} font-size-12 px-2 py-1">
                        {{ $connected ? '✓ API Qoşuldu' : '⏳ API Gözlənilir' }}
                    </span>
                </div>

                <p class="text-muted font-size-13 mb-4">
                    PortalWebsite ilə təhlükəsiz API əlaqəsi quruldu. Buxalteriyanın tələb etdiyi 32 sütunlu hesabat cədvəli (satışlar, 00:00 gecə seansı bonusları, PlusBir nailiyyətləri və ödənişlər) ERP sistemində canlı izlənilə və Excel/CSV kimi ixrac edilə bilər.
                </p>

                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light">
                    <div>
                        <h6 class="font-size-14 font-weight-700 mb-1 text-dark">32 Sütunlu Mühasibatlıq Cədvəlinə Keçid</h6>
                        <p class="text-muted font-size-12 mb-0">Bütün filialların və işçilərin detallı satış və bonus statistikasına baxın.</p>
                    </div>
                    <a href="{{ route('accounting.index') }}" class="btn btn-primary btn-sm font-size-13 px-3 py-2">
                        Hesabata Keç
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="ms-1"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection