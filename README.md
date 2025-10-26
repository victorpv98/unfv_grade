# UNFV Grade

Aplicacion Laravel para la gestion de calificaciones y carga masiva de notas en la UNFV.

## Arquitectura
- Laravel 10 sobre PHP 8.2 con Apache (`Dockerfile`)
- Base de datos PostgreSQL 15 (`docker-compose.yml:17`)
- Vite y Node 18 para assets (`Dockerfile`)
- Scripts de arranque y despliegue automatizados (`start.sh:1`)

## Requisitos previos
- Docker Desktop 24+ y Docker Compose plugin
- Make o equivalente opcional
- Puertos 8000 (HTTP) y 5433 (PostgreSQL) libres

## Preparacion inicial
1. Clonar el repositorio.
2. Copiar variables de entorno: `cp .env.example .env`.
3. Ajustar las variables de conexion a base de datos y correo segun entorno.
4. Generar la clave de la aplicacion: `docker compose run --rm app php artisan key:generate`.

## Desarrollo local (Docker Compose)
1. Levantar servicios: `docker compose up -d`.
2. Instalar dependencias PHP (si cambian): `docker compose exec app composer install`.
3. Instalar dependencias de frontend: `docker compose exec app npm ci`.
4. Ejecutar migraciones y seeders requeridos: `docker compose exec app php artisan migrate --seed`.
5. Iniciar Vite en modo desarrollo si se necesita hot reload: `docker compose exec app npm run dev`.

Los volumenes montados (`docker-compose.yml:26`) preservan `storage` y `bootstrap/cache` para mantener archivos generados y caches.

## Tareas comunes
- Ejecutar pruebas: `docker compose exec app php artisan test`.
- Limpiar cache de Laravel: `docker compose exec app php artisan optimize:clear`.
- Cargar seeders especificos: `docker compose exec app php artisan db:seed --class="Database\\Seeders\\InformaticaCoursesSeeder"`.

## Guia de despliegue en plataformas basadas en contenedores (Railway, Render, ECS)
La imagen se construye con Apache y ejecuta el script `start.sh:1`, que:
- Repara permisos y crea el enlace de storage (`start.sh:8` y `start.sh:18`).
- Espera a la base de datos PostgreSQL/MySQL antes de correr migraciones (`start.sh:24` y `start.sh:43`).
- Ejecuta seeders si se configuran variables `RUN_SEEDERS_ON_BOOT` o `RUN_SEEDERS_CLASSES` (`start.sh:51`).
- Configura Apache para respetar el puerto suministrado por la plataforma (`start.sh:69`).

### Pasos de despliegue
1. Construir la imagen: `docker build -t registry.example.com/unfv-grade:latest .`.
2. Publicar la imagen en el registro de su plataforma.
3. Crear un servicio en el proveedor (ej. Railway) usando la imagen publicada.
4. Definir variables de entorno minimas:
   - `APP_KEY` (usar `php artisan key:generate --show`).
   - `APP_URL` con el dominio final.
   - Credenciales `DB_*` que apunten a la base de datos administrada.
   - `PORT` (la mayoria de plataformas la inyectan automaticamente).
   - Opcional: `RUN_SEEDERS_ON_BOOT=true` o `RUN_SEEDERS_CLASSES=Database\\Seeders\\InformaticaCoursesSeeder`.
5. Proveer almacenamiento persistente para `storage` y `bootstrap/cache` si la plataforma lo permite.
6. Desplegar el servicio; el script ejecuta migraciones y seeders automaticamente cada vez.

Si la base de datos demora en estar lista, el script reintenta cada 5 segundos hasta conectar (`start.sh:24`).

## Mantenimiento
- Regenerar assets de produccion: `docker compose exec app npm run build`.
- Revisar el tablero de salud de contenedores para logs de Apache y Laravel (`storage/logs/laravel.log`).
- Consultar el historial de cambios en `CHANGELOG.md`.
