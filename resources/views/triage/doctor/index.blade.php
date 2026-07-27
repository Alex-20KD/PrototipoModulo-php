@extends('triage.layouts.app')

@section('title', 'Doctor - Citas del Día')

@section('content')
<div class="mb-4">
    <h1 class="section-title">🩺 Citas del día — {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h1>
    <p class="section-subtitle">Listado de citas programadas para la fecha seleccionada.</p>
</div>

@if(session('success'))
    <div class="alert-success-custom mb-4">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

<form method="GET" action="{{ route('triage.doctor.index') }}" class="glass-card date-filter-bar mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
        <label for="fecha" class="mb-0 fw-semibold">Filtrar por fecha</label>
        <input id="fecha" type="date" name="fecha" value="{{ $fecha }}" max="{{ date('Y-m-d') }}" class="form-control date-filter-input">
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom"><i class="bi bi-funnel me-1"></i> Filtrar</button>
            <a href="{{ route('triage.doctor.index') }}" class="btn btn-outline-glass">Hoy</a>
        </div>
    </div>
</form>

@if($appointments->count() === 0)
    <div class="glass-card text-center py-5">
        <i class="bi bi-calendar-x fs-1" style="color:var(--text-secondary);"></i>
        <p class="mt-3" style="color:var(--text-secondary);">No hay citas programadas para la fecha seleccionada.</p>
    </div>
@else
    <div class="glass-card" style="padding:0; overflow:hidden;">
        <table class="table table-glass mb-0">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Doctor</th>
                    <th>Triaje</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appt)
                <tr>
                    <td><strong>{{ $appt->appointment_date->format('H:i') }}</strong></td>
                    <td>{{ $appt->user->nombres }}</td>
                    <td>{{ $appt->doctor->nombres }}</td>
                    <td>
                        @if($appt->vitalSigns)
                            <span class="badge-status badge-assigned">Vinculado</span>
                        @else
                            <span class="badge-status badge-pending">Sin triaje</span>
                        @endif
                    </td>
                    <td>
                        @if($appt->status === 'completed')
                            <span class="badge-status badge-completed">Completada</span>
                        @else
                            <span class="badge-status badge-assigned">{{ $appt->status }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('triage.patient.history', $appt->user_id) }}" class="btn btn-outline-glass btn-sm mb-1">
                            <i class="bi bi-clock-history me-1"></i> Ver Historial
                        </a>
                        @if($appt->status === 'completed')
                            <a href="{{ route('triage.doctor.pdf', $appt->id) }}" class="btn btn-outline-glass btn-sm">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Ver PDF
                            </a>
                        @else
                            <a href="{{ route('triage.doctor.attend', $appt->id) }}" class="btn btn-accent btn-sm">
                                <i class="bi bi-clipboard2-pulse me-1"></i> Atender Paciente
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($appointments->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav>
                {{ $appointments->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    @endif
@endif
@endsection

@section('styles')
<style>
    .date-filter-bar { padding: 1rem 1.25rem; }
    .date-filter-input { max-width: 220px; }
    .pagination .page-link {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: var(--text-secondary);
        border-radius: 8px;
        margin: 0 2px;
        transition: all 0.3s ease;
    }
    .pagination .page-link:hover {
        background: rgba(20, 184, 166, 0.15);
        border-color: var(--primary-light);
        color: var(--primary-light);
    }
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border-color: transparent;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        background: rgba(255, 255, 255, 0.02);
        color: rgba(148, 163, 184, 0.4);
        border-color: rgba(255, 255, 255, 0.05);
    }
    .badge-completed {
        background: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
</style>
@endsection
