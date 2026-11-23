@push('css')
<style>
    .att-wrapper {
        padding-bottom: 50px;
        max-width: 1180px;
        margin: 0 auto;
    }

    /* Top Action & Info Bar */
    .att-top-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 22px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .att-top-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .att-top-desc {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    /* Settings Card */
    .settings-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px 26px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        margin-bottom: 22px;
        transition: all 0.2s ease;
    }
    .settings-card-header {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px dashed #e2e8f0;
    }
    .settings-icon-badge {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .badge-emerald { background: #ecfdf5; color: #107c41; border: 1.5px solid #a7f3d0; }
    .badge-blue { background: #eff6ff; color: #2563eb; border: 1.5px solid #bfdbfe; }
    .badge-amber { background: #fffbeb; color: #d97706; border: 1.5px solid #fde68a; }
    .badge-indigo { background: #eef2ff; color: #4f46e5; border: 1.5px solid #c7d2fe; }
    .badge-purple { background: #f5f3ff; color: #7c3aed; border: 1.5px solid #ddd6fe; }

    .settings-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
    }
    .settings-desc {
        font-size: 12.5px;
        color: #64748b;
        margin: 0;
        line-height: 1.45;
    }

    /* Form Fields */
    .att-field-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 7px;
    }
    .att-input-standard {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        font-size: 13.5px;
        font-weight: 600;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .att-input-standard:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.14);
    }
    .att-textarea-standard {
        width: 100%;
        padding: 11px 14px;
        font-size: 13.5px;
        font-weight: 500;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .att-select-standard {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 16px 16px;
        padding-right: 38px;
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    /* Option Cards */
    .option-card-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .option-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px 14px;
        background: #ffffff;
        cursor: pointer;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        margin: 0;
    }
    .option-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-1px);
    }
    .option-card.active {
        border-color: #107c41;
        background: #f0fdf4;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.12);
    }
    .option-card input[type="radio"],
    .option-card input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .custom-check-indicator {
        width: 20px;
        height: 20px;
        border-radius: 7px;
        border: 2px solid #cbd5e1;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
        transition: all 0.18s ease;
    }
    .custom-radio-indicator { border-radius: 50%; }
    .option-card.active .custom-check-indicator {
        border-color: #107c41;
        background: #107c41;
    }
    .custom-check-indicator svg {
        opacity: 0;
        transform: scale(0.6);
        transition: all 0.18s ease;
        color: #ffffff;
    }
    .option-card.active .custom-check-indicator svg {
        opacity: 1;
        transform: scale(1);
    }
    .opt-title {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.25;
    }
    .opt-desc {
        font-size: 11.5px;
        color: #64748b;
        margin: 3px 0 0 0;
        line-height: 1.35;
    }

    /* Role selection cards */
    .role-select-item {
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 10px 14px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: all 0.15s ease;
        margin-bottom: 8px;
    }
    .role-select-item:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .role-select-item.selected {
        border-color: #107c41;
        background: #f0fdf4;
    }
    .role-pill-badge {
        padding: 3px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Live Preview */
    .preview-list {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 8px 16px;
    }
    .preview-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 13px;
        gap: 12px;
    }
    .preview-row:last-child { border-bottom: none; }
    .preview-pill {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #0f172a;
        padding: 4px 12px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 12.5px;
    }

    /* Buttons */
    .att-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 20px;
        border-radius: 12px;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        box-sizing: border-box;
    }
    .att-btn-primary {
        background: #107c41;
        color: #ffffff !important;
        border-color: #0d6334;
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.22);
    }
    .att-btn-primary:hover {
        background: #0b5e30;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 124, 65, 0.3);
    }
    .att-btn-outline {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #334155 !important;
    }
    .att-btn-outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }

    .settings-bottom-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .att-alert-danger {
        background: #fef2f2;
        border: 1.5px solid #fecaca;
        border-radius: 16px;
        padding: 14px 20px;
        margin-bottom: 22px;
        color: #991b1b;
    }
    .att-info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 13px 15px;
        display: flex;
        align-items: flex-start;
        gap: 11px;
        font-size: 12.5px;
        color: #475569;
        line-height: 1.45;
    }
