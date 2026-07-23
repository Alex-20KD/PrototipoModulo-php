@extends('triage.layouts.app')

@section('title', 'Historial clínico | MedTriaje')

@section('content')
@php
    $lastAppointment = $appointments->first();
    $frequentDiagnosis = $appointments
        ->filter(fn ($appointment) => filled($appointment->cie10_code))
        ->groupBy('cie10_code')
        ->sortByDesc(fn ($visits) => $visits->count())
        ->first();
@endphp

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="section-title">Historial clínico del paciente</h1>
        <p class="section-subtitle mb-0">Consultas y registros de atención en MedTriaje.</p>
    </div>
    <a href="{{ route('triage.doctor.index') }}" class="btn btn-outline-glass align-self-sm-start">
        <i class="bi bi-arrow-left me-1"></i> Volver al panel médico
    </a>
</div>

<section class="glass-card patient-header mb-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="patient-avatar"><i class="bi bi-person"></i></div>
        <div>
            <h2 class="h4 mb-1">{{ $patient->nombres }}</h2>
            <span class="text-secondary">Paciente registrado</span>
        </div>
    </div>
    <div class="row g-3 patient-details">
        <div class="col-6 col-md-3"><span>Cédula</span><strong>{{ $patient->cedula }}</strong></div>
        <div class="col-6 col-md-3"><span>Edad</span><strong>{{ $patient->edad }} años</strong></div>
        <div class="col-6 col-md-3"><span>Sexo</span><strong>{{ $patient->sexo }}</strong></div>
        <div class="col-6 col-md-3"><span>Contacto</span><strong>{{ $patient->contacto ?: 'No registrado' }}</strong></div>
    </div>
</section>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4"><div class="glass-card summary-card"><span>Total de visitas</span><strong>{{ $appointments->count() }}</strong></div></div>
    <div class="col-12 col-md-4"><div class="glass-card summary-card"><span>Última visita</span><strong>{{ $lastAppointment ? $lastAppointment->appointment_date->format('d/m/Y') : 'Sin registros' }}</strong></div></div>
    <div class="col-12 col-md-4"><div class="glass-card summary-card"><span>Diagnóstico más frecuente</span><strong>{{ $frequentDiagnosis ? $frequentDiagnosis->first()->cie10_code : 'Sin diagnóstico' }}</strong></div></div>
</div>

<h2 class="h5 fw-bold mb-3"><i class="bi bi-clock-history me-2" style="color:var(--primary-light);"></i>Línea de tiempo de atenciones</h2>

@forelse ($appointments as $appointment)
    <article class="glass-card timeline-card mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
            <div>
                <h3 class="h5 mb-1">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</h3>
                <span class="text-secondary"><i class="bi bi-person-badge me-1"></i>{{ $appointment->doctor->nombres }}</span>
            </div>
            <div>
                @if ($appointment->status === 'completed')
                    <span class="badge-status badge-completed">Completada</span>
                @else
                    <span class="badge-status badge-pending">{{ ucfirst($appointment->status) }}</span>
                @endif
            </div>
        </div>

        @if ($appointment->vitalSigns)
            <div class="clinical-block mb-3">
                <h4><i class="bi bi-heart-pulse me-1"></i>Signos vitales</h4>
                <div class="vital-signs-grid">
                    <span><small>Presión arterial</small>{{ $appointment->vitalSigns->blood_pressure }}</span>
                    <span><small>Temperatura</small>{{ $appointment->vitalSigns->temperature }} °C</span>
                    <span><small>Peso</small>{{ $appointment->vitalSigns->weight_kg }} kg</span>
                    <span><small>Estatura</small>{{ $appointment->vitalSigns->height_cm }} cm</span>
                </div>
            </div>
        @endif

        @if ($appointment->cie10_code)
            <div class="clinical-block mb-3">
                <h4><i class="bi bi-clipboard2-pulse me-1"></i>Diagnóstico</h4>
                <p class="mb-0"><span class="badge-status badge-assigned">{{ $appointment->cie10_code }}</span> {{ $appointment->cie10_description }} <span class="text-secondary ms-1">· {{ $appointment->diagnosis_type_label }}</span></p>
            </div>
        @endif

        @if ($appointment->prescriptions->isNotEmpty())
            <div class="clinical-block mb-3">
                <h4><i class="bi bi-capsule me-1"></i>Prescripciones</h4>
                <ul class="prescription-list mb-0">
                    @foreach ($appointment->prescriptions as $prescription)
                        <li><strong>{{ $prescription->generic_name }}</strong> · {{ $prescription->concentration }} · {{ $prescription->form }} · Cantidad: {{ $prescription->quantity }}<br><span>{{ $prescription->indications }}</span></li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($appointment->anamnesis)
            <div class="clinical-block mb-0">
                <h4><i class="bi bi-chat-left-text me-1"></i>Anamnesis</h4>
                <p class="mb-0 text-secondary">{{ \Illuminate\Support\Str::limit($appointment->anamnesis, 150) }}</p>
            </div>
        @endif
    </article>
@empty
    <div class="glass-card text-center py-5">
        <i class="bi bi-clipboard-x fs-1" style="color:var(--text-secondary);"></i>
        <p class="mt-3 mb-0 text-secondary">Este paciente aún no tiene citas registradas.</p>
    </div>
@endforelse
@endsection

@section('styles')
<style>
    .patient-header { border-color: rgba(20, 184, 166, 0.25); }
    .patient-avatar { width: 52px; height: 52px; border-radius: 50%; display: grid; place-items: center; background: rgba(20, 184, 166, 0.15); color: var(--primary-light); font-size: 1.5rem; }
    .patient-details span, .summary-card span, .vital-signs-grid small { display: block; color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 0.25rem; }
    .patient-details strong { font-size: 0.95rem; overflow-wrap: anywhere; }
    .summary-card { padding: 1.25rem; height: 100%; }
    .summary-card strong { display: block; color: var(--primary-light); font-size: 1.15rem; margin-top: 0.35rem; }
    .timeline-card { border-left: 3px solid var(--primary-light); }
    .clinical-block { background: rgba(255, 255, 255, 0.025); border-radius: 10px; padding: 0.9rem 1rem; }
    .clinical-block h4 { color: var(--primary-light); font-size: 0.9rem; font-weight: 600; margin-bottom: 0.7rem; }
    .vital-signs-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 0.75rem; }
    .vital-signs-grid span { font-weight: 600; }
    .prescription-list { padding-left: 1.2rem; }
    .prescription-list li + li { margin-top: 0.55rem; }
    .prescription-list span { color: var(--text-secondary); font-size: 0.9rem; }
    .badge-completed { background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.3); }
</style>
@endsection
