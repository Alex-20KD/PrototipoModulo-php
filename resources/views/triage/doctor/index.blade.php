@extends('triage.layouts.app')

@section('title', 'Doctor - Citas del Día')

@section('content')
<div class="mb-4">
    <h1 class="section-title"><i class="bi bi-clipboard2-pulse"></i> Doctor — Citas del Día</h1>
    <p class="section-subtitle">Listado de citas programadas para hoy, {{ now()->format('d/m/Y') }}</p>
</div>

@if(session('success'))
    <div class="alert-success-custom mb-4">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

@if($appointments->count() === 0)
    <div class="glass-card text-center py-5">
        <i class="bi bi-calendar-x fs-1" style="color:var(--text-secondary);"></i>
        <p class="mt-3" style="color:var(--text-secondary);">No hay citas programadas para hoy.</p>
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
                        @if($appt->status === 'completed')
                            <a href="{{ route('triage.doctor.pdf', $appt->id) }}" class="btn btn-outline-glass btn-sm">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Ver PDF
                            </a>
                        @else
                            <a href="{{ route('triage.doctor.attend', $appt->id) }}" class="btn btn-accent btn-sm">
                                <i class="bi bi-clipboard2-pulse me-1"></i> Atender Paciente
                            </a>
                            <a href="{{ route('triage.doctor.pdf', $appt->id) }}" class="btn btn-outline-glass btn-sm ms-1">
                                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
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