</style>
@endpush

@if($errors->any())
    <div class="att-alert-danger">
        <div class="fw-bold mb-1">Xəta baş verdi — zəhmət olmasa düzəliş edin:</div>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $selectedWorkerTypeIds = old('worker_type_ids', $employee->workerTypes->pluck('id')->all() ?? []);
    $primaryWorkerTypeId = old('primary_worker_type_id', $employee->primaryWorkerType()?->id);
    $selectedBranchIds = old('managed_branch_ids', $employee->managedBranches->pluck('id')->all() ?? []);
    $primaryBranchId = old('primary_branch_id', $employee->primaryBranch()?->id);
    $selectedGameIds = old('game_ids', $employee->games->pluck('portal_game_id')->all() ?? []);
    $selGender = old('gender', $employee->gender ?? 'male');
    $selIsIntern = old('is_intern', $employee->is_intern ?? false);
@endphp

<div class="row g-4">
    {{-- Sol Sütun --}}
    <div class="col-lg-7">
        {{-- 1. Əsas Şəxsi Məlumatlar --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-emerald">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div>
                    <h5 class="settings-title">1. Şəxsi Məlumatlar & Cinsiyyət</h5>
                    <p class="settings-desc">İşçinin ad-soyadı, cinsiyyəti (Aktyor/Aktrisa ayrımı üçün), əlaqə və QR PIN kodu.</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="att-field-label">Tam Adı (Ad və Soyad) <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" id="inpFullName" class="att-input-standard" value="{{ old('full_name', $employee->full_name) }}" placeholder="Məs: Əli Əliyev və ya Nərgiz Səlimova" required>
                </div>

                <div class="col-md-6">
                    <label class="att-field-label">Ad</label>
                    <input type="text" name="first_name" id="inpFirstName" class="att-input-standard" value="{{ old('first_name', $employee->first_name) }}" placeholder="Əli">
                </div>
                <div class="col-md-6">
                    <label class="att-field-label">Soyad</label>
                    <input type="text" name="last_name" id="inpLastName" class="att-input-standard" value="{{ old('last_name', $employee->last_name) }}" placeholder="Əliyev">
                </div>

                <div class="col-md-6">
                    <label class="att-field-label">Tabeldə Qısa Ad</label>
                    <input type="text" name="tabel_display_name" id="inpTabelDisplayName" class="att-input-standard" value="{{ old('tabel_display_name', $employee->tabel_display_name) }}" placeholder="Məs: Əliyev (Boş buraxılarsa soyadı götürülər)">
                </div>
                <div class="col-md-6">
                    <label class="att-field-label">Telefon Nömrəsi</label>
                    <input type="text" name="phone" class="att-input-standard" value="{{ old('phone', $employee->phone) }}" placeholder="+994 50 123 45 67">
                </div>

                {{-- Cinsiyyət seçimi --}}
                <div class="col-md-6">
                    <label class="att-field-label">Cinsiyyət <span class="text-danger">*</span></label>
                    <div class="option-card-grid">
                        <label class="option-card {{ $selGender === 'male' ? 'active' : '' }}" data-radio-gender>
                            <input type="radio" name="gender" value="male" {{ $selGender === 'male' ? 'checked' : '' }}>
                            <div class="custom-check-indicator custom-radio-indicator">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">Kişi</div>
                                <p class="opt-desc">Aktyor, adm, res...</p>
                            </div>
                        </label>
                        <label class="option-card {{ $selGender === 'female' ? 'active' : '' }}" data-radio-gender>
                            <input type="radio" name="gender" value="female" {{ $selGender === 'female' ? 'checked' : '' }}>
                            <div class="custom-check-indicator custom-radio-indicator">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">Qadın</div>
                                <p class="opt-desc">Aktrisa (avtomatik)</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="att-field-label">QR Giriş PIN Kodu <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2">
                        <input type="text" name="pin_code" id="inpPinCode" class="att-input-standard font-monospace fw-bold" style="letter-spacing: 2px;" value="{{ old('pin_code', $employee->pin_code ?: '1234') }}" maxlength="10" required>
                        <button type="button" class="att-btn att-btn-outline py-1 px-3" onclick="generateRandomPin()" title="Təsadüfi 4 rəqəmli PIN təyin et">
                            PIN
                        </button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="att-field-label">İşə Qəbul Tarixi</label>
                    <input type="date" name="hire_date" class="att-input-standard" value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d') ?: now()->toDateString()) }}">
                </div>
                <div class="col-md-4">
                    <label class="att-field-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="att-input-standard att-select-standard">
                        <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Aktiv</option>
                        <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Deaktiv</option>
                        <option value="dismissed" {{ old('status', $employee->status) === 'dismissed' ? 'selected' : '' }}>İşdən çıxıb</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="att-field-label">QR Davamiyyət</label>
                    <label class="option-card {{ old('requires_qr', $employee->requires_qr ?? true) ? 'active' : '' }}" style="padding: 10px 12px;" data-check-card>
                        <input type="checkbox" name="requires_qr" value="1" {{ old('requires_qr', $employee->requires_qr ?? true) ? 'checked' : '' }}>
                        <div class="custom-check-indicator">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div>
                            <div class="opt-title" style="font-size: 12px;">QR Tələb Olunur</div>
                            <p class="opt-desc" style="font-size: 11px;">Ofis/Kurator üçün söndürülə bilər</p>
                        </div>
                    </label>
                </div>

                <div class="col-12">
                    <label class="att-field-label">Qeyd / Əlavə Məlumat</label>
                    <textarea name="note" class="att-textarea-standard" rows="2" placeholder="İşçi haqqında qeydlər...">{{ old('note', $employee->note) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. Vəzifələr (Çoxlu Vəzifə İcazəsi) --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-blue">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <h5 class="settings-title">2. Vəzifələr & Stajorluq</h5>
                    <p class="settings-desc">İşçinin birdən çox vəzifəsi ola bilər (məs: Administrator + Resepşn). Stajor yalnız icazəli rollara şamil olunur.</p>
                </div>
            </div>

            {{-- Stajorluq Seçimi --}}
            <div class="mb-3">
                <label class="option-card {{ $selIsIntern ? 'active' : '' }}" style="border-color: #fde68a; background: #fffbeb;" id="internCard" data-check-card>
                    <input type="checkbox" name="is_intern" id="chkIsIntern" value="1" {{ $selIsIntern ? 'checked' : '' }}>
                    <div class="custom-check-indicator" style="border-color: #d97706;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-title" style="color: #92400e;">Bu İşçi Stajordur (Daimi deyil)</div>
                        <p class="opt-desc" style="color: #b45309;">Stajor olduqda vizual yarış xalı 0 olur və yalnız stajor qəbul edən vəzifələr (Resepşn, Aktyor...) seçilə bilər.</p>
                    </div>
                </label>
            </div>

            <div class="att-field-label mb-2">Vəzifələr Siyahısı (Seçin və Əsas Vəzifəni Qeyd Edin):</div>

            <div class="role-list-container">
                @foreach($workerTypes as $wt)
                    @php
                        $isChecked = in_array($wt->id, $selectedWorkerTypeIds, true);
                        $isPrimary = ((int)$primaryWorkerTypeId === (int)$wt->id) || (empty($primaryWorkerTypeId) && $loop->first);
                    @endphp
                    <div class="role-select-item {{ $isChecked ? 'selected' : '' }}" data-role-row data-role-id="{{ $wt->id }}" data-can-intern="{{ $wt->can_have_intern ? '1' : '0' }}" data-requires-game="{{ $wt->requires_game ? '1' : '0' }}">
                        <label class="d-flex align-items-center gap-3 m-0 flex-grow-1" style="cursor: pointer;">
                            <input type="checkbox" name="worker_type_ids[]" value="{{ $wt->id }}" class="form-check-input mt-0 chk-role" {{ $isChecked ? 'checked' : '' }}>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 13.5px;">
                                    {{ $wt->name }}
                                    @if($wt->female_name)
                                        <span class="text-muted fw-normal">/ {{ $wt->female_name }}</span>
                                    @endif
                                    <span class="badge ms-2" style="background: {{ $wt->badge_color ?: '#475569' }}; font-size: 11px;">
                                        {{ $wt->short_code }}
                                    </span>
                                </div>
                                <div class="small text-muted">
                                    {{ $wt->category_label }} &bull; {{ $wt->scope_label }}
                                    @if(!$wt->can_have_intern)
                                        &bull; <span class="text-danger fw-semibold">Stajor olmur</span>
                                    @endif
                                </div>
                            </div>
                        </label>
                        <label class="d-flex align-items-center gap-1 m-0 text-muted small primary-radio-wrap {{ $isChecked ? '' : 'd-none' }}" style="cursor: pointer;">
                            <input type="radio" name="primary_worker_type_id" value="{{ $wt->id }}" {{ $isPrimary ? 'checked' : '' }}>
                            <span class="fw-semibold">Əsas rol</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sağ Sütun --}}
    <div class="col-lg-5">
        {{-- Canlı Önizləmə --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-indigo">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </div>
                <div>
                    <h5 class="settings-title">Canlı Tabel & Görünüş Önizləməsi</h5>
                    <p class="settings-desc">İşçinin filial tabelində və satışlarda necə çıxacağı.</p>
                </div>
            </div>

            <div class="preview-list">
                <div class="preview-row">
                    <span class="text-muted fw-semibold">Tabeldə Adı:</span>
                    <span class="preview-pill" id="prevTabelName">Əliyev</span>
                </div>
                <div class="preview-row">
                    <span class="text-muted fw-semibold">Tabel Rol Kodu:</span>
                    <span class="preview-pill" style="color: #107c41; border-color: #a7f3d0; background: #ecfdf5;" id="prevTabelRole">akt</span>
                </div>
                <div class="preview-row">
                    <span class="text-muted fw-semibold">Status:</span>
                    <span class="preview-pill" id="prevStatus">Daimi İşçi</span>
                </div>
                <div class="preview-row">
                    <span class="text-muted fw-semibold">Əsas Filial:</span>
                    <span class="preview-pill" id="prevBranch">Təyin edilməyib</span>
                </div>
            </div>
        </div>

        {{-- 3. Filial Təhkimatı --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </div>
                <div>
                    <h5 class="settings-title">3. Filial Təhkimatı</h5>
                    <p class="settings-desc">Əsas filial + Kuratorlar üçün 2, 3 və ya daha çox idarə etdiyi filiallar.</p>
                </div>
            </div>

            <div class="mb-3">
                <label class="att-field-label">Əsas Filial</label>
                <select name="primary_branch_id" id="selPrimaryBranch" class="att-input-standard att-select-standard">
                    <option value="">— Filialsız (Mərkəzi Ofis) —</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ (int)$primaryBranchId === (int)$b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <label class="att-field-label">Əlavə İdarə Etdiyi Filiallar (Kurator / Çoxlu filial)</label>
                <div class="d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto; padding: 4px;">
                    @foreach($branches as $b)
                        <label class="d-flex align-items-center gap-2 small text-dark p-2 border rounded-3 bg-light m-0" style="cursor: pointer;">
                            <input type="checkbox" name="managed_branch_ids[]" value="{{ $b->id }}" class="form-check-input mt-0 chk-managed-branch" {{ in_array($b->id, $selectedBranchIds, true) ? 'checked' : '' }}>
                            <span class="fw-semibold">{{ $b->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 4. Baxdığı Oyunlar --}}
        <div class="settings-card" id="gamesCard">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-purple">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                </div>
                <div>
                    <h5 class="settings-title">4. Baxdığı Daimi Oyunlar</h5>
                    <p class="settings-desc">Aktyor və oyun rolları üçün daimi təhkim olunmuş 1 və ya bir neçə oyun (Drakula, Kor və s.).</p>
                </div>
            </div>

            <div class="att-info-box mb-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.2" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                <div>
                    Əvəzə getdiyi günlərdə oyunlar <strong>PortalWebsite satışlarından</strong> avtomatik gələcək. Burada isə işçinin daimi baxdığı əsas oyun(lar)ı seçirsiniz.
                </div>
            </div>

            @if(!empty($games))
                <div class="d-flex flex-column gap-2" style="max-height: 240px; overflow-y: auto; padding: 4px;">
                    @foreach($games as $g)
                        <label class="d-flex align-items-center gap-2 small text-dark p-2 border rounded-3 bg-white m-0" style="cursor: pointer;">
                            <input type="checkbox" name="game_ids[]" value="{{ $g['id'] }}" class="form-check-input mt-0" {{ in_array($g['id'], $selectedGameIds, true) ? 'checked' : '' }}>
                            <span><strong>{{ $g['name'] }}</strong> <span class="text-muted">({{ $g['branch_name'] }})</span></span>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="text-muted small text-center py-3 border rounded-3" style="border-style: dashed !important;">
                    Aktiv oyun tapılmadı.
                </div>
            @endif
        </div>

        {{-- 5. Tarixçə Jurnalı (Yalnız Edit səhifəsində) --}}
        @if($employee->exists && $employee->statusHistories->count() > 0)
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-icon-badge badge-blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <h5 class="settings-title">5. Əmək & Staj Keçid Tarixçəsi</h5>
                        <p class="settings-desc">İşçinin status dəyişiklikləri və stajorluqdan daimiyə keçid qeydləri.</p>
                    </div>
                </div>

                <div class="timeline-list">
                    @foreach($employee->statusHistories as $sh)
                        <div class="p-2 mb-2 border rounded-3 bg-light small">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">
                                    @if($sh->event_type === 'intern_promoted')
                                        ⭐ Stajorluqdan Daimiyə Keçid
                                    @elseif($sh->event_type === 'created')
                                        📝 İşçi Qeydiyyatı
                                    @else
                                        {{ $sh->event_type }}
                                    @endif
                                </span>
                                <span class="text-muted font-monospace">{{ optional($sh->effective_date)->format('d.m.Y') }}</span>
                            </div>
                            @if($sh->note)
                                <div class="text-muted mt-1">{{ $sh->note }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function generateRandomPin() {
    const pin = Math.floor(1000 + Math.random() * 9000).toString();
    const inp = document.getElementById('inpPinCode');
    if (inp) inp.value = pin;
}

document.addEventListener('DOMContentLoaded', function () {
    const inpFullName = document.getElementById('inpFullName');
    const inpFirstName = document.getElementById('inpFirstName');
    const inpLastName = document.getElementById('inpLastName');
    const inpTabelDisplayName = document.getElementById('inpTabelDisplayName');
    const chkIsIntern = document.getElementById('chkIsIntern');
    const selPrimaryBranch = document.getElementById('selPrimaryBranch');
    const prevTabelName = document.getElementById('prevTabelName');
    const prevTabelRole = document.getElementById('prevTabelRole');
    const prevStatus = document.getElementById('prevStatus');
    const prevBranch = document.getElementById('prevBranch');
    const gamesCard = document.getElementById('gamesCard');

    function refreshOptionCardVisual(card, checked) {
        if (!card) return;
        card.classList.toggle('active', !!checked);
    }

    // Radio Gender
    document.querySelectorAll('.option-card[data-radio-gender] input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.option-card[data-radio-gender]').forEach(c => c.classList.remove('active'));
            this.closest('.option-card')?.classList.add('active');
            updatePreview();
        });
    });

    // Checkbox cards
    document.querySelectorAll('.option-card[data-check-card] input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function () {
            refreshOptionCardVisual(this.closest('.option-card'), this.checked);
            if (this.id === 'chkIsIntern') {
                syncInternRules();
            }
            updatePreview();
        });
    });

    // Role row toggles
    document.querySelectorAll('.chk-role').forEach(cb => {
        cb.addEventListener('change', function () {
            const row = this.closest('[data-role-row]');
            row.classList.toggle('selected', this.checked);
            const radioWrap = row.querySelector('.primary-radio-wrap');
            if (radioWrap) radioWrap.classList.toggle('d-none', !this.checked);

            // Əgər ilk dəfə seçilirsə və heç bir primary yoxdursa, bunu primary edirik
            if (this.checked && !document.querySelector('input[name="primary_worker_type_id"]:checked')) {
                const r = row.querySelector('input[type="radio"]');
                if (r) r.checked = true;
            }

            syncGamesVisibility();
            updatePreview();
        });
    });

    function getSelectedGender() {
        return document.querySelector('input[name="gender"]:checked')?.value || 'male';
    }

    function syncInternRules() {
        const isIntern = chkIsIntern?.checked || false;
        document.querySelectorAll('[data-role-row]').forEach(row => {
            const canIntern = row.dataset.canIntern === '1';
            const cb = row.querySelector('.chk-role');
            if (isIntern && !canIntern) {
                if (cb) {
                    cb.checked = false;
                    cb.disabled = true;
                }
                row.classList.remove('selected');
                row.style.opacity = '0.45';
                const radioWrap = row.querySelector('.primary-radio-wrap');
                if (radioWrap) radioWrap.classList.add('d-none');
            } else {
                if (cb) cb.disabled = false;
                row.style.opacity = '1';
            }
        });
    }

    function syncGamesVisibility() {
        let requiresGame = false;
        document.querySelectorAll('[data-role-row]').forEach(row => {
            const cb = row.querySelector('.chk-role');
            if (cb && cb.checked && row.dataset.requiresGame === '1') {
                requiresGame = true;
            }
        });
        if (gamesCard) {
            gamesCard.style.display = requiresGame ? 'block' : 'none';
        }
    }

    function updatePreview() {
        const full = (inpFullName?.value || '').trim();
        const explicitLast = (inpLastName?.value || '').trim();
        const tabelDisplay = (inpTabelDisplayName?.value || '').trim();

        let displayName = tabelDisplay || explicitLast;
        if (!displayName && full) {
            const parts = full.split(/\s+/);
            displayName = parts.length > 1 ? parts.slice(1).join(' ') : parts[0];
        }
        if (!displayName) displayName = 'Əliyev';
        if (prevTabelName) prevTabelName.textContent = displayName;

        // Status & Intern
        const isIntern = chkIsIntern?.checked || false;
        if (prevStatus) {
            prevStatus.textContent = isIntern ? 'Stajor İşçi' : 'Daimi İşçi';
            prevStatus.style.borderColor = isIntern ? '#fde68a' : '#cbd5e1';
            prevStatus.style.background = isIntern ? '#fffbeb' : '#ffffff';
            prevStatus.style.color = isIntern ? '#b45309' : '#0f172a';
        }

        // Branch
        if (prevBranch && selPrimaryBranch) {
            const selText = selPrimaryBranch.options[selPrimaryBranch.selectedIndex]?.text || '';
            prevBranch.textContent = selText ? selText.replace(/—.*—/, 'Mərkəzi Ofis') : 'Təyin edilməyib';
        }

        // Role code preview
        const gender = getSelectedGender();
        const selectedCodes = [];
        document.querySelectorAll('[data-role-row]').forEach(row => {
            const cb = row.querySelector('.chk-role');
            if (cb && cb.checked) {
                const codeBadge = row.querySelector('.badge')?.textContent.trim() || '';
                selectedCodes.push(codeBadge);
            }
        });

        if (selectedCodes.length === 0) {
            if (prevTabelRole) prevTabelRole.textContent = 'vəzifəsiz';
        } else {
            let roleStr = selectedCodes.join('-');
            if (isIntern) {
                roleStr = 'staj-' + roleStr;
            }
            if (prevTabelRole) prevTabelRole.textContent = roleStr;
        }
    }

    inpFullName?.addEventListener('input', function () {
        const val = this.value.trim();
        if (val) {
            const parts = val.split(/\s+/);
            if (inpFirstName && !inpFirstName.value) inpFirstName.value = parts[0] || '';
            if (inpLastName && !inpLastName.value && parts.length > 1) inpLastName.value = parts.slice(1).join(' ');
        }
        updatePreview();
    });

    [inpFirstName, inpLastName, inpTabelDisplayName].forEach(el => {
        el?.addEventListener('input', updatePreview);
    });

    selPrimaryBranch?.addEventListener('change', updatePreview);

    syncInternRules();
    syncGamesVisibility();
    updatePreview();
});
</script>
@endpush
