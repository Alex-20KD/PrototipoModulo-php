# 🏥 MedTriaje

**MedTriaje** es el backend de triaje y citas médicas desarrollado para un proyecto universitario (IS-803, ULEAM).
Actualmente implementa el flujo de registro de signos vitales, agendamiento de citas, consulta médica (diagnósticos CIE-10, receta, anamnesis) e historial clínico en una arquitectura modular.
El proyecto está configurado para un entorno de desarrollo reproducible e instantáneo en cualquier plataforma.

## Requisitos previos

- **Git**
- **Docker Engine 24+** con **Docker Compose v2** (el comando `docker compose`, sin guion)

## Arranque rápido

Sigue estos pasos para levantar el proyecto con sus dependencias y base de datos (PHP 8.4 + MySQL 8.4):

1. **Clonar el repositorio y entrar en la carpeta:**
   ```bash
   git clone <url-del-repositorio> PrototipoModulo-php
   cd PrototipoModulo-php
   ```

2. **Preparar variables de entorno:**
   ```bash
   cp .env.example .env
   ```

3. **Generar la clave de la aplicación (antes de levantar los contenedores):**
  ```bash
   docker compose run --rm --no-deps --entrypoint php app artisan key:generate
  ```
   *Por qué antes:* Docker lee el `.env` al crear el contenedor. Si la clave se genera después, los contenedores siguen con `APP_KEY` vacío y las páginas dan error 500.

4. **Levantar los contenedores (y construir la imagen):**
  ```bash
   docker compose up -d --build
  ```

5. **Ejecutar migraciones y sembrar datos de prueba (seeders):**
   ```bash
   docker compose exec app php artisan migrate --seed
   ```
   *Nota:* El script de inicio (`docker/entrypoint.sh`) espera automáticamente a que MySQL esté listo.

6. **Comprobar que el sistema responde:**
   Abre en tu navegador [http://localhost:8080/up](http://localhost:8080/up) y [http://localhost:8081/up](http://localhost:8081/up), o ejecuta desde la terminal:
   ```bash
   curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/up
   ```
   *Resultado esperado:* Debe devolver `200`. La interfaz de triaje está en `/triage/nursing`.

## Tests y formato

Para ejecutar la suite de pruebas automatizadas dentro del contenedor:

```bash
docker compose exec app php artisan test
```

> **⚠️ AVISO IMPORTANTE SOBRE LOS TESTS:**
> Los tests **nunca** deben correr contra la base de datos de desarrollo. Para evitar pérdida de datos, `phpunit.xml` y `tests/TestCase.php` fuerzan el uso de la base de datos aislada `medtriaje_test` y **abortan** la ejecución si no es así.
> La base `medtriaje_test` se crea automáticamente mediante `docker/mysql-init/01-test-db.sql` **solo en la primera inicialización del volumen de MySQL**. Si la base de pruebas no existe o arroja error de conexión, debes limpiar el volumen ejecutando `docker compose down -v` y volver a levantar los contenedores.

Para formatear el código usando Laravel Pint:
```bash
docker compose exec app vendor/bin/pint
```

## Dos instancias del backend

El `docker-compose.yml` levanta dos instancias idénticas del backend apuntando a la misma base de datos para simular un entorno concurrente o balanceado:
- **`app`**: Disponible en el puerto `8080`.
- **`app2`**: Disponible en el puerto `8081`.
Ambas responden de la misma manera y comparten la misma sesión en base de datos.

## Servidor SFTP para PDF

Para probar la subida de reportes clínicos en formato PDF al servidor SFTP (PC5), debes levantar el contenedor de SFTP. Está configurado con el perfil `sftp`, por lo que se debe iniciar de manera explícita:

```bash
docker compose --profile sftp up -d sftp
```

Las credenciales por defecto están configuradas en tu `.env.example` o `.env`.

## Estructura de carpetas relevante

- `app/Modules/Triage/`: Contiene toda la lógica modular (Controladores, Modelos).
- `routes/`: Archivos de rutas (como `triage.php` donde se agrupan las rutas del módulo).
- `database/`: Migraciones y seeders para estructurar MySQL y poblar datos ficticios.
- `docker/`: Scripts de inicialización como el `entrypoint.sh` (gestión de permisos de `storage` y espera de base de datos) y la inicialización de MySQL (`mysql-init/`).

## Autenticación de Staff y roles (desarrollo)

Para la autenticación de la API mediante Laravel Sanctum, se utiliza el modelo `Staff` (tabla `staff`) con los siguientes roles definidos en `StaffRole`:

| Rol (`role`) | Email de desarrollo | Contraseña | Detalle |
| :--- | :--- | :--- | :--- |
| `nurse` | `enfermera@medtriaje.test` | `password123` | Personal de enfermería (triaje y signos vitales) |
| `reception` | `recepcion@medtriaje.test` | `password123` | Personal de recepción (gestión de citas) |
| `doctor` | `medico@medtriaje.test` | `password123` | Médico (vinculado a un registro en `triage_doctors`) |

## Problemas comunes

- **Puerto 8080 u 8081 ocupado:** Cambia las variables `APP_PORT` o `APP2_PORT` en tu archivo `.env`.
- **Permisos de `storage`:** El `docker/entrypoint.sh` asigna permisos a las carpetas `storage` y `bootstrap/cache` al iniciar, pero si tienes problemas de escritura, reinicia los contenedores.
- **MySQL tarda en arrancar la primera vez:** Es normal. El contenedor `app` esperará automáticamente a que la base de datos esté lista antes de operar.
- **Error de contraseña o base de datos no existe:** Si cambiaste las credenciales en el `.env` o la base `medtriaje_test` no se creó, es porque el volumen de Docker retiene el estado de la primera vez. Ejecuta `docker compose down -v` para destruir el volumen y vuelve a iniciar.
- **Linux con kernel actualizado (error al crear redes en Docker):** Si actualizaste el kernel recientemente sin reiniciar, Docker puede fallar al crear la red interna. Solución: Reinicia tu equipo.

- **Error 500 con `MissingAppKeyException`:** la clave se generó después de levantar los contenedores. Ejecuta `docker compose up -d` para recrearlos.
- **No puedes borrar el directorio del proyecto:** `storage/` y `bootstrap/cache` los escribe `www-data` desde el contenedor. Usa `sudo rm -rf` o ejecuta antes `docker compose exec app chown -R $(id -u):$(id -g) storage bootstrap/cache`.

## Estado del proyecto

**Qué existe actualmente:**
- Flujo funcional: toma de signos vitales, agendamiento, consulta, historial, diagnóstico CIE-10.
- Catálogo interno de diagnósticos y medicamentos.
- Generación de formulario PDF.

**Qué falta (en desarrollo):**
- API REST/JSON para cliente web y móvil.
- Autenticación de usuarios.
- Integración con el servidor de archivos de PC5.
- Mensajes de validación traducidos al español.

---

Para más detalles sobre cómo colaborar y los estándares del equipo, revisa la [Guía de Contribución (CONTRIBUTING.md)](CONTRIBUTING.md).
