# Documentación de API - MedTriaje

Todas las peticiones a la API deben incluir el prefijo `/api` y responderán con la siguiente estructura base:

```json
{
    "status": true,
    "message": "Mensaje descriptivo",
    "data": null
}
```

## Autenticación

Los endpoints protegidos requieren el token Sanctum en la cabecera:
`Authorization: Bearer {token}`

### POST `/api/auth/login`
Inicia sesión como personal (Staff) y devuelve un token de Sanctum.
- **Body**:
  ```json
  {
      "email": "test@example.com",
      "password": "secret"
  }
  ```
- **Respuestas**:
  - `200 OK`: Login exitoso, devuelve `token` y `role` en `data`. Si es doctor, incluye `doctor_id`.
  - `401 Unauthorized`: Credenciales inválidas.
  - `429 Too Many Requests`: Más de 5 intentos fallidos por minuto.

### POST `/api/auth/logout`
Cierra la sesión actual (revoca el token en uso).
- **Headers**: `Authorization: Bearer {token}`
- **Respuestas**: `200 OK`.

### POST `/api/auth/logout-all`
Cierra todas las sesiones del usuario en todos los dispositivos.
- **Headers**: `Authorization: Bearer {token}`
- **Respuestas**: `200 OK`.

### GET `/api/auth/me`
Obtiene los datos del personal autenticado.
- **Headers**: `Authorization: Bearer {token}`
- **Respuestas**:
  - `200 OK`: Devuelve los datos del `Staff`.
  - `401 Unauthorized`: Token no válido o ausente.

## Matriz de Roles y Permisos

La API implementa un modelo de autorización basado en roles (`StaffRole`):
- `nurse`: Enfermería.
- `reception`: Recepción.
- `doctor`: Médico.

| Endpoint | Rol Permitido | Notas de Acceso |
|---|---|---|
| `GET /api/ping` | Público | No requiere autenticación. |
| `GET /up` | Público | No requiere autenticación. (Healthcheck) |
| `POST /api/auth/login` | Público | No requiere autenticación. |
| `POST /api/triage/vital-signs` | `nurse` | Solo personal de enfermería. |
| `GET /api/reception/appointments` | `reception` | Solo personal de recepción. |
| `POST /api/reception/appointments` | `reception` | Solo personal de recepción. |
| `GET /api/doctor/appointments` | `doctor` | Solo el médico puede ver sus propias citas (validado por Policy). |
| `GET /api/doctor/pdf/{appointment}` | `doctor` | Solo el médico asignado a la cita puede descargar el PDF. |
| `GET /api/patients/{user_id}/history` | `doctor` | Solo personal médico tiene acceso al historial clínico del paciente. |
| `GET /api/reports` | `doctor`, `reception` | Acceso a reportes. |

### Códigos de Error Comunes de Autorización
- **401 Unauthorized**: El token Sanctum es inválido, ha expirado o no se proporcionó en las cabeceras (`Authorization: Bearer {token}`).
- **403 Forbidden**: El usuario está autenticado pero no tiene el rol necesario para el endpoint, o su Policy denegó el acceso (ej. un médico intentando ver datos de la cita de otro médico). Ambos devolverán JSON estandarizado:
```json
{
    "status": false,
    "message": "No tiene permisos para acceder a este recurso",
    "data": null
}
```
