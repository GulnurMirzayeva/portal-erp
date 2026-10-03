@extends('layouts.erp')

@section('title', 'Xərc Təsnifatları')
@section('page-title', 'XƏRC TƏSNİFATLARI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ERP</a></li>
    <li class="breadcrumb-item"><a href="javascript:void(0);">Mühasibatlıq</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Xərclər</a></li>
    <li class="breadcrumb-item active">Təsnifatlar</li>
@endsection

@push('css')
<style>
    .classification-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .classification-toolbar {
        padding: 12px 18px;
        background: #ffffff;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .classification-table {
        margin-bottom: 0;
        font-size: 13px;
    }

    .classification-table thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 1px solid #e2e8f0;
        padding: 9px 14px;
        vertical-align: middle;
    }

    .classification-table tbody td {
        padding: 9px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }

    .classification-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge-status-active {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 20px;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
    }

    .badge-status-inactive {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 20px;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
    }

    .badge-status-active:hover, .badge-status-inactive:hover {
        opacity: 0.85;
        transform: scale(0.98);
    }

    .btn-table-action {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-edit-action {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .btn-edit-action:hover {
        background: #15803d;
        color: #ffffff;
        border-color: #15803d;
    }

    .btn-delete-action {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .btn-delete-action:hover {
        background: #b91c1c;
        color: #ffffff;
        border-color: #b91c1c;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Uğurlu Əməliyyat Mesajı --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm d-flex align-items-center justify-content-between" role="alert" style="background: #ecfdf5; color: #065f46; border-left: 4px solid #107c41 !important; border-radius: 8px;">
                <div class="d-flex align-items-center gap-2 small fw-semibold">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Təsnifatlar Cədvəl Kartı --}}
        <div class="classification-card">

            {{-- Yuxarı İdarəetmə Paneli --}}
            <div class="classification-toolbar">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark fs-6">Xərc Təsnifatları</span>
                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size: 11px;">
                        {{ $classifications->count() }} ədəd
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    {{-- Kompakt Axtarış --}}
                    <form method="GET" action="{{ route('expense-classifications.index') }}" class="d-flex align-items-center gap-1">
                        <div class="input-group input-group-sm">
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   class="form-control"
                                   placeholder="Təsnifat axtar..."
                                   style="font-size: 12px; height: 32px; width: 170px;">
                            <button class="btn btn-outline-secondary" type="submit" title="Axtar" style="height: 32px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </button>
                            @if($search)
                                <a href="{{ route('expense-classifications.index') }}" class="btn btn-outline-danger" title="Axtarışı sıfırla" style="height: 32px; line-height: 18px;">
                                    &times;
                                </a>
                            @endif
                        </div>
                    </form>

                    {{-- Yeni Təsnifat Əlavə Et Düyməsi --}}
                    <a href="{{ route('expense-classifications.create') }}"
                       class="btn btn-sm d-inline-flex align-items-center gap-1.5 px-3 fw-semibold text-white shadow-sm"
                       style="background-color: var(--erp-primary, #107c41); height: 32px; font-size: 12px; border-radius: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Yeni Təsnifat Əlavə Et</span>
                    </a>
                </div>
            </div>

            {{-- Cədvəl (Bütün Təsnifatlar Alt-alta) --}}
            <div class="table-responsive">
                <table class="table classification-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">№</th>
                            <th>Təsnifat Adı</th>
                            <th style="width: 100px;" class="text-center">Sıralama</th>
                            <th style="width: 130px;" class="text-center">Status</th>
                            <th style="width: 170px;" class="text-center">Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classifications as $index => $item)
                            <tr>
                                {{-- 1. № --}}
                                <td class="text-center text-muted fw-semibold" style="font-size: 12px;">
                                    {{ $index + 1 }}
                                </td>

                                {{-- 2. Təsnifat Adı --}}
                                <td>
                                    <span class="fw-semibold text-dark">{{ $item->name }}</span>
                                </td>

                                {{-- 3. Sıralama --}}
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1 font-monospace" style="font-size: 11px;">
                                        {{ $item->sort_order }}
                                    </span>
                                </td>

                                {{-- 4. Status (Klikləyərək tez dəyişdirilə bilər) --}}
                                <td class="text-center">
                                    <span class="{{ $item->is_active ? 'badge-status-active' : 'badge-status-inactive' }} status-toggle-btn"
                                          data-id="{{ $item->id }}"
                                          title="Statusu dəyişmək üçün klikləyin">
                                        <span style="font-size: 8px;">●</span>
                                        <span class="status-text">{{ $item->is_active ? 'Aktiv' : 'Deaktiv' }}</span>
                                    </span>
                                </td>

                                {{-- 5. Əməliyyatlar (Düzəliş et və Sil) --}}
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        {{-- Düzəliş Et --}}
                                        <a href="{{ route('expense-classifications.edit', $item->id) }}"
                                           class="btn-table-action btn-edit-action"
                                           title="Təsnifata düzəliş et">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            <span>Düzəliş et</span>
                                        </a>

                                        {{-- Sil --}}
                                        <form action="{{ route('expense-classifications.destroy', $item->id) }}"
                                              method="POST"
                                              class="d-inline m-0"
                                              onsubmit="return confirm('«{{ $item->name }}» təsnifatını silmək istədiyinizdən əminsiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn-table-action btn-delete-action"
                                                    title="Təsnifatı sil">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    Heç bir təsnifat tapılmadı.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Aşağı İnfo Zolağı --}}
            <div class="py-2 px-3 bg-light border-top text-muted d-flex justify-content-between align-items-center" style="font-size: 11.5px;">
                <span>Cəmi: <strong>{{ $classifications->count() }}</strong> təsnifat (hamısı göstərilir)</span>
                <span class="text-secondary">Sıralama nömrəsinə görə ardıcıl düzülüb</span>
            </div>

        </div>

    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Status toggle AJAX
        document.querySelectorAll('.status-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const textSpan = this.querySelector('.status-text');
                const self = this;

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
                        if (data.is_active) {
                            self.className = 'badge-status-active status-toggle-btn';
                            textSpan.textContent = 'Aktiv';
                        } else {
                            self.className = 'badge-status-inactive status-toggle-btn';
                            textSpan.textContent = 'Deaktiv';
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
