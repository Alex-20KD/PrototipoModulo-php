# 🏥 MedTriaje

<p align="center">
  <strong>Módulo de triaje, agendamiento y atención médica</strong><br>
  Desarrollado en PHP con Laravel para su próxima integración en un sistema clínico institucional.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Estado-En%20desarrollo-14b8a6?style=for-the-badge" alt="Estado: En desarrollo">
  <img src="https://img.shields.io/badge/PHP-Laravel-ff2d20?style=for-the-badge&logo=laravel&logoColor=white" alt="PHP Laravel">
  <img src="https://img.shields.io/badge/Interfaz-Bootstrap%205-7952b3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5">
</p>

---

## 📌 Descripción del proyecto

**MedTriaje** es un módulo independiente desarrollado como parte de las prácticas laborales. Su propósito es apoyar el flujo de atención médica desde la toma de signos vitales hasta la consulta, la receta y el seguimiento clínico del paciente.

El módulo está construido para integrarse próximamente a un sistema clínico existente. Por ello, mantiene una arquitectura aislada, con rutas, controladores, modelos y tablas propias bajo el prefijo `triage_`, evitando interferir con las funciones del sistema anfitrión.

> La autenticación y la gestión de usuarios serán administradas por el sistema principal al momento de la integración.

## 🎯 Objetivo

Digitalizar y organizar el proceso básico de atención ambulatoria, facilitando el registro clínico, el agendamiento de citas y la consulta médica con información estructurada.

## 🔄 Flujo del módulo

```text
Enfermería ──► Triaje y signos vitales
                     │
                     ▼
Recepción ───► Agendamiento de cita y vinculación del triaje
                     │
                     ▼
Médico ──────► Consulta, diagnósticos, receta e historial clínico
```

## ✨ Funcionalidades implementadas

### 🩺 Triaje y agendamiento

- Registro de signos vitales con validaciones de rangos fisiológicos.
- Gestión de triajes pendientes y vinculación automática a la cita.
- Agenda médica con filtro de citas por fecha.
- Panel diferenciado para Enfermería, Recepción y Médico.

### 📋 Atención médica y Formulario 002

- Registro de anamnesis con validación de redacción clínica.
- Antecedentes personales estructurados: HTA, diabetes y enfermedades crónicas.
- Examen físico regional por sistemas.
- Generación del **Formulario 002 del MSP** en PDF.
- Receta médica con medicamentos por denominación genérica.

### 🔎 Diagnóstico y tratamiento

- Buscador AJAX de códigos CIE-10.
- Registro de diagnósticos principal y asociados por cita.
- Tipos de diagnóstico: presuntivo/definitivo, de ingreso/alta.
- Catálogo de medicamentos basado en el Cuadro Nacional de Medicamentos Básicos del MSP.
- Identificación visual de medicamentos controlados.

### 📊 Seguimiento y reportes

- Historial clínico por paciente con línea de tiempo de atenciones.
- Consulta de signos vitales, diagnósticos, recetas y anamnesis anteriores.
- Reportes de pacientes atendidos, consultas completadas, recetas emitidas y triajes pendientes.
- Diagnósticos CIE-10 más frecuentes.

## 🧱 Arquitectura del módulo

```text
app/Modules/Triage/
├── Controllers/     # Enfermería, Recepción, Médico y Reportes
├── Models/          # Entidades clínicas del módulo
└── routes/          # Rutas bajo /triage/*

resources/views/triage/
├── nursing/         # Pantalla de triaje
├── reception/       # Agendamiento de citas
├── doctor/          # Panel y consulta médica
├── patients/        # Historial clínico
├── reports/         # Reportes operativos
└── pdf/             # Formulario 002
```

## 🛠️ Tecnologías utilizadas

| Tecnología | Uso en el proyecto |
|---|---|
| PHP + Laravel | Lógica del módulo, rutas, controladores y modelos |
| SQLite | Base de datos del prototipo |
| MySQL | Base de datos prevista para producción e integración |
| Bootstrap 5 | Interfaz responsiva con estilo glassmorphism dark |
| DomPDF | Generación de documentos clínicos en PDF |
| JavaScript + AJAX | Búsqueda dinámica de CIE-10 y medicamentos |

## 🚀 Ejecución local

```bash
composer install
php artisan migrate
php artisan db:seed
php artisan serve
```

Luego, el módulo estará disponible desde:

```text
http://localhost:8000/triage/nursing
```

## 🗺️ Estado actual y próximos pasos

El módulo cuenta con un flujo funcional de triaje, agendamiento, atención médica, recetas, reportes e historial clínico. Su siguiente etapa es la **integración con el sistema clínico institucional**, incluyendo la conexión con usuarios, permisos y base de datos del sistema principal.

Mejoras previstas:

- Middleware de roles y permisos.
- Integración remota en ambiente de pruebas.
- Interconsulta y referencia médica.
- Firma digital o escaneada del profesional.
- Ampliación y actualización del catálogo clínico.

---

<p align="center">
  <strong>MedTriaje</strong> · Módulo clínico en desarrollo para prácticas laborales<br>
  <sub>Proyecto preparado para integración progresiva con un sistema de salud existente.</sub>
</p>
