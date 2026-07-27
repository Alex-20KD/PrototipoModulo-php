# Guía de instalación y pruebas — MedTriaje

## 1. Descripción

**MedTriaje** es un módulo web desarrollado en PHP con Laravel para apoyar el proceso de triaje, agendamiento y atención médica ambulatoria.

El proyecto fue diseñado como un **módulo independiente que se integrará posteriormente a un sistema clínico institucional real**. Por esa razón, sus componentes se encuentran organizados bajo `app/Modules/Triage`, sus rutas usan el prefijo `/triage` y sus tablas usan el prefijo `triage_`.

La versión entregada es un prototipo funcional para demostrar el flujo clínico. La autenticación, los roles de usuario y la conexión definitiva con el sistema institucional serán incorporados durante la etapa de integración.

> Los pacientes y datos incluidos son ficticios y se utilizan exclusivamente para pruebas académicas.

## 2. Requisitos

- PHP 8.4 o superior.
- Composer 2.
- Extensión SQLite habilitada en PHP.
- Navegador web moderno.

No se requiere instalar Node.js para realizar las pruebas funcionales, ya que la interfaz utiliza recursos cargados desde CDN.

## 3. Instalación local

Abra una terminal dentro de la carpeta descomprimida del proyecto y ejecute:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

Luego abra en el navegador:

```text
http://127.0.0.1:8000/triage/nursing
```

> `migrate:fresh --seed` reinicia la base local y carga los datos de demostración. Debe usarse solo para pruebas, porque elimina los datos existentes de la base SQLite local.

## 4. Datos de prueba

| Paciente | Cédula | Edad | Sexo |
|---|---:|---:|---|
| María García López | `0102030405` | 34 | Femenino |
| Juan Pérez Martínez | `0605040302` | 45 | Masculino |
| Ana Torres Ruiz | `1710203040` | 28 | Femenino |

Médicos incluidos:

- Dr. Carlos Mendoza — Medicina General.
- Dra. Lucía Ramírez — Medicina Interna.

## 5. Flujo de pruebas del sistema

### 5.1 Enfermería: registro de triaje

Abra:

```text
/triage/nursing
```

1. Ingrese una cédula de prueba, por ejemplo `0102030405`.
2. Registre los signos vitales: presión arterial, frecuencia cardíaca, frecuencia respiratoria, peso, estatura y temperatura.
3. Escriba un motivo de consulta, por ejemplo: `Paciente refiere cefalea intensa desde hace dos días`.
4. Guarde el triaje.

**Resultado esperado:** se registra el triaje con estado pendiente y queda disponible para ser vinculado a una cita.

### 5.2 Recepción: agendamiento de cita

Abra:

```text
/triage/reception
```

1. Busque al paciente por su cédula.
2. Seleccione un médico.
3. Seleccione uno de los horarios disponibles.
4. Presione **Confirmar Cita y Vincular Triaje**.

**Resultado esperado:** se crea la cita y, si existe un triaje pendiente, el sistema lo vincula automáticamente.

Validaciones incluidas:

- Un paciente no puede tener dos citas en el mismo día.
- Un médico no puede tener dos citas en el mismo horario.

### 5.3 Médico: consulta y atención

Abra:

```text
/triage/doctor
```

1. Seleccione la fecha del día de la cita mediante el filtro.
2. Ubique la cita creada y presione **Atender Paciente**.
3. Complete la anamnesis en tercera persona.
4. Registre antecedentes de hipertensión, diabetes u otras enfermedades crónicas si corresponde.
5. Complete el **Examen Físico Regional**.
6. Busque un diagnóstico CIE-10 y seleccione el tipo de diagnóstico.
7. Agregue diagnósticos asociados si es necesario.
8. Agregue medicamentos mediante el buscador del catálogo MSP, cantidad e indicaciones.
9. Presione **Guardar Consulta y Finalizar**.

**Resultado esperado:** la cita cambia a estado completado y se almacenan la información clínica, diagnósticos y receta.

Ejemplos para probar el buscador CIE-10:

```text
diabetes
hipertensión
cefalea
J00
```

Ejemplos para probar el catálogo de medicamentos:

```text
paracetamol
amoxicilina
losartán
metformina
```

### 5.4 Historial clínico

Desde el panel del médico, presione **Ver Historial** junto a una cita.

**Resultado esperado:** se muestran los datos del paciente y una línea de tiempo con sus consultas, signos vitales, diagnósticos, recetas y anamnesis registradas.

### 5.5 Formulario 002 en PDF

En una cita completada, presione **Ver PDF**.

**Resultado esperado:** se descarga el Formulario 002 con datos del paciente, antecedentes, examen físico, signos vitales, diagnósticos, receta y médico responsable.

### 5.6 Reportes

Abra:

```text
/triage/reports
```

**Resultado esperado:** se visualizan indicadores de pacientes atendidos, consultas completadas, recetas emitidas, triajes pendientes, atenciones por fecha y diagnósticos frecuentes.

## 6. Rutas principales

| Área | Dirección local |
|---|---|
| Enfermería | `http://127.0.0.1:8000/triage/nursing` |
| Recepción | `http://127.0.0.1:8000/triage/reception` |
| Médico | `http://127.0.0.1:8000/triage/doctor` |
| Reportes | `http://127.0.0.1:8000/triage/reports` |

## 7. Alcance de la entrega

El módulo implementa el flujo funcional de:

```text
Enfermería → Triaje → Recepción → Agendamiento → Médico → Historial / PDF / Reportes
```

Para una integración institucional definitiva se contemplan, entre otras mejoras, autenticación, roles y permisos, control de acceso a datos clínicos, firma del médico, interconsultas y conexión con la base de datos central del sistema anfitrión.
