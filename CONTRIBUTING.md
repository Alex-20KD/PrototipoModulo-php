# Guía de Contribución — MedTriaje

¡Bienvenido al equipo de desarrollo de **MedTriaje**! Este documento resume el flujo de trabajo, convenciones y estándares requeridos para colaborar eficazmente mediante **GitHub Flow**.

---

## 1. Entorno de Desarrollo

Antes de comenzar a trabajar, asegúrate de tener tu entorno local levantado y funcionando con Docker (PHP 8.4 + MySQL 8.4).

Consulta los pasos detallados en el [README.md](README.md).

> **⚠️ AVISO SOBRE SECRETOS Y PRUEBAS:**
> - **NUNCA subas ni versionas el archivo `.env`**. Solo se actualiza `.env.example` con valores de ejemplo cuando se agregan nuevas variables.
> - **Los tests nunca deben correr contra la base de datos de desarrollo.** La suite está forzada a usar `medtriaje_test`.

---

## 2. Flujo de Ramas (GitHub Flow)

- **`main`** es la rama principal siempre estable y desplegable. **Nadie hace `push` directo a `main`**.
- Todo trabajo se realiza en ramas cortas derivadas de `main`.
- Nombra tus ramas según el tipo de cambio:
  - `feat/nombre-tarea`: Nueva funcionalidad.
  - `fix/nombre-bug`: Corrección de errores.
  - `chore/nombre-tarea`: Tareas de configuración, dependencias o mantenimiento.
  - `docs/nombre-doc`: Cambios exclusivos en documentación.
  - `test/nombre-test`: Incorporación o ajuste de pruebas.

Para mantener tu rama al día antes de abrir un PR o mergear, haz un rebase:
```bash
git fetch origin
git pull --rebase origin main
```

---

## 3. Convención de Commits

Utilizamos **Conventional Commits** en español:

- `feat: agregar buscador de medicamentos por código ATC`
- `fix: corregir validación de presión arterial en triaje`
- `chore: actualizar configuración de Pint`
- `docs: actualizar guía de instalación en README`
- `test: agregar prueba de citas duplicadas en recepción`

---

## 4. Pull Requests (PR)

- **PRs pequeños:** Procura que cada PR tenga menos de **400 líneas de cambio**. Esto agiliza la revisión y reduce conflictos.
- Vincula siempre el issue correspondiente (usando `Closes #numero-issue`).
- Sigue la plantilla automática provista en `.github/pull_request_template.md`.

### Comandos obligatorios antes de abrir un PR
Ejecuta siempre dentro del contenedor:

1. **Formato de código:**
   ```bash
   docker compose exec app vendor/bin/pint
   ```
2. **Suite de pruebas:**
   ```bash
   docker compose exec app php artisan test
   ```

Ambos deben completarse sin errores antes de enviar el PR.

---

## 5. Regla sobre Migraciones de Base de Datos

- **Avisar al equipo:** Antes de crear una nueva migración, comunica al equipo qué tabla o campo vas a modificar para evitar colisiones de fecha o dependencias cruzadas.
- **Inmutabilidad:** **NUNCA modifiques una migración que ya fue mergeada a `main`**. Si necesitas cambiar un campo o una tabla existente, crea una nueva migración que aplique el cambio (`alter table`).

---

## 6. Definition of Done (Criterio de Aceptación)

Una tarea se considera terminada (**Done**) únicamente cuando:
1. **Funciona:** Cumple con todos los criterios de aceptación del issue.
2. **Pruebas:** Incluye al menos una prueba automatizada que valide el cambio o la corrección.
3. **CI en verde:** Los jobs de `lint` (Pint) y `tests` en GitHub Actions pasan exitosamente.
4. **Sin secretos:** No se incluyen claves, credenciales reales ni volcados de datos privados.
5. **Documentación:** Se actualizan los documentos y comentarios si la funcionalidad lo amerita.
6. **Aprobación:** Cuenta con la revisión y visto bueno de al menos un compañero de equipo.
