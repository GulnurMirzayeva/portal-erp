@extends('layouts.erp')

@section('title', 'Yeni İşçi Qeydiyyatı — Portal ERP')
@section('page-title', 'YENİ İŞÇİ QEYDİYYATI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">İşçilər</a></li>
    <li class="breadcrumb-item active">Yeni İşçi</li>
@endsection

@section('content')
<div class="att-wrapper" style="max-width: 900px; margin: 0 auto;">
    <div class="att-top-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; color: #059669; border: 1.5px solid #a7f3d0; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
            </div>
            <div>
                <h5 class="att-top-title" style="margin: 0 0 2px 0;">
                    Yeni Vahid İşçi Qeydiyyatı
                </h5>
                <p class="att-top-desc">
                    İşçinin şəxsi məlumatlarını, vəzifələrini, əsas filialını və QR giriş parametrlərini daxil edin.
                </p>
            </div>
        </div>
        <a href="{{ route('employees.index') }}" class="att-btn att-btn-outline" style="height: 38px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            İşçilər Siyahısına Qayıt
        </a>
    </div>

    <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        @include('employees._form')

        <div class="settings-bottom-bar">
            <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 13px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#107c41" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>İşçi dərhal ERP kadr bazasına yazılacaq və QR üçün aktivləşəcək.</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="{{ route('employees.index') }}" class="att-btn att-btn-outline">İmtina</a>
                <button type="submit" class="att-btn att-btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    İşçini Yadda Saxla
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
