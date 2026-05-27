@extends('triage.layouts.app')

@section('title', 'Doctor - Citas del Día')

@section('content')
<div class="mb-4">
    <h1 class="section-title"><i class="bi bi-clipboard2-pulse"></i> Doctor — Citas del Día</h1>
    <p class="section-subtitle">Listado de citas programadas para hoy, {{ now()->format('d/m/Y') }}</p>
</div>

@if($appointments->isEmpty())
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
                    <td><span class="badge-status badge-assigned">{{ $appt->status }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('triage.doctor.pdf', $appt->id) }}" class="btn btn-outline-glass btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Ver Hoja Clínica (PDF)
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
