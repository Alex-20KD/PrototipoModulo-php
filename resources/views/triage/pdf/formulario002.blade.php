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
        .empty-row td { height: 50px; }
        .empty-row-sm td { height: 30px; }
        .checkbox { display: inline-block; width: 10px; height: 10px; border: 1px solid #000; margin-right: 3px; vertical-align: middle; }
        .checkbox.checked { background: #000; }
        .signature-line { border-top: 1px solid #000; width: 200px; margin: 30px auto 5px; }
        .signature-label { text-align: center; font-size: 10px; color: #333; }
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
            <td colspan="4" class="section-title">2. ANTECEDENTES PERSONALES</td>
        </tr>
        <tr>
            <td class="label" style="width:25%;">Clínicos</td>
            <td style="width:25%;">{{ $appointment->antecedentes ?? '' }}</td>
            <td class="label" style="width:25%;">Quirúrgicos</td>
            <td style="width:25%;"></td>
        </tr>
        <tr>
            <td class="label">Alergias</td>
            <td></td>
            <td class="label">Medicación Habitual</td>
            <td></td>
        </tr>
        <tr class="empty-row-sm">
            <td class="label">Observaciones</td>
            <td colspan="3">{{ $appointment->antecedentes ?? '' }}</td>
        </tr>
    </table>

    {{-- 3. ANTECEDENTES FAMILIARES --}}
    <table>
        <tr>
            <td colspan="4" class="section-title">3. ANTECEDENTES FAMILIARES</td>
        </tr>
        <tr>
            <td class="label" style="width:25%;">Cardiopatías</td>
            <td style="width:25%;">
                <span class="checkbox"></span> Sí
                <span class="checkbox"></span> No
            </td>
            <td class="label" style="width:25%;">Diabetes</td>
            <td style="width:25%;">
                <span class="checkbox"></span> Sí
                <span class="checkbox"></span> No
            </td>
        </tr>
        <tr>
            <td class="label">HTA</td>
            <td>
                <span class="checkbox"></span> Sí
                <span class="checkbox"></span> No
            </td>
            <td class="label">Cáncer</td>
            <td>
                <span class="checkbox"></span> Sí
                <span class="checkbox"></span> No
            </td>
        </tr>
        <tr>
            <td class="label">Tuberculosis</td>
            <td>
                <span class="checkbox"></span> Sí
                <span class="checkbox"></span> No
            </td>
            <td class="label">Enf. Mentales</td>
            <td>
                <span class="checkbox"></span> Sí
                <span class="checkbox"></span> No
            </td>
        </tr>
        <tr class="empty-row-sm">
            <td class="label">Otros</td>
            <td colspan="3"></td>
        </tr>
    </table>

    {{-- 4. ENFERMEDAD O PROBLEMA ACTUAL --}}
    <table>
        <tr>
            <td class="section-title">4. ENFERMEDAD O PROBLEMA ACTUAL</td>
        </tr>
        <tr>
            <td class="value" style="padding:8px;">
                <span class="label">Cronología y descripción:</span><br>
                {{ $appointment->anamnesis ?? '' }}
            </td>
        </tr>
        <tr class="empty-row-sm">
            <td></td>
        </tr>
    </table>

    {{-- 5. REVISIÓN ACTUAL DE ÓRGANOS Y SISTEMAS --}}
    <table>
        <tr>
            <td colspan="6" class="section-title">5. REVISIÓN ACTUAL DE ÓRGANOS Y SISTEMAS</td>
        </tr>
        <tr>
            <td class="label" style="width:16%;">Órganos de los Sentidos</td>
            <td style="width:17%;"><span class="checkbox"></span> Sin patología</td>
            <td class="label" style="width:16%;">Respiratorio</td>
            <td style="width:17%;"><span class="checkbox"></span> Sin patología</td>
            <td class="label" style="width:16%;">Cardiovascular</td>
            <td style="width:18%;"><span class="checkbox"></span> Sin patología</td>
        </tr>
        <tr>
            <td class="label">Digestivo</td>
            <td><span class="checkbox"></span> Sin patología</td>
            <td class="label">Genito Urinario</td>
            <td><span class="checkbox"></span> Sin patología</td>
            <td class="label">Músculo Esquelético</td>
            <td><span class="checkbox"></span> Sin patología</td>
        </tr>
        <tr>
            <td class="label">Endocrino</td>
            <td><span class="checkbox"></span> Sin patología</td>
            <td class="label">Hemo Linfático</td>
            <td><span class="checkbox"></span> Sin patología</td>
            <td class="label">Nervioso</td>
            <td><span class="checkbox"></span> Sin patología</td>
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

    {{-- 7. EXAMEN FÍSICO REGIONAL --}}
    <table>
        <tr>
            <td colspan="2" class="section-title">7. EXAMEN FÍSICO REGIONAL</td>
        </tr>
        <tr>
            <td class="label" style="width:30%;">Cabeza</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="label">Cuello</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="label">Tórax</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="label">Abdomen</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="label">Pelvis</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="label">Extremidades</td>
            <td class="value"></td>
        </tr>
    </table>

    {{-- 8. DIAGNÓSTICO --}}
    @if($appointment->cie10_code)
    <table>
        <tr>
            <td colspan="3" class="section-title">8. DIAGNÓSTICO</td>
        </tr>
        <tr>
            <td class="label" style="width:20%;">CÓDIGO CIE-10</td>
            <td class="label" style="width:50%;">DESCRIPCIÓN</td>
            <td class="label" style="width:30%;">TIPO</td>
        </tr>
        <tr>
            <td class="value" style="font-weight:bold;">{{ $appointment->cie10_code }}</td>
            <td class="value">{{ $appointment->cie10_description }}</td>
            <td class="value" style="text-transform:uppercase;">{{ $appointment->diagnosis_type }}</td>
        </tr>
    </table>
    @endif

    {{-- 9. PLAN DE TRATAMIENTO / RECETA --}}
    @if($appointment->prescriptions && $appointment->prescriptions->count() > 0)
    <table>
        <tr>
            <td colspan="5" class="section-title">9. PLAN DE TRATAMIENTO / RECETA</td>
        </tr>
        <tr>
            <td class="label" style="width:25%;">MEDICAMENTO</td>
            <td class="label" style="width:15%;">CONCENTRACIÓN</td>
            <td class="label" style="width:15%;">FORMA FARM.</td>
            <td class="label" style="width:10%;">CANTIDAD</td>
            <td class="label" style="width:35%;">INDICACIONES</td>
        </tr>
        @foreach($appointment->prescriptions as $rx)
        <tr>
            <td class="value">{{ $rx->generic_name }}</td>
            <td class="value">{{ $rx->concentration }}</td>
            <td class="value">{{ $rx->form }}</td>
            <td class="value" style="text-align:center;">{{ $rx->quantity }}</td>
            <td class="value">{{ $rx->indications }}</td>
        </tr>
        @endforeach
    </table>
    @endif

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

    {{-- Firma --}}
    <div style="margin-top: 30px;">
        <table>
            <tr>
                <td class="no-border" style="width:50%; text-align:center; padding-top:40px;">
                    <div class="signature-line"></div>
                    <div class="signature-label">FIRMA DEL PROFESIONAL</div>
                </td>
                <td class="no-border" style="width:50%; text-align:center; padding-top:40px;">
                    <div class="signature-line"></div>
                    <div class="signature-label">FIRMA DEL PACIENTE / REPRESENTANTE</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        SNS-MSP / HCU-form.002 / 2008 &nbsp;&nbsp; CONSULTA EXTERNA - ANAMNESIS Y EXAMEN FISICO
    </div>
</body>
</html>
