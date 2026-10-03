@extends('layouts.erp')

@section('title', 'Xərc Təsnifatları')
@section('page-title', 'XƏRC TƏSNİFATLARI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Xərclər</a></li>
    <li class="breadcrumb-item active">Təsnifatlar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center justify-content-between mb-4" role="alert" style="border-left: 4px solid #107c41 !important;">
                <div class="d-flex align-items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-success">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span class="fw-medium">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Üst Panel: Başlıq, Axtarış və Əlavə Et Düyməsi --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="card-title fw-bold text-dark mb-0">Xərc Təsnifatları</h4>
                            <span class="badge bg-success-subtle text-success fs-7 px-2.5 py-1 rounded-pill">
                                {{ $classifications->total() }} ədəd
                            </span>
                        </div>
                        <p class="text-muted small mb-0">Filial və ofis xərclərində istifadə olunan təsnifat kateqoriyalarını buradan idarə edin.</p>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        {{-- Axtarış Formu --}}
                        <form method="GET" action="{{ route('expense-classifications.index') }}" class="d-flex align-items-center gap-2">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                                       placeholder="Təsnifat adı axtar..." style="min-width: 200px;">
                                <button class="btn btn-sm btn-outline-secondary" type="submit" title="Axtar">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </button>
                                @if($search)
                                    <a href="{{ route('expense-classifications.index') }}" class="btn btn-sm btn-outline-danger" title="Axtarışı təmizlə">
                                        &times;
                                    </a>
                                @endif
                            </div>
                        </form>

                        {{-- Açıq və Aydın "+ Yeni Təsnifat Əlavə Et" Düyməsi --}}
                        <a href="{{ route('expense-classifications.create') }}"
                           class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm"
                           style="background-color: var(--erp-primary, #107c41); border-color: var(--erp-primary, #107c41);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Yeni Təsnifat Əlavə Et</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cədvəl Kartı --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light" style="border-bottom: 2px solid #e9edf0;">
                            <tr>
                                <th style="width: 70px;" class="text-center py-3 text-muted text-uppercase fs-7 fw-semibold">№</th>
                                <th class="py-3 text-muted text-uppercase fs-7 fw-semibold">Təsnifat Adı</th>
                                <th style="width: 140px;" class="text-center py-3 text-muted text-uppercase fs-7 fw-semibold">Sıralama</th>
                                <th style="width: 140px;" class="text-center py-3 text-muted text-uppercase fs-7 fw-semibold">Status</th>
                                <th style="width: 220px;" class="text-center py-3 text-muted text-uppercase fs-7 fw-semibold pe-4">Əməliyyatlar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($classifications as $index => $item)
                                <tr>
                                    {{-- № --}}
                                    <td class="text-center text-muted fw-semibold">
                                        {{ $classifications->firstItem() + $index }}
                                    </td>

                                    {{-- Təsnifat Adı --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2 py-1">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                                 style="width: 34px; height: 34px; background: linear-gradient(135deg, #107c41 0%, #1e4620 100%); font-size: 13px;">
                                                {{ mb_substr($item->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                                                <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 11px;">
                                                    ID: #{{ $item->id }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Sıralama --}}
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold">
                                            #{{ $item->sort_order }}
                                        </span>
                                    </td>

                                    {{-- Status Toggle --}}
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-flex align-items-center gap-1">
                                            <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                                   data-id="{{ $item->id }}"
                                                   {{ $item->is_active ? 'checked' : '' }}
                                                   style="cursor: pointer; width: 36px; height: 18px;">
                                            <span class="small fw-semibold ms-1 status-label-{{ $item->id }} {{ $item->is_active ? 'text-success' : 'text-muted' }}">
                                                {{ $item->is_active ? 'Aktiv' : 'Deaktiv' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Əməliyyatlar: Düzəliş et və Sil --}}
                                    <td class="text-center pe-4">
                                        <div class="d-inline-flex align-items-center gap-2">
                                            {{-- Düzəliş et Düyməsi --}}
                                            <a href="{{ route('expense-classifications.edit', $item->id) }}"
                                               class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5 px-2.5 py-1.5 fw-medium rounded-2"
                                               title="Təsnifata düzəliş et">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                <span>Düzəliş et</span>
                                            </a>

                                            {{-- Sil Düyməsi --}}
                                            <form action="{{ route('expense-classifications.destroy', $item->id) }}" method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('«{{ $item->name }}» təsnifatını silmək istədiyinizdən əminsiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1.5 px-2.5 py-1.5 fw-medium rounded-2"
                                                        title="Təsnifatı sil">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                    <span>Sil</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div class="bg-light rounded-circle p-3 mb-3 text-muted">
                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                </svg>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Heç bir təsnifat tapılmadı</h6>
                                            <p class="text-muted small mb-3">Axtarış meyarlarına uyğun və ya hələ əlavə edilmiş təsnifat yoxdur.</p>
                                            <a href="{{ route('expense-classifications.create') }}" class="btn btn-sm btn-primary">
                                                + İlk Təsnifatı Əlavə Et
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($classifications->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        Göstərilir: {{ $classifications->firstItem() }} - {{ $classifications->lastItem() }} (Cəmi: {{ $classifications->total() }})
                    </span>
                    <div>
                        {{ $classifications->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Status switch AJAX toggle
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function () {
                const id = this.getAttribute('data-id');
                const label = document.querySelector(`.status-label-${id}`);

                fetch(`/expense-classifications/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        label.textContent = data.is_active ? 'Aktiv' : 'Deaktiv';
                        if (data.is_active) {
                            label.classList.remove('text-muted');
                            label.classList.add('text-success');
                        } else {
                            label.classList.remove('text-success');
                            label.classList.add('text-muted');
                        }
                    }
                })
                .catch(err => {
                    console.error('Status toggle failed:', err);
                });
            });
        });
    });
</script>
@endpush
@endsection
