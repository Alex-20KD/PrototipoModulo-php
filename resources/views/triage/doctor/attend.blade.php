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
        <h5 class="card-section-title"><i class="bi bi-journal-medical me-2"></i>Anamnesis y Antecedentes</h5>

        <div class="mb-3">
            <label for="anamnesis" class="form-label">Anamnesis (Enfermedad Actual) <span class="text-danger">*</span></label>
            <textarea class="form-control" id="anamnesis" name="anamnesis" rows="4"
                      placeholder="Describa la enfermedad actual, cronología, síntomas..."
                      required>{{ old('anamnesis') }}</textarea>
        </div>

        <div class="mb-0">
            <label for="antecedentes" class="form-label">Antecedentes Personales</label>
            <textarea class="form-control" id="antecedentes" name="antecedentes" rows="3"
                      placeholder="Antecedentes clínicos, quirúrgicos, alergias, medicación habitual...">{{ old('antecedentes') }}</textarea>
        </div>
    </div>

    <div class="glass-card mb-4">
        <h5 class="card-section-title"><i class="bi bi-search me-2"></i>Diagnóstico CIE-10</h5>

        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="cie10-search" class="form-label">Buscar Diagnóstico CIE-10 <span class="text-danger">*</span></label>
                <div style="position: relative;">
                    <input type="text" class="form-control" id="cie10-search"
                           placeholder="Escriba al menos 2 caracteres... (ej: diabetes, J00, gastritis)"
                           autocomplete="off">
                    <input type="hidden" name="cie10_code" id="cie10-code" value="{{ old('cie10_code') }}" required>
                    <div id="cie10-results" class="cie10-dropdown"></div>
                </div>
                <div id="cie10-selected" class="cie10-selected-badge" style="display: none;">
                    <span id="cie10-selected-text"></span>
                    <button type="button" id="cie10-clear" class="cie10-clear-btn" title="Cambiar diagnóstico">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label for="diagnosis_type" class="form-label">Tipo de Diagnóstico <span class="text-danger">*</span></label>
                <select class="form-select" id="diagnosis_type" name="diagnosis_type" required>
                    <option value="" disabled {{ old('diagnosis_type') ? '' : 'selected' }}>Seleccionar...</option>
                    <option value="presuntivo" {{ old('diagnosis_type') == 'presuntivo' ? 'selected' : '' }}>Presuntivo</option>
                    <option value="definitivo" {{ old('diagnosis_type') == 'definitivo' ? 'selected' : '' }}>Definitivo</option>
                </select>
            </div>
        </div>
    </div>

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

    @media (max-width: 768px) {
        .vitals-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('scripts')
<script>
(function() {
    'use strict';

    // ── CIE-10 Search with debounce ──
    const searchInput = document.getElementById('cie10-search');
    const codeInput = document.getElementById('cie10-code');
    const resultsDiv = document.getElementById('cie10-results');
    const selectedDiv = document.getElementById('cie10-selected');
    const selectedText = document.getElementById('cie10-selected-text');
    const clearBtn = document.getElementById('cie10-clear');
    let debounceTimer = null;

    searchInput.addEventListener('keyup', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();

        if (q.length < 2) {
            resultsDiv.classList.remove('show');
            resultsDiv.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(function() {
            fetch('/triage/doctor/cie10?q=' + encodeURIComponent(q))
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    resultsDiv.innerHTML = '';
                    if (data.length === 0) {
                        resultsDiv.innerHTML = '<div class="cie10-no-results"><i class="bi bi-search me-1"></i>No se encontraron resultados</div>';
                    } else {
                        data.forEach(function(item) {
                            var div = document.createElement('div');
                            div.className = 'cie10-item';
                            div.innerHTML = '<span class="cie10-item-code">' + item.code + '</span>' +
                                            '<span class="cie10-item-desc">' + item.description + '</span>';
                            div.addEventListener('click', function() {
                                selectCie10(item.code, item.description);
                            });
                            resultsDiv.appendChild(div);
                        });
                    }
                    resultsDiv.classList.add('show');
                })
                .catch(function() {
                    resultsDiv.innerHTML = '<div class="cie10-no-results">Error al buscar</div>';
                    resultsDiv.classList.add('show');
                });
        }, 300);
    });

    function selectCie10(code, description) {
        codeInput.value = code;
        searchInput.value = code + ' — ' + description;
        searchInput.setAttribute('readonly', true);
        searchInput.style.opacity = '0.7';
        resultsDiv.classList.remove('show');
        resultsDiv.innerHTML = '';
        selectedText.textContent = code + ' — ' + description;
        selectedDiv.style.display = 'inline-flex';
    }

    clearBtn.addEventListener('click', function() {
        codeInput.value = '';
        searchInput.value = '';
        searchInput.removeAttribute('readonly');
        searchInput.style.opacity = '1';
        selectedDiv.style.display = 'none';
        searchInput.focus();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.remove('show');
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

    function addPrescriptionRow() {
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
                    '<label class="form-label">Nombre Genérico *</label>' +
                    '<input type="text" class="form-control" name="prescriptions[' + idx + '][generic_name]" required placeholder="Ej: Paracetamol">' +
                '</div>' +
                '<div class="col-md-3 mb-2">' +
                    '<label class="form-label">Concentración *</label>' +
                    '<input type="text" class="form-control" name="prescriptions[' + idx + '][concentration]" required placeholder="Ej: 500mg">' +
                '</div>' +
                '<div class="col-md-3 mb-2">' +
                    '<label class="form-label">Forma Farm. *</label>' +
                    '<input type="text" class="form-control" name="prescriptions[' + idx + '][form]" required placeholder="Ej: Tableta">' +
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
                addPrescriptionRow();
                var lastRow = container.lastElementChild;
                var inputs = lastRow.querySelectorAll('input, textarea');
                var values = {!! json_encode($rx) !!};
                inputs[0].value = values.generic_name || '';
                inputs[1].value = values.concentration || '';
                inputs[2].value = values.form || '';
                inputs[3].value = values.quantity || '';
                inputs[4].value = values.indications || '';
            })();
        @endforeach
    @endif

    // Restore CIE-10 selection on validation failure
    @if(old('cie10_code'))
        (function() {
            searchInput.value = '{{ old('cie10_code') }}';
            searchInput.setAttribute('readonly', true);
            searchInput.style.opacity = '0.7';
            codeInput.value = '{{ old('cie10_code') }}';
            selectedText.textContent = '{{ old('cie10_code') }}';
            selectedDiv.style.display = 'inline-flex';
        })();
    @endif

})();
</script>
<style>
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to   { opacity: 0; transform: translateY(-10px); }
    }
</style>
@endsection
