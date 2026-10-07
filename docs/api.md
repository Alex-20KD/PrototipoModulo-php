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
