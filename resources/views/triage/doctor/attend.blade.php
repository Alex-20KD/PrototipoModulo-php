@extends('triage.layouts.app')

@section('title', 'Atender Paciente — Consulta Médica')

@section('content')
<div class="mb-4">
    <a href="{{ route('triage.doctor.index') }}" class="btn btn-outline-glass btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Volver a Citas
    </a>
    <h1 class="section-title"><i class="bi bi-clipboard2-pulse"></i> Consulta Médica</h1>
    <p class="section-subtitle">Atención al paciente — {{ $appointment->appointment_date->format('d/m/Y H:i') }}</p>
</div>

{{-- Validation errors --}}
@if($errors->any())
    <div class="alert-danger-custom mb-4">
        <i class="bi bi-exclamation-triangle me-2"></i><strong>Errores de validación:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Patient & Vital Signs Summary Card --}}
<div class="glass-card mb-4" style="border-left: 3px solid var(--primary-light);">
    <div class="row">
        <div class="col-md-6">
            <h5 style="color: var(--primary-light); font-weight: 600; margin-bottom: 1rem;">
                <i class="bi bi-person-badge me-2"></i>Datos del Paciente
            </h5>
            <div class="info-row">
                <span class="info-label">Nombre:</span>
                <span class="info-value">{{ $appointment->user->nombres }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cédula:</span>
                <span class="info-value">{{ $appointment->user->cedula }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Edad:</span>
                <span class="info-value">{{ $appointment->user->edad }} años</span>
            </div>
            <div class="info-row">
                <span class="info-label">Sexo:</span>
                <span class="info-value">{{ $appointment->user->sexo }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Doctor:</span>
                <span class="info-value">{{ $appointment->doctor->nombres }} — {{ $appointment->doctor->especialidad }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <h5 style="color: var(--accent); font-weight: 600; margin-bottom: 1rem;">
                <i class="bi bi-heart-pulse me-2"></i>Signos Vitales (Triaje)
            </h5>
            @if($appointment->vitalSigns)
                @php $vs = $appointment->vitalSigns; @endphp
                <div class="vitals-grid">
                    <div class="vital-item">
                        <span class="vital-icon"><i class="bi bi-thermometer-half"></i></span>
                        <span class="vital-val">{{ $vs->temperature }} °C</span>
                        <span class="vital-lbl">Temperatura</span>
                    </div>
                    <div class="vital-item">
                        <span class="vital-icon"><i class="bi bi-activity"></i></span>
                        <span class="vital-val">{{ $vs->blood_pressure }}</span>
                        <span class="vital-lbl">Presión Arterial</span>
                    </div>
                    <div class="vital-item">
                        <span class="vital-icon"><i class="bi bi-heart"></i></span>
                        <span class="vital-val">{{ $vs->heart_rate }} bpm</span>
                        <span class="vital-lbl">Frec. Cardíaca</span>
                    </div>
                    <div class="vital-item">
                        <span class="vital-icon"><i class="bi bi-lungs"></i></span>
                        <span class="vital-val">{{ $vs->respiratory_rate ?? '—' }} rpm</span>
                        <span class="vital-lbl">Frec. Respiratoria</span>
                    </div>
                    <div class="vital-item">
                        <span class="vital-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="vital-val">{{ $vs->weight_kg }} kg</span>
                        <span class="vital-lbl">Peso</span>
                    </div>
                    <div class="vital-item">
                        <span class="vital-icon"><i class="bi bi-rulers"></i></span>
                        <span class="vital-val">{{ $vs->height_cm }} cm</span>
                        <span class="vital-lbl">Talla</span>
                    </div>
                </div>
                <div class="mt-3 p-2" style="background: rgba(245,158,11,0.08); border-radius: 8px; border: 1px solid rgba(245,158,11,0.15);">
                    <small class="text-muted" style="color: var(--text-secondary) !important;">
                        <strong style="color: var(--accent);">Motivo de consulta:</strong>
                        {{ $vs->reason_for_consultation }}
                    </small>
                </div>
            @else
                <div class="text-center py-3" style="color: var(--text-secondary);">
                    <i class="bi bi-exclamation-circle me-1"></i> Sin datos de triaje registrados
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Consultation Form --}}
<form action="{{ route('triage.doctor.attend.store', $appointment->id) }}" method="POST" id="consultation-form">
    @csrf

    <div class="glass-card mb-4">
        <h5 class="card-section-title"><i class="bi bi-journal-medical me-2"></i>Anamnesis</h5>

        <div class="mb-0">
            <label for="anamnesis" class="form-label">Anamnesis (Enfermedad Actual) <span class="text-danger">*</span></label>
            <small style="color: var(--text-secondary); display: block; margin-bottom: 0.4rem;">
                📝 Redactar en <strong>tercera persona</strong> o como síntoma.
                Nunca en primera persona (tengo, me duele, siento...).
            </small>
            <textarea class="form-control" id="anamnesis" name="anamnesis" rows="4"
                      placeholder="Paciente refiere... / Describe síntomas en tercera persona o como hallazgo clínico. Ej: 'cefalea intensa de 2 días de evolución'"
                      required>{{ old('anamnesis') }}</textarea>
            <div id="anamnesis-warning" class="alert-primera-persona" style="display:none;">
                ⚠️ <strong>Atención:</strong> El motivo de consulta debe escribirse en
                <strong>tercera persona</strong> o como síntoma.<br>
                <span style="color:#f87171;">✗ Incorrecto:</span> "tengo dolor de cabeza"<br>
                <span style="color:#34d399;">✓ Correcto:</span> "paciente refiere dolor de cabeza"
                o simplemente "cefalea intensa"
            </div>
            <small id="anamnesis-counter" style="color: var(--text-secondary); float:right; margin-top: 4px;">
                0 caracteres (mínimo 10)
            </small>
        </div>
    </div>

    <div class="glass-card mb-4">
        <h5 class="card-section-title"><i class="bi bi-heart-pulse me-2"></i>2. Antecedentes Personales y Familiares</h5>

        {{-- A) HIPERTENSIÓN --}}
        <div class="ant-block mb-3">
            <div class="form-check form-switch ant-switch">
                <input class="form-check-input" type="checkbox" id="ant_hta" name="ant_hta" value="1"
                       {{ old('ant_hta') ? 'checked' : '' }}>
                <label class="form-check-label" for="ant_hta">
                    <i class="bi bi-droplet-half me-1" style="color:#f87171;"></i>
                    ¿El paciente padece <strong>Hipertensión Arterial (HTA)</strong>?
                </label>
            </div>
            <div class="ant-subcard" id="hta-details" style="{{ old('ant_hta') ? '' : 'display:none;' }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="ant_hta_years" class="form-label">Años de evolución</label>
                        <input type="number" class="form-control" id="ant_hta_years" name="ant_hta_years"
                               min="1" max="100" placeholder="Ej: 5"
                               value="{{ old('ant_hta_years') }}">
                    </div>
                    <div class="col-md-8">
                        <div class="form-check form-switch ant-switch mt-md-4">
                            <input class="form-check-input" type="checkbox" id="ant_hta_treatment" name="ant_hta_treatment" value="1"
                                   {{ old('ant_hta_treatment') ? 'checked' : '' }}>
                            <label class="form-check-label" for="ant_hta_treatment">¿Está en tratamiento actualmente?</label>
                        </div>
                    </div>
                    <div class="col-12" id="hta-medication-wrapper" style="{{ old('ant_hta_treatment') ? '' : 'display:none;' }}">
                        <label for="ant_hta_medication" class="form-label">Medicamento que toma</label>
                        <input type="text" class="form-control" id="ant_hta_medication" name="ant_hta_medication"
                               placeholder="Ej: Losartán 50mg, Enalapril..."
                               value="{{ old('ant_hta_medication') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- B) DIABETES --}}
        <div class="ant-block mb-3">
            <div class="form-check form-switch ant-switch">
                <input class="form-check-input" type="checkbox" id="ant_dm" name="ant_dm" value="1"
                       {{ old('ant_dm') ? 'checked' : '' }}>
                <label class="form-check-label" for="ant_dm">
                    <i class="bi bi-clipboard2-pulse me-1" style="color:#fbbf24;"></i>
                    ¿El paciente padece <strong>Diabetes Mellitus (DM)</strong>?
                </label>
            </div>
            <div class="ant-subcard" id="dm-details" style="{{ old('ant_dm') ? '' : 'display:none;' }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="ant_dm_years" class="form-label">Años de evolución</label>
                        <input type="number" class="form-control" id="ant_dm_years" name="ant_dm_years"
                               min="1" max="100" placeholder="Ej: 3"
                               value="{{ old('ant_dm_years') }}">
                    </div>
                    <div class="col-md-8">
                        <div class="form-check form-switch ant-switch mt-md-4">
                            <input class="form-check-input" type="checkbox" id="ant_dm_treatment" name="ant_dm_treatment" value="1"
                                   {{ old('ant_dm_treatment') ? 'checked' : '' }}>
                            <label class="form-check-label" for="ant_dm_treatment">¿Está en tratamiento actualmente?</label>
                        </div>
                    </div>
                    <div class="col-12" id="dm-medication-wrapper" style="{{ old('ant_dm_treatment') ? '' : 'display:none;' }}">
                        <label for="ant_dm_medication" class="form-label">Medicamento que toma</label>
                        <input type="text" class="form-control" id="ant_dm_medication" name="ant_dm_medication"
                               placeholder="Ej: Metformina 850mg, Insulina..."
                               value="{{ old('ant_dm_medication') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- C) ENFERMEDADES CRÓNICAS --}}
        <div class="ant-block mb-3">
            <label class="form-label" style="color: var(--text-primary); font-weight: 600;">
                <i class="bi bi-bandaid me-1" style="color: var(--primary-light);"></i>
                Otras enfermedades crónicas relevantes
            </label>
            @php $oldChronic = old('ant_chronic', []); @endphp
            <div class="chronic-grid">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ant_chronic[]" value="tiroides" id="chronic_tiroides"
                           {{ in_array('tiroides', $oldChronic) ? 'checked' : '' }}>
                    <label class="form-check-label" for="chronic_tiroides">Enfermedad tiroidea</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ant_chronic[]" value="vih" id="chronic_vih"
                           {{ in_array('vih', $oldChronic) ? 'checked' : '' }}>
                    <label class="form-check-label" for="chronic_vih">VIH / SIDA</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ant_chronic[]" value="ets" id="chronic_ets"
                           {{ in_array('ets', $oldChronic) ? 'checked' : '' }}>
                    <label class="form-check-label" for="chronic_ets">Enfermedad de transmisión sexual (ETS)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ant_chronic[]" value="psiquiatrica" id="chronic_psiquiatrica"
                           {{ in_array('psiquiatrica', $oldChronic) ? 'checked' : '' }}>
                    <label class="form-check-label" for="chronic_psiquiatrica">Trastorno psiquiátrico</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ant_chronic[]" value="cancer" id="chronic_cancer"
                           {{ in_array('cancer', $oldChronic) ? 'checked' : '' }}>
                    <label class="form-check-label" for="chronic_cancer">Cáncer (oncológico)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ant_chronic[]" value="cardiopatia" id="chronic_cardiopatia"
                           {{ in_array('cardiopatia', $oldChronic) ? 'checked' : '' }}>
                    <label class="form-check-label" for="chronic_cardiopatia">Cardiopatía</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ant_chronic[]" value="otra" id="chronic_otra"
                           {{ in_array('otra', $oldChronic) ? 'checked' : '' }}>
                    <label class="form-check-label" for="chronic_otra">Otra</label>
                </div>
            </div>
            <div id="chronic-other-wrapper" class="mt-2" style="{{ in_array('otra', $oldChronic) ? '' : 'display:none;' }}">
                <input type="text" class="form-control" name="ant_chronic_other" id="ant_chronic_other"
                       placeholder="Especifique la enfermedad"
                       value="{{ old('ant_chronic_other') }}">
            </div>
        </div>

        {{-- D) OBSERVACIONES --}}
        <div class="ant-block">
            <label for="ant_observations" class="form-label" style="color: var(--text-primary); font-weight: 600;">
                <i class="bi bi-pencil-square me-1" style="color: var(--primary-light);"></i>
                Observaciones adicionales de antecedentes
            </label>
            <textarea class="form-control" id="ant_observations" name="ant_observations" rows="3"
                      placeholder="Antecedentes quirúrgicos, alergias, antecedentes familiares relevantes...">{{ old('ant_observations') }}</textarea>
        </div>
    </div>

    <div class="glass-card mb-4">
        <h5 class="card-section-title"><i class="bi bi-clipboard2-pulse me-2"></i>3. Examen Físico Regional</h5>
        <p class="section-subtitle">📋 Exclusivo del médico. Describa hallazgos por sistemas: cardiovascular, respiratorio, abdominal, neurológico, etc.</p>
        <textarea class="form-control" name="physical_exam" rows="5" maxlength="2000"
                  placeholder="Cardiovascular: ruidos cardíacos rítmicos, sin soplos. Respiratorio: murmullo vesicular conservado...">{{ old('physical_exam') }}</textarea>
    </div>

    @php
        $diagTypes = [
            'presuntivo_ingreso' => ['label' => 'Presuntivo de Ingreso', 'desc' => 'Sospecha al momento de la admisión', 'icon' => '🔍'],
            'definitivo_ingreso' => ['label' => 'Definitivo de Ingreso', 'desc' => 'Confirmado al momento de la admisión', 'icon' => '✅'],
            'presuntivo_alta' => ['label' => 'Presuntivo de Alta', 'desc' => 'Aún en sospecha al dar el alta', 'icon' => '⚠️'],
            'definitivo_alta' => ['label' => 'Definitivo de Alta', 'desc' => 'Confirmado al momento del alta', 'icon' => '✔️'],
        ];
    @endphp
    <div class="glass-card mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
            <h5 class="card-section-title mb-0"><i class="bi bi-search me-2"></i>4. Diagnósticos (CIE-10)</h5>
            <button type="button" id="add-diagnosis" class="btn btn-outline-glass btn-sm"><i class="bi bi-plus-circle me-1"></i> Agregar Diagnóstico</button>
        </div>
        <p class="section-subtitle">Agregue todos los diagnósticos del paciente. El primero se considera el principal.</p>
        <div id="diagnoses-container"></div>
        <div id="diagnoses-client-error" class="field-error" style="display:none;">Debe agregar al menos un diagnóstico CIE-10.</div>
    </div>

    <template id="diagnosis-template">
        <div class="diagnosis-row" data-index="__INDEX__">
            <div class="diagnosis-row-header">
                <span class="diagnosis-row-title"></span>
                <button type="button" class="btn-remove-diagnosis"><i class="bi bi-trash me-1"></i>Eliminar</button>
            </div>
            <div class="row">
                <div class="col-md-7 mb-3">
                    <label class="form-label">Buscar Diagnóstico CIE-10 <span class="text-danger">*</span></label>
                    <div class="diagnosis-search-wrap">
                        <input type="text" class="form-control diagnosis-search" id="diag-search-__INDEX__" placeholder="Escriba al menos 2 caracteres... (ej: diabetes, J00, gastritis)" autocomplete="off" required>
                        <input type="hidden" class="diagnosis-code" name="diagnoses[__INDEX__][cie10_code]" required>
                        <input type="hidden" class="diagnosis-primary" name="diagnoses[__INDEX__][is_primary]" value="0">
                        <div class="diagnosis-results cie10-dropdown"></div>
                    </div>
                    <div class="diagnosis-selected cie10-selected-badge" style="display:none;">
                        <span class="diagnosis-selected-text"></span>
                        <button type="button" class="diagnosis-clear cie10-clear-btn" title="Cambiar diagnóstico"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Tipo de Diagnóstico <span class="text-danger">*</span></label>
                    <div class="diagnosis-type-grid">
                        @foreach($diagTypes as $value => $type)
                            <label class="diagnosis-card" for="diag-__INDEX__-{{ $value }}">
                                <input type="radio" id="diag-__INDEX__-{{ $value }}" name="diagnoses[__INDEX__][diagnosis_type]" value="{{ $value }}" style="display:none;" required>
                                <span class="diag-icon">{{ $type['icon'] }}</span>
                                <span class="diag-label">{{ $type['label'] }}</span>
                                <span class="diag-desc">{{ $type['desc'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </template>

    <div class="glass-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-section-title mb-0"><i class="bi bi-capsule me-2"></i>Receta / Plan de Tratamiento</h5>
            <button type="button" id="add-prescription" class="btn btn-outline-glass btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Agregar Medicamento
            </button>
        </div>

        <div id="prescriptions-container">
            {{-- Dynamic prescription rows inserted here --}}
        </div>

        <div id="no-prescriptions" class="text-center py-3" style="color: var(--text-secondary);">
            <i class="bi bi-info-circle me-1"></i> No se han agregado medicamentos. Haga clic en "Agregar Medicamento" para añadir.
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mb-4">
        <a href="{{ route('triage.doctor.index') }}" class="btn btn-outline-glass">
            <i class="bi bi-x-circle me-1"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary-custom">
            <i class="bi bi-check-circle me-1"></i> Guardar Consulta y Finalizar
        </button>
    </div>
</form>
@endsection

@section('styles')
<style>
    .info-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
    }
    .info-label {
        color: var(--text-secondary);
        font-weight: 500;
        min-width: 80px;
    }
    .info-value {
        color: var(--text-primary);
        font-weight: 600;
    }

    .vitals-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    .vital-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
        border-radius: 10px;
        padding: 0.6rem 0.4rem;
        transition: all 0.3s ease;
    }
    .vital-item:hover {
        border-color: rgba(20, 184, 166, 0.3);
        background: rgba(20, 184, 166, 0.05);
    }
    .vital-icon {
        font-size: 1.1rem;
        color: var(--primary-light);
        margin-bottom: 0.2rem;
    }
    .vital-val {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-primary);
    }
    .vital-lbl {
        font-size: 0.7rem;
        color: var(--text-secondary);
        text-align: center;
    }

    .card-section-title {
        font-weight: 600;
        font-size: 1.1rem;
        background: linear-gradient(135deg, var(--primary-light), var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* CIE-10 Dropdown */
    .cie10-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        max-height: 280px;
        overflow-y: auto;
        display: none;
        border-radius: 0 0 10px 10px;
        border: 1px solid var(--glass-border);
        border-top: none;
        background: rgba(30, 41, 59, 0.98);
        backdrop-filter: blur(20px);
    }
    .cie10-dropdown.show { display: block; }
    .cie10-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .cie10-item:hover {
        background: rgba(20, 184, 166, 0.12);
    }
    .cie10-item:last-child { border-bottom: none; }
    .cie10-item-code {
        background: rgba(20, 184, 166, 0.15);
        color: var(--primary-light);
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        min-width: 50px;
        text-align: center;
    }
    .cie10-item-desc {
        color: var(--text-primary);
        font-size: 0.875rem;
    }
    .cie10-no-results {
        padding: 0.75rem 1rem;
        color: var(--text-secondary);
        font-size: 0.875rem;
        text-align: center;
    }

    .cie10-selected-badge {
        margin-top: 0.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(20, 184, 166, 0.12);
        border: 1px solid rgba(20, 184, 166, 0.3);
        border-radius: 8px;
        padding: 0.4rem 0.75rem;
        font-size: 0.875rem;
        color: var(--primary-light);
        font-weight: 500;
    }
    .cie10-clear-btn {
        background: none;
        border: none;
        color: #f87171;
        cursor: pointer;
        font-size: 0.85rem;
        padding: 0.15rem 0.3rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    .cie10-clear-btn:hover {
        background: rgba(239, 68, 68, 0.15);
    }
    .diagnosis-row {
        background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border);
        border-radius: 12px; padding: 1rem; margin-bottom: 0.9rem;
    }
    .diagnosis-row-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
    .diagnosis-row-title { color: var(--primary-light); font-size: 0.9rem; font-weight: 600; }
    .btn-remove-diagnosis { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; border-radius: 6px; font-size: 0.8rem; padding: 0.25rem 0.6rem; }
    .diagnosis-search-wrap { position: relative; }

    /* Prescription rows */
    .prescription-row {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        animation: fadeIn 0.3s ease-out;
    }
    .prescription-row:hover {
        border-color: rgba(20, 184, 166, 0.2);
    }
    .prescription-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .prescription-number {
        font-weight: 600;
        color: var(--primary-light);
        font-size: 0.85rem;
    }
    .btn-remove-rx {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #f87171;
        font-size: 0.8rem;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-remove-rx:hover {
        background: rgba(239, 68, 68, 0.25);
        border-color: rgba(239, 68, 68, 0.4);
    }
    .medication-search-wrap { position: relative; }
    .medication-dropdown {
        position: absolute;
        top: 100%; left: 0; right: 0; z-index: 1060;
        display: none; max-height: 260px; overflow-y: auto;
        background: rgba(30, 41, 59, 0.98); backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border); border-radius: 0 0 10px 10px;
    }
    .medication-dropdown.show { display: block; }
    .medication-option { padding: 0.65rem 0.85rem; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .medication-option:hover { background: rgba(20, 184, 166, 0.12); }
    .medication-option:last-child { border-bottom: none; }
    .medication-option strong { color: var(--primary-light); font-size: 0.9rem; }
    .medication-option small { color: var(--text-secondary); display: block; }
    .medication-controlled-badge { display: none; margin-top: 0.45rem; color: #fca5a5; font-size: 0.78rem; font-weight: 600; }
    .btn-change-medication { display: none; border: 0; background: transparent; color: var(--primary-light); font-size: 0.78rem; padding: 0.25rem 0; }

    /* Antecedentes structured blocks */
    .ant-block {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 1rem;
    }
    .ant-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }
    .ant-switch .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    .ant-switch .form-check-label {
        color: var(--text-primary);
        font-size: 0.92rem;
        cursor: pointer;
    }
    .ant-subcard {
        margin-top: 0.75rem;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        animation: fadeIn 0.25s ease-out;
    }
    .chronic-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem 1rem;
        margin-top: 0.5rem;
    }
    .chronic-grid .form-check-label {
        color: var(--text-primary);
        font-size: 0.88rem;
    }
    .chronic-grid .form-check-input {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }
    .chronic-grid .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .alert-primera-persona {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.4);
        border-radius: 8px;
        padding: 12px 16px;
        margin-top: 8px;
        font-size: 0.875rem;
        color: #fca5a5;
        animation: fadeIn 0.25s ease-out;
    }

    .diagnosis-type-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-top: 8px;
    }
    .diagnosis-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 16px 12px;
        border: 2px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        background: rgba(255,255,255,0.03);
    }
    .diagnosis-card:hover {
        border-color: rgba(13, 148, 136, 0.5);
        background: rgba(13, 148, 136, 0.08);
    }
    .diagnosis-card.selected {
        border-color: #0d9488;
        background: rgba(13, 148, 136, 0.15);
        box-shadow: 0 0 0 1px #0d9488;
    }
    .diag-icon { font-size: 1.5rem; margin-bottom: 6px; }
    .diag-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    .diag-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    @media (max-width: 768px) {
        .vitals-grid { grid-template-columns: repeat(2, 1fr); }
        .chronic-grid { grid-template-columns: 1fr; }
        .diagnosis-type-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('scripts')
<script>
(function() {
    'use strict';

    // ── Anamnesis First-Person Detection (real-time) ──
    const firstPersonPatterns = [
        /\btengo\b/i,
        /\bme duele\b/i,
        /\bme duelen\b/i,
        /\bme siento\b/i,
        /\bsiento\b/i,
        /\bsufro\b/i,
        /\bmi enfermedad\b/i,
        /\byo tengo\b/i,
        /^estoy\b/i,
    ];

    const anamnesisTextarea = document.getElementById('anamnesis');
    const anamnesisWarning = document.getElementById('anamnesis-warning');
    const anamnesisCounter = document.getElementById('anamnesis-counter');

    if (anamnesisTextarea) {
        // Fire on load if old() value is present
        function checkAnamnesis() {
            var value = anamnesisTextarea.value;
            var len = value.length;

            // Update counter
            anamnesisCounter.textContent = len + ' caracteres (mínimo 10)';
            anamnesisCounter.style.color = len >= 10 ? '#34d399' : '#f87171';

            // Check first person
            var hasFirstPerson = firstPersonPatterns.some(function(p) { return p.test(value); });
            anamnesisWarning.style.display = hasFirstPerson ? 'block' : 'none';

            // Visual feedback on textarea border
            if (hasFirstPerson) {
                anamnesisTextarea.style.borderColor = 'rgba(239, 68, 68, 0.6)';
            } else if (len >= 10) {
                anamnesisTextarea.style.borderColor = 'rgba(52, 211, 153, 0.6)';
            } else {
                anamnesisTextarea.style.borderColor = '';
            }
        }

        anamnesisTextarea.addEventListener('input', checkAnamnesis);
        // Run once on page load (for old() restoration)
        checkAnamnesis();
    }

    // ── Dynamic CIE-10 diagnosis rows ──
    var diagnosisIndex = 0;
    var diagnosesContainer = document.getElementById('diagnoses-container');
    var diagnosisTemplate = document.getElementById('diagnosis-template');
    var addDiagnosisButton = document.getElementById('add-diagnosis');
    var diagnosesClientError = document.getElementById('diagnoses-client-error');

    function addDiagnosisRow(values) {
        var index = diagnosisIndex++;
        var fragment = diagnosisTemplate.content.cloneNode(true);
        var row = fragment.querySelector('.diagnosis-row');
        row.innerHTML = row.innerHTML.replaceAll('__INDEX__', index);
        row.dataset.index = index;
        diagnosesContainer.appendChild(row);
        bindDiagnosisRow(row);

        if (values) {
            setDiagnosis(row, values.cie10_code || '', values.cie10_description || values.cie10_code || '');
            var typeInput = row.querySelector('input[name="diagnoses[' + index + '][diagnosis_type]"][value="' + values.diagnosis_type + '"]');
            if (typeInput) {
                typeInput.checked = true;
                typeInput.closest('.diagnosis-card').classList.add('selected');
            }
        }
        refreshDiagnosisRows();
    }

    function bindDiagnosisRow(row) {
        var search = row.querySelector('.diagnosis-search');
        var code = row.querySelector('.diagnosis-code');
        var results = row.querySelector('.diagnosis-results');
        var selected = row.querySelector('.diagnosis-selected');
        var selectedText = row.querySelector('.diagnosis-selected-text');
        var clearButton = row.querySelector('.diagnosis-clear');
        var debounceTimer;

        search.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            var query = this.value.trim();
            if (query.length < 2) {
                results.classList.remove('show');
                results.innerHTML = '';
                return;
            }
            debounceTimer = setTimeout(function() {
                fetch('/triage/doctor/cie10?q=' + encodeURIComponent(query))
                    .then(function(response) { return response.json(); })
                    .then(function(diagnoses) {
                        results.innerHTML = '';
                        if (!diagnoses.length) {
                            results.innerHTML = '<div class="cie10-no-results">No se encontraron resultados</div>';
                        } else {
                            diagnoses.forEach(function(diagnosis) {
                                var option = document.createElement('div');
                                option.className = 'cie10-item';
                                var itemCode = document.createElement('span');
                                itemCode.className = 'cie10-item-code';
                                itemCode.textContent = diagnosis.code;
                                var itemDescription = document.createElement('span');
                                itemDescription.className = 'cie10-item-desc';
                                itemDescription.textContent = diagnosis.description;
                                option.appendChild(itemCode);
                                option.appendChild(itemDescription);
                                option.addEventListener('click', function() {
                                    setDiagnosis(row, diagnosis.code, diagnosis.description);
                                    results.classList.remove('show');
                                });
                                results.appendChild(option);
                            });
                        }
                        results.classList.add('show');
                    });
            }, 300);
        });

        clearButton.addEventListener('click', function() {
            code.value = '';
            search.value = '';
            search.removeAttribute('readonly');
            search.style.opacity = '1';
            selected.style.display = 'none';
            search.focus();
        });

        row.querySelector('.btn-remove-diagnosis').addEventListener('click', function() {
            row.remove();
            refreshDiagnosisRows();
        });

        row.querySelectorAll('.diagnosis-card').forEach(function(card) {
            card.addEventListener('click', function() {
                row.querySelectorAll('.diagnosis-card').forEach(function(item) { item.classList.remove('selected'); });
                card.classList.add('selected');
                card.querySelector('input').checked = true;
            });
        });

        document.addEventListener('click', function(event) {
            if (!row.contains(event.target)) results.classList.remove('show');
        });
    }

    function setDiagnosis(row, diagnosisCode, description) {
        var search = row.querySelector('.diagnosis-search');
        row.querySelector('.diagnosis-code').value = diagnosisCode;
        search.value = diagnosisCode + ' — ' + description;
        search.setAttribute('readonly', true);
        search.style.opacity = '0.7';
        row.querySelector('.diagnosis-selected-text').textContent = diagnosisCode + ' — ' + description;
        row.querySelector('.diagnosis-selected').style.display = 'inline-flex';
    }

    function refreshDiagnosisRows() {
        var rows = diagnosesContainer.querySelectorAll('.diagnosis-row');
        rows.forEach(function(row, position) {
            row.querySelector('.diagnosis-row-title').textContent = position === 0 ? 'Diagnóstico Principal' : 'Diagnóstico Asociado ' + position;
            row.querySelector('.diagnosis-primary').value = position === 0 ? '1' : '0';
            row.querySelector('.btn-remove-diagnosis').style.display = position === 0 ? 'none' : 'inline-block';
        });
        diagnosesClientError.style.display = rows.length ? 'none' : 'block';
    }

    addDiagnosisButton.addEventListener('click', function() { addDiagnosisRow(); });

    @if(old('diagnoses'))
        @foreach(old('diagnoses') as $diagnosis)
            addDiagnosisRow({!! json_encode($diagnosis) !!});
        @endforeach
    @else
        addDiagnosisRow();
    @endif

    document.getElementById('consultation-form').addEventListener('submit', function(event) {
        if (!diagnosesContainer.querySelectorAll('.diagnosis-row').length) {
            event.preventDefault();
            diagnosesClientError.style.display = 'block';
        }
    });

    // ── Dynamic Prescription Rows ──
    var rxIndex = 0;
    var container = document.getElementById('prescriptions-container');
    var noRxMsg = document.getElementById('no-prescriptions');
    var addBtn = document.getElementById('add-prescription');

    addBtn.addEventListener('click', function() {
        addPrescriptionRow();
    });

    function addPrescriptionRow(values) {
        noRxMsg.style.display = 'none';
        var idx = rxIndex++;

        var row = document.createElement('div');
        row.className = 'prescription-row';
        row.id = 'rx-row-' + idx;
        row.innerHTML =
            '<div class="prescription-header">' +
                '<span class="prescription-number"><i class="bi bi-capsule me-1"></i>Medicamento #' + (idx + 1) + '</span>' +
                '<button type="button" class="btn-remove-rx" onclick="removePrescription(' + idx + ')">' +
                    '<i class="bi bi-trash me-1"></i>Eliminar' +
                '</button>' +
            '</div>' +
            '<div class="row g-2">' +
                '<div class="col-md-4 mb-2">' +
                    '<label class="form-label">Medicamento MSP *</label>' +
                    '<div class="medication-search-wrap">' +
                        '<input type="text" class="form-control med-search" id="med-search-' + idx + '" placeholder="Buscar medicamento MSP..." autocomplete="off" required>' +
                        '<div id="med-results-' + idx + '" class="medication-dropdown"></div>' +
                    '</div>' +
                    '<input type="hidden" class="med-generic" name="prescriptions[' + idx + '][generic_name]" required>' +
                    '<input type="hidden" class="med-concentration" name="prescriptions[' + idx + '][concentration]" required>' +
                    '<input type="hidden" class="med-form" name="prescriptions[' + idx + '][form]" required>' +
                    '<div class="medication-controlled-badge">⚠️ Medicamento Controlado</div>' +
                    '<button type="button" class="btn-change-medication">Cambiar medicamento</button>' +
                '</div>' +
                '<div class="col-md-3 mb-2">' +
                    '<label class="form-label">Concentración *</label>' +
                    '<input type="text" class="form-control med-concentration-display" readonly placeholder="Se completa al seleccionar">' +
                '</div>' +
                '<div class="col-md-3 mb-2">' +
                    '<label class="form-label">Forma Farm. *</label>' +
                    '<input type="text" class="form-control med-form-display" readonly placeholder="Se completa al seleccionar">' +
                '</div>' +
                '<div class="col-md-2 mb-2">' +
                    '<label class="form-label">Cantidad *</label>' +
                    '<input type="number" class="form-control" name="prescriptions[' + idx + '][quantity]" required min="1" placeholder="Ej: 10">' +
                '</div>' +
                '<div class="col-12">' +
                    '<label class="form-label">Indicaciones *</label>' +
                    '<textarea class="form-control" name="prescriptions[' + idx + '][indications]" rows="2" required placeholder="Ej: Tomar 1 tableta cada 8 horas por 5 días"></textarea>' +
                '</div>' +
            '</div>';

        container.appendChild(row);
        bindMedicationSearch(row, idx);

        if (values) {
            setMedication(row, {
                generic_name: values.generic_name || '',
                concentration: values.concentration || '',
                form: values.form || '',
                controlled: false
            });
            row.querySelector('[name="prescriptions[' + idx + '][quantity]"]').value = values.quantity || '';
            row.querySelector('[name="prescriptions[' + idx + '][indications]"]').value = values.indications || '';
        }
    }

    function bindMedicationSearch(row, idx) {
        var search = row.querySelector('.med-search');
        var results = row.querySelector('#med-results-' + idx);
        var changeButton = row.querySelector('.btn-change-medication');
        var timer;

        search.addEventListener('input', function() {
            clearTimeout(timer);
            var query = this.value.trim();
            if (query.length < 2) {
                results.classList.remove('show');
                results.innerHTML = '';
                return;
            }
            timer = setTimeout(function() {
                fetch('/triage/doctor/medications?q=' + encodeURIComponent(query))
                    .then(function(response) { return response.json(); })
                    .then(function(medications) {
                        results.innerHTML = '';
                        if (!medications.length) {
                            results.innerHTML = '<div class="cie10-no-results">No se encontraron medicamentos MSP</div>';
                        } else {
                            medications.forEach(function(medication) {
                                var option = document.createElement('div');
                                option.className = 'medication-option';
                                var name = document.createElement('strong');
                                name.textContent = medication.generic_name + ' ' + medication.concentration;
                                var details = document.createElement('small');
                                details.textContent = medication.form + (medication.route ? ' · ' + medication.route : '') + (medication.controlled ? ' · CONTROLADO' : '');
                                option.appendChild(name);
                                option.appendChild(details);
                                option.addEventListener('click', function() {
                                    setMedication(row, medication);
                                    results.classList.remove('show');
                                    results.innerHTML = '';
                                });
                                results.appendChild(option);
                            });
                        }
                        results.classList.add('show');
                    })
                    .catch(function() {
                        results.innerHTML = '<div class="cie10-no-results">Error al buscar medicamentos</div>';
                        results.classList.add('show');
                    });
            }, 250);
        });

        changeButton.addEventListener('click', function() {
            clearMedication(row);
        });

        document.addEventListener('click', function(event) {
            if (!row.contains(event.target)) results.classList.remove('show');
        });
    }

    function setMedication(row, medication) {
        row.querySelector('.med-search').value = medication.generic_name + ' ' + medication.concentration + ' — ' + medication.form;
        row.querySelector('.med-search').setAttribute('readonly', true);
        row.querySelector('.med-generic').value = medication.generic_name;
        row.querySelector('.med-concentration').value = medication.concentration;
        row.querySelector('.med-form').value = medication.form;
        row.querySelector('.med-concentration-display').value = medication.concentration;
        row.querySelector('.med-form-display').value = medication.form;
        row.querySelector('.medication-controlled-badge').style.display = medication.controlled ? 'block' : 'none';
        row.querySelector('.btn-change-medication').style.display = 'inline-block';
    }

    function clearMedication(row) {
        row.querySelector('.med-search').value = '';
        row.querySelector('.med-search').removeAttribute('readonly');
        row.querySelector('.med-generic').value = '';
        row.querySelector('.med-concentration').value = '';
        row.querySelector('.med-form').value = '';
        row.querySelector('.med-concentration-display').value = '';
        row.querySelector('.med-form-display').value = '';
        row.querySelector('.medication-controlled-badge').style.display = 'none';
        row.querySelector('.btn-change-medication').style.display = 'none';
        row.querySelector('.med-search').focus();
    }

    window.removePrescription = function(idx) {
        var row = document.getElementById('rx-row-' + idx);
        if (row) {
            row.style.animation = 'fadeOut 0.2s ease-out';
            setTimeout(function() {
                row.remove();
                if (container.children.length === 0) {
                    noRxMsg.style.display = 'block';
                }
            }, 200);
        }
    };

    // Restore old values on validation failure — add a prescription row if old data exists
    @if(old('prescriptions'))
        @foreach(old('prescriptions') as $i => $rx)
            (function() {
                var values = {!! json_encode($rx) !!};
                addPrescriptionRow(values);
            })();
        @endforeach
    @endif

    // ── Antecedentes Toggle Handlers ──
    function bindToggle(checkboxId, targetId) {
        var cb = document.getElementById(checkboxId);
        var target = document.getElementById(targetId);
        if (!cb || !target) return;
        cb.addEventListener('change', function() {
            target.style.display = this.checked ? '' : 'none';
        });
    }

    bindToggle('ant_hta', 'hta-details');
    bindToggle('ant_hta_treatment', 'hta-medication-wrapper');
    bindToggle('ant_dm', 'dm-details');
    bindToggle('ant_dm_treatment', 'dm-medication-wrapper');
    bindToggle('chronic_otra', 'chronic-other-wrapper');

})();
</script>
<style>
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to   { opacity: 0; transform: translateY(-10px); }
    }
</style>
@endsection
