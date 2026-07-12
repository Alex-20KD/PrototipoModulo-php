@extends('triage.layouts.app')

@section('title', 'Enfermería - Triaje')

@section('content')
<div class="mb-4">
    <h1 class="section-title"><i class="bi bi-heart-pulse"></i> Enfermería — Triaje</h1>
    <p class="section-subtitle">Busque al paciente por cédula y registre los signos vitales</p>
</div>

@if(session('success'))
    <div class="alert-success-custom mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5"></i>
        {{ session('success') }}
    </div>
@endif

{{-- Search by cedula --}}
<div class="glass-card mb-4">
    <h5 class="mb-3" style="font-weight:600;"><i class="bi bi-search me-2"></i>Buscar Paciente</h5>
    <form method="GET" action="{{ route('triage.nursing.index') }}" class="d-flex gap-3">
        <input type="text" name="cedula" class="form-control" placeholder="Ingrese número de cédula..."
               value="{{ request('cedula') }}" style="max-width: 350px;">
        <button type="submit" class="btn btn-primary-custom">
            <i class="bi bi-search me-1"></i> Buscar
        </button>
    </form>
</div>

@if(request('cedula') && !$patient)
    <div class="alert-danger-custom d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        No se encontró ningún paciente con la cédula <strong>{{ request('cedula') }}</strong>
    </div>
@endif

@if($patient)
    {{-- Patient info card --}}
    <div class="patient-card d-flex align-items-center gap-3">
        <div class="icon-circle icon-circle-teal">
            <i class="bi bi-person-fill"></i>
        </div>
        <div>
            <div class="patient-name">{{ $patient->nombres }}</div>
            <div class="patient-detail">
                Cédula: {{ $patient->cedula }} &nbsp;|&nbsp;
                Edad: {{ $patient->edad }} años &nbsp;|&nbsp;
                Sexo: {{ $patient->sexo }} &nbsp;|&nbsp;
                Contacto: {{ $patient->contacto ?? 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Vital signs form --}}
    <div class="glass-card">
        <h5 class="mb-3" style="font-weight:600;"><i class="bi bi-activity me-2"></i>Registro de Signos Vitales</h5>
        <form method="POST" action="{{ route('triage.nursing.store') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $patient->id }}">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Presión Arterial</label>
                    <input type="text" id="bp-input" name="blood_pressure" class="form-control" placeholder="Ej: 120/80" required
                           data-format="bp" value="{{ old('blood_pressure') }}">
                    <small class="range-hint" id="bp-hint">Formato: 120/80 | Sistólica: 60–250 | Diastólica: 40–150</small>
                    <span class="vital-feedback" id="bp-input-feedback"></span>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frecuencia Cardíaca (ppm)</label>
                    <input type="number" id="hr-input" name="heart_rate" class="form-control" placeholder="Ej: 72" required
                           data-min="30" data-max="250" data-normal-min="60" data-normal-max="100" value="{{ old('heart_rate') }}">
                    <small class="range-hint" id="hr-hint">Rango válido: 30–250 | Normal: 60–100</small>
                    <span class="vital-feedback" id="hr-input-feedback"></span>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frecuencia Respiratoria (rpm)</label>
                    <input type="number" id="rr-input" name="respiratory_rate" class="form-control" placeholder="Ej: 16" required
                           data-min="8" data-max="40" data-normal-min="12" data-normal-max="20" value="{{ old('respiratory_rate') }}">
                    <small class="range-hint" id="rr-hint">Rango válido: 8–40 | Normal: 12–20</small>
                    <span class="vital-feedback" id="rr-input-feedback"></span>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Peso (Kg)</label>
                    <input type="number" step="0.01" id="w-input" name="weight_kg" class="form-control" placeholder="Ej: 65.50" required
                           data-min="1" data-max="300" value="{{ old('weight_kg') }}">
                    <small class="range-hint" id="w-hint">Rango válido: 1–300</small>
                    <span class="vital-feedback" id="w-input-feedback"></span>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Talla (cm)</label>
                    <input type="number" step="0.01" id="h-input" name="height_cm" class="form-control" placeholder="Ej: 160.00" required
                           data-min="30" data-max="250" value="{{ old('height_cm') }}">
                    <small class="range-hint" id="h-hint">Rango válido: 30–250</small>
                    <span class="vital-feedback" id="h-input-feedback"></span>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Temperatura (°C)</label>
                    <input type="number" step="0.1" id="t-input" name="temperature" class="form-control" placeholder="Ej: 36.5" required
                           data-min="34" data-max="42" data-normal-min="36.1" data-normal-max="37.2" value="{{ old('temperature') }}">
                    <small class="range-hint" id="t-hint">Rango válido: 34–42 | Normal: 36.1–37.2</small>
                    <span class="vital-feedback" id="t-input-feedback"></span>
                </div>
                <div class="col-12">
                    <label class="form-label">Motivo de Consulta</label>
                    <textarea name="reason_for_consultation" class="form-control" rows="3"
                              placeholder="Describa el motivo de la consulta..." required>{{ old('reason_for_consultation') }}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-floppy me-1"></i> Guardar Triaje (Pendiente)
                </button>
            </div>
        </form>
    </div>
