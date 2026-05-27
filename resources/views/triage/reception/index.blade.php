@extends('triage.layouts.app')

@section('title', 'Recepción - Agendar Cita')

@section('content')
<div class="mb-4">
    <h1 class="section-title"><i class="bi bi-calendar-check"></i> Recepción — Agendamiento</h1>
    <p class="section-subtitle">Busque al paciente, seleccione doctor y horario</p>
</div>

@if(session('success'))
    <div class="alert-success-custom mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5"></i>
        {{ session('success') }}
    </div>
@endif

<div class="glass-card mb-4">
    <h5 class="mb-3" style="font-weight:600;"><i class="bi bi-search me-2"></i>Buscar Paciente</h5>
    <form method="GET" action="{{ route('triage.reception.index') }}" class="d-flex gap-3">
        <input type="text" name="cedula" class="form-control" placeholder="Ingrese número de cédula..." value="{{ request('cedula') }}" style="max-width: 350px;">
        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-search me-1"></i> Buscar</button>
    </form>
</div>

@if(request('cedula') && !$patient)
    <div class="alert-danger-custom d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        No se encontró paciente con cédula <strong>{{ request('cedula') }}</strong>
    </div>
@endif

@if($patient)
    <div class="patient-card d-flex align-items-center gap-3">
        <div class="icon-circle icon-circle-teal"><i class="bi bi-person-fill"></i></div>
        <div>
            <div class="patient-name">{{ $patient->nombres }}</div>
            <div class="patient-detail">Cédula: {{ $patient->cedula }} | Edad: {{ $patient->edad }} | Sexo: {{ $patient->sexo }}</div>
        </div>
    </div>

    @if($pendingTriage)
        <div class="alert-warning-custom mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-diamond-fill fs-4"></i>
            <div>
                <strong>¡ALERTA: Este paciente tiene un TRIAJE PENDIENTE!</strong><br>
                <span style="font-size:0.9rem;">Se vinculará automáticamente al confirmar la cita.</span>
            </div>
        </div>
    @endif

    <div class="glass-card">
        <h5 class="mb-3" style="font-weight:600;"><i class="bi bi-calendar-plus me-2"></i>Agendar Cita</h5>
        <form method="POST" action="{{ route('triage.reception.store') }}" id="appointmentForm">
            @csrf
            <input type="hidden" name="user_id" value="{{ $patient->id }}">
            <input type="hidden" name="appointment_time" id="appointmentTime" value="">
            <div class="mb-3">
                <label class="form-label">Seleccionar Doctor</label>
                <select name="doctor_id" class="form-select" required>
                    <option value="" disabled selected>-- Seleccione un doctor --</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->nombres }} — {{ $doctor->especialidad }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Horario Disponible (hoy)</label>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach(['09:00','09:30','10:00','10:30'] as $slot)
                        <button type="button" class="time-slot-btn" data-time="{{ $slot }}" onclick="selectTime(this)">
                            <i class="bi bi-clock me-1"></i> {{ $slot }}
                        </button>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn btn-accent" id="submitBtn" disabled>
                <i class="bi bi-check-circle me-1"></i> Confirmar Cita y Vincular Triaje
            </button>
        </form>
    </div>
@endif

@if($errors->any())
    <div class="alert-danger-custom mt-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif
@endsection

@section('scripts')
<script>
function selectTime(btn) {
    document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('appointmentTime').value = btn.dataset.time;
    document.getElementById('submitBtn').disabled = false;
}
</script>
@endsection
