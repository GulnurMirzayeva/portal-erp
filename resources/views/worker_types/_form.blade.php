@push('css')
<style>
    .att-wrapper {
        padding-bottom: 50px;
        max-width: 1120px;
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

    /* Labels & Standard Inputs */
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
    .att-textarea-standard:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.14);
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

    /* Input Group with Addon */
    .att-input-group {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.15s ease;
    }
    .att-input-group:focus-within {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.14);
    }
    .att-input-field {
        flex: 1;
        border: none;
        outline: none;
        padding: 11px 14px;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        background: transparent;
    }
    .att-input-addon {
        background: #f8fafc;
        border-left: 1px solid #e2e8f0;
        padding: 11px 16px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
    }

    /* Option / Rule Toggle Cards */
    .option-card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    @media (max-width: 640px) {
        .option-card-grid { grid-template-columns: 1fr; }
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
    .custom-radio-indicator {
        border-radius: 50%;
    }
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
    .rules-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    @media (max-width: 640px) {
        .rules-two-col { grid-template-columns: 1fr; }
    }

    /* Preset Chips */
    .preset-chips-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .preset-chip {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .preset-chip:hover {
        background: #ffffff;
        border-color: #107c41;
        color: #107c41;
    }

    /* Info Tip Box */
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

    /* Live Preview Card */
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
    }
    .preview-row:last-child { border-bottom: none; }
    .preview-pill {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #0f172a;
        padding: 3px 11px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 12.5px;
    }

    /* Alert Error */
    .att-alert-danger {
        background: #fef2f2;
        border: 1.5px solid #fecaca;
        border-radius: 16px;
        padding: 14px 20px;
        margin-bottom: 22px;
        color: #991b1b;
    }

    /* Bottom Action Bar */
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
        color: #ffffff !important;
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

    .game-rule-row {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 9px 10px;
        display: flex;
        align-items: center;
        gap: 8px;
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

<div class="row g-4">
    {{-- Sol Sütun --}}
    <div class="col-lg-7">
        {{-- 1. Əsas Kimlik & Kateqoriya --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-emerald">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div>
                    <h5 class="settings-title">1. Əsas Kimlik, Cinsiyyət & Sahə</h5>
                    <p class="settings-desc">Vəzifənin adını, cinsiyyətə görə fərqli görünüşünü, tabel qısa kodunu və işlədiyi sahəni təyin edin.</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="att-field-label">Vəzifə Adı <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="wtName" class="att-input-standard" value="{{ old('name', $workerType->name) }}" placeholder="Məs: Aktyor, Kurator, Resepşn..." required>
                </div>
                <div class="col-md-6">
                    <label class="att-field-label">Qadın Cinsi Üçün Ad (Opsional)</label>
                    <input type="text" name="female_name" id="wtFemaleName" class="att-input-standard" value="{{ old('female_name', $workerType->female_name) }}" placeholder="Məs: Aktrisa">
                </div>

                <div class="col-md-4">
                    <label class="att-field-label">Tabel Qısa Kodu <span class="text-danger">*</span></label>
                    <input type="text" name="short_code" id="wtShortCode" class="att-input-standard font-monospace" value="{{ old('short_code', $workerType->short_code) }}" placeholder="K, adm, res, akt" required>
                </div>
                <div class="col-md-4">
                    <label class="att-field-label">Qadın Qısa Kodu</label>
                    <input type="text" name="female_short_code" id="wtFemaleShortCode" class="att-input-standard font-monospace" value="{{ old('female_short_code', $workerType->female_short_code) }}" placeholder="Məs: aktrisa">
                </div>
                <div class="col-md-4">
                    <label class="att-field-label">Tabel Sırası <span class="text-danger">*</span></label>
                    <input type="number" name="tabel_sort_order" class="att-input-standard" value="{{ old('tabel_sort_order', $workerType->tabel_sort_order) }}" min="1" max="9999" required>
                </div>

                {{-- İş Sahəsi Option Cards --}}
                <div class="col-12 mt-3">
                    <label class="att-field-label">İş Sahəsi <span class="text-danger">*</span></label>
                    @php $selScope = old('scope', $workerType->scope); @endphp
                    <div class="option-card-grid" id="scopeGroup">
                        <label class="option-card {{ $selScope === 'branch' ? 'active' : '' }}" data-radio-group="scope">
                            <input type="radio" name="scope" value="branch" {{ $selScope === 'branch' ? 'checked' : '' }}>
                            <div class="custom-check-indicator custom-radio-indicator">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">Filial</div>
                                <p class="opt-desc">Yalnız filiallara aid vəzifə</p>
                            </div>
                        </label>
                        <label class="option-card {{ $selScope === 'office' ? 'active' : '' }}" data-radio-group="scope">
                            <input type="radio" name="scope" value="office" {{ $selScope === 'office' ? 'checked' : '' }}>
                            <div class="custom-check-indicator custom-radio-indicator">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">Ofis</div>
                                <p class="opt-desc">Mərkəzi ofis heyəti</p>
                            </div>
                        </label>
                        <label class="option-card {{ $selScope === 'both' ? 'active' : '' }}" data-radio-group="scope">
                            <input type="radio" name="scope" value="both" {{ $selScope === 'both' ? 'checked' : '' }}>
                            <div class="custom-check-indicator custom-radio-indicator">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">Hər İkisi</div>
                                <p class="opt-desc">Həm filial, həm ofis</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Kateqoriya Option Cards --}}
                <div class="col-12 mt-3">
                    <label class="att-field-label">Vəzifə Kateqoriyası <span class="text-danger">*</span></label>
                    @php $selCat = old('category', $workerType->category); @endphp
                    <div class="option-card-grid" id="categoryGroup">
                        <label class="option-card {{ $selCat === 'operational' ? 'active' : '' }}" data-radio-group="category">
                            <input type="radio" name="category" value="operational" {{ $selCat === 'operational' ? 'checked' : '' }}>
                            <div class="custom-check-indicator custom-radio-indicator">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">Bonus Gətirən</div>
                                <p class="opt-desc">Satışda seçilən rollar (Resepşn, Aktyor...)</p>
                            </div>
                        </label>
                        <label class="option-card {{ $selCat === 'management' ? 'active' : '' }}" data-radio-group="category">
                            <input type="radio" name="category" value="management" {{ $selCat === 'management' ? 'checked' : '' }}>
                            <div class="custom-check-indicator custom-radio-indicator">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">İdarəedici</div>
                                <p class="opt-desc">Kurator, Admin, Supervayzer (satışsız)</p>
                            </div>
                        </label>
                        <label class="option-card {{ $selCat === 'support' ? 'active' : '' }}" data-radio-group="category">
                            <input type="radio" name="category" value="support" {{ $selCat === 'support' ? 'checked' : '' }}>
                            <div class="custom-check-indicator custom-radio-indicator">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="opt-title">Dəstək / İnzibati</div>
                                <p class="opt-desc">Xadimə, Mühasib, HR, Texnik...</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="col-md-5 mt-3">
                    <label class="att-field-label">Defolt Ödəniş Modeli</label>
                    <select name="default_pay_model" class="att-input-standard att-select-standard">
                        @foreach(\App\Models\WorkerType::PAY_MODELS as $key => $label)
                            <option value="{{ $key }}" {{ old('default_pay_model', $workerType->default_pay_model) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 mt-3" id="saleSlotWrapper">
                    <label class="att-field-label">Satış Qrupu (PortalWebsite)</label>
                    <select name="sale_slot_key" id="wtSaleSlot" class="att-input-standard att-select-standard">
                        <option value="">— Seçilməyib —</option>
                        @foreach(\App\Models\WorkerType::SALE_SLOTS as $key => $label)
                            <option value="{{ $key }}" {{ old('sale_slot_key', $workerType->sale_slot_key) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mt-3">
                    <label class="att-field-label">Rəng</label>
                    <input type="color" name="badge_color" id="wtColor" class="att-input-standard p-1" value="{{ old('badge_color', $workerType->badge_color ?: '#107c41') }}" style="cursor: pointer;">
                </div>

                <div class="col-12">
                    <label class="att-field-label">Qısa Qeyd / İzah</label>
                    <textarea name="description" class="att-textarea-standard" rows="2" placeholder="Bu vəzifə haqqında qısa izah...">{{ old('description', $workerType->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. Dinamik Sistem Qaydaları --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-blue">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                </div>
                <div>
                    <h5 class="settings-title">2. Dinamik Davranış Qaydaları</h5>
                    <p class="settings-desc">Bu vəzifənin stajorluq, tabel, əvəzetmə və oyun bağlantısı davranışını təyin edin.</p>
                </div>
            </div>

            <div class="rules-two-col">
                <label class="option-card {{ old('is_bonus_eligible', $workerType->is_bonus_eligible) ? 'active' : '' }}" data-check-card>
                    <input type="checkbox" name="is_bonus_eligible" id="chkBonusEligible" value="1" {{ old('is_bonus_eligible', $workerType->is_bonus_eligible) ? 'checked' : '' }}>
                    <div class="custom-check-indicator">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-title">Satışda Seçilir & Bonus Gətirir</div>
                        <p class="opt-desc">Oyun satışında siyahıda görünür və yarış xalı hesablanır.</p>
                    </div>
                </label>

                <label class="option-card {{ old('can_have_intern', $workerType->can_have_intern) ? 'active' : '' }}" id="internBox" data-check-card>
                    <input type="checkbox" name="can_have_intern" id="chkCanHaveIntern" value="1" {{ old('can_have_intern', $workerType->can_have_intern) ? 'checked' : '' }}>
                    <div class="custom-check-indicator">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-title">Bu Vəzifə Üzrə Stajor Olar</div>
                        <p class="opt-desc">Yalnız bonus gətirən rollarda staj (<code>staj-res</code>, <code>staj-akt</code>) açılır.</p>
                    </div>
                </label>

                <label class="option-card {{ old('requires_game', $workerType->requires_game) ? 'active' : '' }}" data-check-card>
                    <input type="checkbox" name="requires_game" id="chkRequiresGame" value="1" {{ old('requires_game', $workerType->requires_game) ? 'checked' : '' }}>
                    <div class="custom-check-indicator">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-title">İşçiyə Oyun Bağlansın (Drakula, Kor...)</div>
                        <p class="opt-desc">Aktyor/Aktrisa işçi kartında və tabeldə hansı oyunda işlədiyini seçmək üçün.</p>
                    </div>
                </label>

                <label class="option-card {{ old('show_in_tabel', $workerType->show_in_tabel) ? 'active' : '' }}" data-check-card>
                    <input type="checkbox" name="show_in_tabel" id="chkShowInTabel" value="1" {{ old('show_in_tabel', $workerType->show_in_tabel) ? 'checked' : '' }}>
                    <div class="custom-check-indicator">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-title">Filial Tabelində Göstərilsin</div>
                        <p class="opt-desc">Filialın aylıq tabelində sütun kimi çıxır (Ofis rollarında bağlı olur).</p>
                    </div>
                </label>

                <label class="option-card {{ old('can_substitute', $workerType->can_substitute) ? 'active' : '' }}" data-check-card>
                    <input type="checkbox" name="can_substitute" id="chkCanSubstitute" value="1" {{ old('can_substitute', $workerType->can_substitute) ? 'checked' : '' }}>
                    <div class="custom-check-indicator">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-title">Əvəzedici Kimi Çıxa Bilər</div>
                        <p class="opt-desc">Başqa filialda əvəz kimi işləməyə icazə verir.</p>
                    </div>
                </label>

                <label class="option-card {{ old('is_active', $workerType->is_active ?? true) ? 'active' : '' }}" data-check-card>
                    <input type="checkbox" name="is_active" id="chkIsActive" value="1" {{ old('is_active', $workerType->is_active ?? true) ? 'checked' : '' }}>
                    <div class="custom-check-indicator">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="opt-title">Aktiv Status</div>
                        <p class="opt-desc">Deaktiv olduqda yeni işçilər üçün siyahıda görünmür.</p>
                    </div>
                </label>
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
                    <h5 class="settings-title">Canlı Tabel & Etiket Önizləməsi</h5>
                    <p class="settings-desc">Bu vəzifənin tabeldə və əvəzetmələrdə necə görünəcəyi.</p>
                </div>
            </div>

            <div class="preview-list">
                <div class="preview-row">
                    <span class="text-muted fw-semibold">Daimi işçi (Kişi):</span>
                    <span class="preview-pill" id="prevMale">Əliyev / akt</span>
                </div>
                <div class="preview-row">
                    <span class="text-muted fw-semibold">Daimi işçi (Qadın):</span>
                    <span class="preview-pill" id="prevFemale">Vəliyeva / aktrisa</span>
                </div>
                <div class="preview-row">
                    <span class="text-muted fw-semibold">Stajor:</span>
                    <span class="preview-pill" style="border-color: #fde68a; background: #fffbeb; color: #b45309;" id="prevIntern">Səlimova / staj-akt</span>
                </div>
                <div class="preview-row">
                    <span class="text-muted fw-semibold">Əvəzedici:</span>
                    <span class="preview-pill" style="border-color: #bfdbfe; background: #eff6ff; color: #1d4ed8;" id="prevSub">əvəz aktyor / əvəz aktrisa</span>
                </div>
            </div>
        </div>

        {{-- 3. Vizual Yarış Bonusu Qaydaları --}}
        <div class="settings-card" id="bonusRulesCard">
            <div class="settings-card-header">
                <div class="settings-icon-badge badge-amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <div>
                    <h5 class="settings-title">3. Vizual Yarış Bonusu Qaydaları</h5>
                    <p class="settings-desc">PortalWebsite satışlarındakı xal yarışması üçün rol və oyun xalları.</p>
                </div>
            </div>

            <div class="att-info-box mb-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" style="flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                <div>
                    <strong>Qayda:</strong> Stajorların vizual xalı avtomatik <strong>0</strong> olur. Bir işçi bir oyunda bir neçə rolda seçilərsə, yalnız ən yüksək xal götürülür. (<code>+1</code> və <code>00:00</code> pul bonusları ERP maaş bölməsində hesablanacaq).
                </div>
            </div>

            <div class="mb-4">
                <label class="att-field-label">Default Xal (Bütün oyunlar üçün)</label>
                <div class="att-input-group">
                    <input type="number" step="0.01" min="0" max="9999" id="defaultPointsInput" name="default_points" class="att-input-field" value="{{ old('default_points', $defaultPoints) }}" placeholder="Məs: 1.00 və ya 0.30">
                    <span class="att-input-addon">xal</span>
                </div>
                <div class="preset-chips-row">
                    <span class="small fw-bold text-muted">Tez seçim:</span>
                    <span class="preset-chip" onclick="setPointsPreset('0.30')">0.30</span>
                    <span class="preset-chip" onclick="setPointsPreset('0.50')">0.50</span>
                    <span class="preset-chip" onclick="setPointsPreset('1.00')">1.00</span>
                    <span class="preset-chip" onclick="setPointsPreset('1.50')">1.50</span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="att-field-label mb-0">Oyunlara Görə Xüsusi Xallar</label>
                <button type="button" class="att-btn att-btn-outline py-1 px-3" id="btnAddGameRule" style="font-size: 12px; border-radius: 10px;">
                    + Oyun Əlavə Et
                </button>
            </div>

            @php
                $existingGameRules = old('game_rules', $gameRules ?? []);
            @endphp

            <div id="gameRulesContainer" class="d-flex flex-column gap-2">
                @foreach($existingGameRules as $idx => $gr)
                    <div class="game-rule-row">
                        @if(!empty($games))
                            <select name="game_rules[{{ $idx }}][portal_game_id]" class="att-input-standard att-select-standard flex-grow-1" style="height: 38px; font-size: 12.5px;">
                                <option value="">Oyun seçin...</option>
                                @foreach($games as $g)
                                    <option value="{{ $g['id'] }}" {{ (int)($gr['portal_game_id'] ?? 0) === (int)$g['id'] ? 'selected' : '' }}>
                                        {{ $g['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="number" name="game_rules[{{ $idx }}][portal_game_id]" class="att-input-standard" style="width: 85px; height: 38px; font-size: 12.5px;" placeholder="Oyun ID" value="{{ $gr['portal_game_id'] ?? '' }}">
                            <input type="text" name="game_rules[{{ $idx }}][game_name]" class="att-input-standard flex-grow-1" style="height: 38px; font-size: 12.5px;" placeholder="Oyun adı" value="{{ $gr['game_name'] ?? '' }}">
                        @endif
                        <input type="number" step="0.01" min="0" name="game_rules[{{ $idx }}][points]" class="att-input-standard" style="width: 85px; height: 38px; font-size: 12.5px;" placeholder="Xal" value="{{ $gr['points'] ?? '' }}">
                        <input type="hidden" name="game_rules[{{ $idx }}][is_active]" value="1">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-game-rule" style="border-radius: 9px; height: 36px; width: 36px;">&times;</button>
                    </div>
                @endforeach
            </div>

            <div id="noGameRulesHint" class="text-muted small text-center py-3 border rounded-3 mt-1 {{ count($existingGameRules) > 0 ? 'd-none' : '' }}" style="border-style: dashed !important;">
                Hazırda oyun üzrə fərqli xal yoxdur (hamısına Default xal tətbiq edilir).
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function setPointsPreset(val) {
    const inp = document.getElementById('defaultPointsInput');
    if (inp) inp.value = val;
}

document.addEventListener('DOMContentLoaded', function () {
    const wtName = document.getElementById('wtName');
    const wtFemaleName = document.getElementById('wtFemaleName');
    const wtShortCode = document.getElementById('wtShortCode');
    const wtFemaleShortCode = document.getElementById('wtFemaleShortCode');
    const chkBonus = document.getElementById('chkBonusEligible');
    const chkIntern = document.getElementById('chkCanHaveIntern');
    const chkShowInTabel = document.getElementById('chkShowInTabel');
    const bonusCard = document.getElementById('bonusRulesCard');
    const saleSlotWrapper = document.getElementById('saleSlotWrapper');
    const internBox = document.getElementById('internBox');

    function getCheckedValue(name) {
        return document.querySelector(`input[name="${name}"]:checked`)?.value || '';
    }

    function refreshCardVisual(card, checked) {
        if (!card) return;
        card.classList.toggle('active', !!checked);
    }

    function syncRulesState() {
        const catVal = getCheckedValue('category');
        const isOp = catVal === 'operational';

        if (!isOp) {
            chkBonus.checked = false;
            chkBonus.disabled = true;
            const bonusCardLabel = chkBonus.closest('.option-card');
            refreshCardVisual(bonusCardLabel, false);
            if (bonusCardLabel) bonusCardLabel.style.opacity = '0.6';
        } else {
            chkBonus.disabled = false;
            const bonusCardLabel = chkBonus.closest('.option-card');
            refreshCardVisual(bonusCardLabel, chkBonus.checked);
            if (bonusCardLabel) bonusCardLabel.style.opacity = '1';
        }

        const bonusAllowed = isOp && chkBonus.checked;
        if (!bonusAllowed) {
            chkIntern.checked = false;
            chkIntern.disabled = true;
            refreshCardVisual(internBox, false);
            internBox.style.opacity = '0.55';
            bonusCard.style.opacity = '0.55';
            saleSlotWrapper.style.opacity = '0.55';
        } else {
            chkIntern.disabled = false;
            refreshCardVisual(internBox, chkIntern.checked);
            internBox.style.opacity = '1';
            bonusCard.style.opacity = '1';
            saleSlotWrapper.style.opacity = '1';
        }

        updatePreview();
    }

    function updatePreview() {
        const name = (wtName.value || 'Vəzifə').trim();
        const fName = (wtFemaleName.value || name).trim();
        const code = (wtShortCode.value || 'kod').trim();
        const fCode = (wtFemaleShortCode.value || code).trim();

        document.getElementById('prevMale').textContent = `Əliyev / ${code}`;
        document.getElementById('prevFemale').textContent = `Vəliyeva / ${fCode}`;
        document.getElementById('prevIntern').textContent = chkIntern.checked ? `Səlimova / staj-${code}` : 'Bu rolda stajor olmur';
        document.getElementById('prevSub').textContent = `əvəz ${name.toLowerCase()} / əvəz ${fName.toLowerCase()}`;
    }

    // Radio option cards (scope & category)
    document.querySelectorAll('.option-card[data-radio-group] input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const card = this.closest('.option-card');
            const group = card?.dataset.radioGroup;
            if (!group) return;

            document.querySelectorAll(`.option-card[data-radio-group="${group}"]`).forEach(c => c.classList.remove('active'));
            card.classList.add('active');

            if (group === 'scope') {
                const show = this.value !== 'office';
                chkShowInTabel.checked = show;
                refreshCardVisual(chkShowInTabel.closest('.option-card'), show);
            }
            if (group === 'category') {
                if (this.value === 'operational') {
                    chkBonus.checked = true;
                }
                syncRulesState();
            }
        });
    });

    // Checkbox option cards
    document.querySelectorAll('.option-card[data-check-card] input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function () {
            refreshCardVisual(this.closest('.option-card'), this.checked);
            if (this.id === 'chkBonusEligible') {
                syncRulesState();
            } else if (this.id === 'chkCanHaveIntern') {
                updatePreview();
            }
        });
    });

    [wtName, wtFemaleName, wtShortCode, wtFemaleShortCode].forEach(el => {
        el?.addEventListener('input', updatePreview);
    });

    syncRulesState();

    // Dynamic game rules rows
    const gamesList = @json($games ?? []);
    const container = document.getElementById('gameRulesContainer');
    const hint = document.getElementById('noGameRulesHint');
    let nextIdx = {{ count($existingGameRules) }};

    function refreshHint() {
        if (!hint) return;
        hint.classList.toggle('d-none', container.querySelectorAll('.game-rule-row').length > 0);
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    document.getElementById('btnAddGameRule')?.addEventListener('click', function () {
        const idx = nextIdx++;
        const row = document.createElement('div');
        row.className = 'game-rule-row';

        let gameInputHtml = '';
        if (gamesList.length > 0) {
            const opts = gamesList.map(g => `<option value="${escapeHtml(g.id)}">${escapeHtml(g.label)}</option>`).join('');
            gameInputHtml = `<select name="game_rules[${idx}][portal_game_id]" class="att-input-standard att-select-standard flex-grow-1" style="height: 38px; font-size: 12.5px;"><option value="">Oyun seçin...</option>${opts}</select>`;
        } else {
            gameInputHtml = `<input type="number" name="game_rules[${idx}][portal_game_id]" class="att-input-standard" style="width: 85px; height: 38px; font-size: 12.5px;" placeholder="Oyun ID">` +
                            `<input type="text" name="game_rules[${idx}][game_name]" class="att-input-standard flex-grow-1" style="height: 38px; font-size: 12.5px;" placeholder="Oyun adı">`;
        }

        row.innerHTML = `${gameInputHtml}
            <input type="number" step="0.01" min="0" name="game_rules[${idx}][points]" class="att-input-standard" style="width: 85px; height: 38px; font-size: 12.5px;" placeholder="Xal">
            <input type="hidden" name="game_rules[${idx}][is_active]" value="1">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-game-rule" style="border-radius: 9px; height: 36px; width: 36px;">&times;</button>`;

        container.appendChild(row);
        refreshHint();
    });

    container?.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-game-rule')) {
            e.target.closest('.game-rule-row')?.remove();
            refreshHint();
        }
    });
});
</script>
@endpush
