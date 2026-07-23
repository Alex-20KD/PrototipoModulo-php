@extends('triage.layouts.app')

@section('title', 'Reportes | MedTriaje')

@section('styles')
<style>
    .report-stat { height: 100%; padding: 1.35rem; }
    .report-stat .label { color: var(--text-secondary); font-size: 0.83rem; font-weight: 600; }
    .report-stat .value { font-size: 2rem; font-weight: 700; line-height: 1.2; margin-top: 0.5rem; }
    .today-row td { background: rgba(20, 184, 166, 0.12); color: #99f6e4; }
</style>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="section-title">📊 Reportes del Módulo de Triaje</h1>
        <p class="section-subtitle mb-0">Resumen operativo y clínico del sistema.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="glass-card report-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="label">Total pacientes atendidos</span>
                    <span class="icon-circle icon-circle-teal"><i class="bi bi-people"></i></span>
                </div>
                <div class="value">{{ number_format($totalPatients) }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="glass-card report-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="label">Consultas completadas hoy</span>
                    <span class="icon-circle icon-circle-teal"><i class="bi bi-check2-circle"></i></span>
                </div>
                <div class="value">{{ number_format($completedToday) }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="glass-card report-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="label">Recetas emitidas en total</span>
                    <span class="icon-circle icon-circle-amber"><i class="bi bi-prescription2"></i></span>
                </div>
                <div class="value">{{ number_format($prescriptionsCount) }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="glass-card report-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="label">Triajes pendientes ahora</span>
                    <span class="icon-circle icon-circle-rose"><i class="bi bi-hourglass-split"></i></span>
                </div>
                <div class="value">{{ number_format($pendingTriages) }}</div>
            </div>
        </div>
    </div>

    <section class="glass-card mb-4">
        <h2 class="h5 fw-bold mb-1">Pacientes atendidos por día (últimos 30 días)</h2>
        <p class="section-subtitle">Citas registradas por fecha.</p>
        <div class="table-responsive">
            <table class="table table-glass mb-0">
                <thead><tr><th>Fecha</th><th class="text-end">Cantidad</th></tr></thead>
                <tbody>
                    @forelse ($dailyPatients as $dailyPatient)
                        <tr class="{{ $dailyPatient->date === $today->toDateString() ? 'today-row' : '' }}">
                            <td>{{ \Carbon\Carbon::parse($dailyPatient->date)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</td>
                            <td class="text-end fw-semibold">{{ number_format($dailyPatient->count) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-secondary py-4">No hay citas registradas en los últimos 30 días.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="glass-card">
        <h2 class="h5 fw-bold mb-1">Diagnósticos CIE-10 más frecuentes</h2>
        <p class="section-subtitle">Los 10 diagnósticos registrados con mayor frecuencia.</p>
        <div class="table-responsive">
            <table class="table table-glass mb-0">
                <thead><tr><th>#</th><th>Código</th><th>Descripción</th><th class="text-end">Cantidad</th></tr></thead>
                <tbody>
                    @forelse ($topDiagnoses as $diagnosis)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge-status badge-assigned">{{ $diagnosis->cie10_code }}</span></td>
                            <td>{{ $diagnosis->cie10_description }}</td>
                            <td class="text-end fw-semibold">{{ number_format($diagnosis->count) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">Aún no hay diagnósticos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