@endif

@if($errors->any())
    <div class="alert-danger-custom mt-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection

@section('styles')
<style>
.range-hint {
    display: block;
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 4px;
}
.input-ok    { border-color: rgba(52, 211, 153, 0.6) !important; }
.input-warn  { border-color: rgba(251, 191, 36, 0.6) !important; }
.input-error { border-color: rgba(239, 68, 68, 0.6)  !important; }

.vital-feedback {
    display: block;
    font-size: 0.75rem;
    margin-top: 4px;
    min-height: 18px;
    transition: all 0.2s;
}
.vital-feedback.ok    { color: #34d399; }
.vital-feedback.warn  { color: #fbbf24; }
.vital-feedback.error { color: #f87171; }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function validateVitalInput(input) {
        const min = parseFloat(input.dataset.min);
        const max = parseFloat(input.dataset.max);
        const normalMin = parseFloat(input.dataset.normalMin);
        const normalMax = parseFloat(input.dataset.normalMax);
        const val = parseFloat(input.value);
        const feedbackEl = document.getElementById(input.id + '-feedback');

        input.classList.remove('input-ok', 'input-warn', 'input-error');
        if (!input.value || isNaN(val)) {
            feedbackEl.textContent = '';
            feedbackEl.className = 'vital-feedback';
            return;
        }
        if (val < min || val > max) {
            input.classList.add('input-error');
            feedbackEl.textContent = '⚠️ Valor fuera del rango fisiológico válido (' + min + '–' + max + ')';
            feedbackEl.className = 'vital-feedback error';
        } else if (!isNaN(normalMin) && !isNaN(normalMax) &&
                   (val < normalMin || val > normalMax)) {
            input.classList.add('input-warn');
            feedbackEl.textContent = '⚡ Valor fuera del rango normal pero fisiológicamente posible';
            feedbackEl.className = 'vital-feedback warn';
        } else {
            input.classList.add('input-ok');
            feedbackEl.textContent = '✓ Valor dentro del rango normal';
            feedbackEl.className = 'vital-feedback ok';
        }
    }

    // Blood pressure special validation
    function validateBP(input) {
        const val = input.value;
        const feedbackEl = document.getElementById(input.id + '-feedback');
        input.classList.remove('input-ok', 'input-warn', 'input-error');

        const bpRegex = /^\d{2,3}\/\d{2,3}$/;
        if (!val) { feedbackEl.textContent = ''; return; }

        if (!bpRegex.test(val)) {
            input.classList.add('input-error');
            feedbackEl.textContent = '⚠️ Formato inválido. Use: 120/80';
            feedbackEl.className = 'vital-feedback error';
            return;
        }
        const parts = val.split('/');
        const sys = parseInt(parts[0]);
        const dia = parseInt(parts[1]);

        if (sys < 60 || sys > 250 || dia < 40 || dia > 150) {
            input.classList.add('input-error');
            feedbackEl.textContent = '⚠️ Valores fuera de rango fisiológico';
            feedbackEl.className = 'vital-feedback error';
        } else if (sys <= dia) {
            input.classList.add('input-error');
            feedbackEl.textContent = '⚠️ La sistólica debe ser mayor que la diastólica';
            feedbackEl.className = 'vital-feedback error';
        } else if (sys > 140 || dia > 90) {
            input.classList.add('input-warn');
            feedbackEl.textContent = '⚡ Valores compatibles con hipertensión';
            feedbackEl.className = 'vital-feedback warn';
        } else {
            input.classList.add('input-ok');
            feedbackEl.textContent = '✓ Presión arterial dentro del rango normal';
            feedbackEl.className = 'vital-feedback ok';
        }
    }

    // Attach listeners
    document.querySelectorAll('[data-min]').forEach(input => {
        input.addEventListener('input', () => validateVitalInput(input));
        if(input.value) validateVitalInput(input);
    });
    
    const bpInput = document.querySelector('[data-format="bp"]');
    if (bpInput) {
        bpInput.addEventListener('input', () => validateBP(bpInput));
        if(bpInput.value) validateBP(bpInput);
    }
});
</script>
@endsection
