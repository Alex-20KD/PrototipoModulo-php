<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MSP - Formulario 002</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; padding: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        td, th { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        .header-title { text-align: center; font-weight: bold; font-size: 13px; background: #e0e0e0; padding: 6px; }
        .section-title { font-weight: bold; font-size: 11px; background: #f0f0f0; padding: 4px 6px; }
        .no-border { border: none; }
        .label { font-weight: bold; font-size: 10px; color: #333; }
        .value { font-size: 11px; min-height: 18px; }
        .footer { text-align: center; font-size: 9px; margin-top: 15px; color: #555; border-top: 1px solid #999; padding-top: 6px; }
        .empty-row td { height: 60px; }
    </style>
</head>
<body>
    @php
        $user = $appointment->user;
        $doctor = $appointment->doctor;
        $vs = $appointment->vitalSigns;
    @endphp

    {{-- HEADER --}}
    <table>
        <tr>
            <td colspan="6" class="header-title">
                MINISTERIO DE SALUD PÚBLICA — FORMULARIO 002<br>
                CONSULTA EXTERNA — ANAMNESIS Y EXAMEN FÍSICO
            </td>
        </tr>
        <tr>
            <td class="label" style="width:20%;">ESTABLECIMIENTO</td>
            <td class="label" style="width:20%;">NOMBRE</td>
            <td class="label" style="width:15%;">APELLIDO</td>
            <td class="label" style="width:10%;">SEXO</td>
            <td class="label" style="width:10%;">EDAD</td>
            <td class="label" style="width:25%;">N° HISTORIA CLÍNICA</td>
        </tr>
        <tr>
            <td class="value">Centro de Salud</td>
            <td class="value">{{ explode(' ', $user->nombres)[0] ?? '' }}</td>
            <td class="value">{{ implode(' ', array_slice(explode(' ', $user->nombres), 1)) }}</td>
            <td class="value">{{ $user->sexo }}</td>
            <td class="value">{{ $user->edad }}</td>
            <td class="value">HC-{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</td>
        </tr>
    </table>

    {{-- 1. MOTIVO DE CONSULTA --}}
    <table>
        <tr>
            <td class="section-title">1. MOTIVO DE CONSULTA</td>
        </tr>
        <tr>
            <td class="value" style="min-height:40px; padding:8px;">
                {{ $vs ? $vs->reason_for_consultation : '' }}
            </td>
        </tr>
    </table>

    {{-- 2. ANTECEDENTES PERSONALES --}}
    <table>
        <tr>
            <td class="section-title">2. ANTECEDENTES PERSONALES</td>
        </tr>
        <tr class="empty-row">
            <td></td>
        </tr>
    </table>

    {{-- 4. ENFERMEDAD O PROBLEMA ACTUAL --}}
    <table>
        <tr>
            <td class="section-title">4. ENFERMEDAD O PROBLEMA ACTUAL</td>
        </tr>
        <tr class="empty-row">
            <td></td>
        </tr>
    </table>

    {{-- 6. SIGNOS VITALES Y ANTROPOMETRIA --}}
    <table>
        <tr>
            <td colspan="7" class="section-title">6. SIGNOS VITALES Y ANTROPOMETRÍA</td>
        </tr>
        <tr>
            <td class="label">FECHA DE MEDICIÓN</td>
            <td class="label">TEMPERATURA °C</td>
            <td class="label">PRESIÓN ARTERIAL</td>
            <td class="label">PULSO/min</td>
            <td class="label">FREC. RESPIRATORIA</td>
            <td class="label">PESO/Kg</td>
            <td class="label">TALLA/cm</td>
        </tr>
        <tr>
            <td class="value">{{ $vs ? $vs->created_at->format('d/m/Y H:i') : '' }}</td>
            <td class="value">{{ $vs ? $vs->temperature : '' }}</td>
            <td class="value">{{ $vs ? $vs->blood_pressure : '' }}</td>
            <td class="value">{{ $vs ? $vs->heart_rate : '' }}</td>
            <td class="value"></td>
            <td class="value">{{ $vs ? $vs->weight_kg : '' }}</td>
            <td class="value">{{ $vs ? $vs->height_cm : '' }}</td>
        </tr>
    </table>

    {{-- Doctor info --}}
    <table>
        <tr>
            <td class="label" style="width:30%;">MÉDICO RESPONSABLE</td>
            <td class="value">{{ $doctor->nombres }} — {{ $doctor->especialidad }}</td>
        </tr>
        <tr>
            <td class="label">FECHA DE CITA</td>
            <td class="value">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <div class="footer">
        SNS-MSP / HCU-form.002 / 2008 &nbsp;&nbsp; CONSULTA EXTERNA - ANAMNESIS Y EXAMEN FISICO
    </div>
</body>
</html>
