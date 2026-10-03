@extends('layouts.erp')

@section('title', 'Yeni Xərc Təsnifatı')
@section('page-title', 'YENİ TƏSNİFAT')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Xərclər</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expense-classifications.index') }}">Təsnifatlar</a></li>
    <li class="breadcrumb-item active">Yeni Təsnifat</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-6 col-lg-8 col-md-10">

        {{-- Geri Düyməsi və Başlıq --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('expense-classifications.index') }}"
                   class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 mb-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Təsnifatlar siyahısına qayıt</span>
                </a>
                <h4 class="fw-bold text-dark mb-1">Yeni Xərc Təsnifatı Əlavə Et</h4>
                <p class="text-muted small mb-0">Xərclərin qeydiyyatında istifadə olunacaq yeni kateqoriya yaradın.</p>
            </div>
        </div>

        {{-- Xəta Mesajları --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="fw-bold mb-1">Zəhmət olmasa xətaları düzəldin:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Forma Kartı --}}
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('expense-classifications.store') }}" method="POST">
                    @csrf

                    {{-- 1. Təsnifat Adı --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">
                            Təsnifat Adı <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Məsələn: Maaş, Arenda, Taksi, Reklam, PPX..."
                               required
                               autofocus>
                        <small class="text-muted mt-1 d-block">
                            Bu ad həm filial adminlərinin xərc əlavə etmə səhifəsində, həm də ERP cədvəlində dropdown kimi çıxacaq.
                        </small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 2. Sıralama Nömrəsi --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">
                            Sıralama Nömrəsi (Sort Order)
                        </label>
                        <input type="number"
                               name="sort_order"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', $nextOrder) }}"
                               min="1">
                        <small class="text-muted mt-1 d-block">
                            Cədvəl və açılan siyahılarda təsnifatların hansı ardıcıllıqla düzüləcəyini təyin edir.
                        </small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 3. Status --}}
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                   style="width: 38px; height: 20px; cursor: pointer;">
                            <label class="form-check-label fw-bold text-dark ms-2" for="is_active" style="cursor: pointer;">
                                Aktiv olsun
                            </label>
                            <small class="text-muted d-block ms-2">
                                Qeyri-aktiv edilərsə, xərc daxil edərkən bu təsnifat seçim siyahısında görünməyəcək.
                            </small>
                        </div>
                    </div>

                    {{-- Düymələr: İmtina və Əlavə Et --}}
                    <div class="d-flex align-items-center justify-content-end gap-3 pt-3 border-top">
                        <a href="{{ route('expense-classifications.index') }}"
                           class="btn btn-light px-4 py-2.5 fw-semibold border">
                            İmtina
                        </a>
                        <button type="submit"
                                class="btn btn-primary px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm"
                                style="background-color: var(--erp-primary, #107c41); border-color: var(--erp-primary, #107c41);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Əlavə Et</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
