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
                <div class="col-md-6">
                    <label class="form-label">Presión Arterial</label>
                    <input type="text" name="blood_pressure" class="form-control" placeholder="Ej: 120/80" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Frecuencia Cardíaca (ppm)</label>
                    <input type="number" name="heart_rate" class="form-control" placeholder="Ej: 72" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Peso (Kg)</label>
                    <input type="number" step="0.01" name="weight_kg" class="form-control" placeholder="Ej: 65.50" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Talla (cm)</label>
                    <input type="number" step="0.01" name="height_cm" class="form-control" placeholder="Ej: 160.00" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Temperatura (°C)</label>
                    <input type="number" step="0.1" name="temperature" class="form-control" placeholder="Ej: 36.5" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Motivo de Consulta</label>
                    <textarea name="reason_for_consultation" class="form-control" rows="3"
                              placeholder="Describa el motivo de la consulta..." required></textarea>
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
