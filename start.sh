#!/usr/bin/env bash
set -euo pipefail

APP_DIR=/var/www/html
cd "${APP_DIR}"

# ---------- Permisos básicos ----------
chown -R www-data:www-data storage bootstrap/cache || true
find storage -type d -exec chmod 0775 {} \; || true
find storage -type f -exec chmod 0664 {} \; || true
chmod -R ug+rwX bootstrap/cache || true

# ---------- Optimización Laravel ----------
echo "Running Laravel optimizations..."
runuser -u www-data -- php artisan optimize --no-ansi

# Symlink de storage
if [ ! -L "${APP_DIR}/public/storage" ]; then
  echo "Creating storage symlink..."
  runuser -u www-data -- php artisan storage:link --no-ansi || true
fi

# ---------- Espera de Base de Datos ----------
if [ "${DB_CONNECTION:-}" = "pgsql" ] && command -v pg_isready >/dev/null 2>&1; then
  echo "Waiting for PostgreSQL..."
  export PGPASSWORD="${DB_PASSWORD:-}"
  until pg_isready \
      -h "${DB_HOST:-127.0.0.1}" \
      -p "${DB_PORT:-5432}" \
      -d "${DB_DATABASE:-postgres}" \
      -U "${DB_USERNAME:-postgres}" >/dev/null 2>&1; do
    echo "Database not ready, retrying in 5 seconds..."
    sleep 5
  done
elif [ "${DB_CONNECTION:-}" = "mysql" ] && command -v mysqladmin >/dev/null 2>&1; then
  echo "Waiting for MySQL..."
  until mysqladmin ping -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" --silent; do
    echo "Database not ready, retrying in 5 seconds..."
    sleep 5
  done
fi

# ---------- Migraciones ----------
echo "Running database migrations..."
until runuser -u www-data -- php artisan migrate --force --no-interaction --no-ansi; do
  echo "Migrations failed or DB not ready yet. Retrying in 5 seconds..."
  sleep 5
done

# ---------- Seeders controlados por ENV ----------
# 1) Para ejecutar todo DatabaseSeeder en el arranque:
#    RUN_SEEDERS_ON_BOOT=true
if [ "${RUN_SEEDERS_ON_BOOT:-false}" = "true" ]; then
  echo "Running DatabaseSeeder on boot..."
  runuser -u www-data -- php artisan db:seed --force --no-interaction --no-ansi || true
fi

# 2) Para ejecutar seeders específicos (coma-separados):
#    RUN_SEEDERS_CLASSES="Database\\Seeders\\InformaticaCoursesSeeder,Database\\Seeders\\OtroSeeder"
if [ -n "${RUN_SEEDERS_CLASSES:-}" ]; then
  IFS=',' read -ra _classes <<< "${RUN_SEEDERS_CLASSES}"
  for _c in "${_classes[@]}"; do
    echo "Running seeder: ${_c}"
    runuser -u www-data -- php artisan db:seed --class="${_c}" --force --no-interaction --no-ansi || true
  done
fi

# ---------- Apache para Railway ----------
PORT="${PORT:-8080}"
APP_HOST="$(echo "${APP_URL:-localhost}" | sed -E 's#^https?://##' | sed 's#/.*$##')"

echo "Configuring Apache to listen on ${PORT} and ServerName ${APP_HOST} ..."

# ServerName para silenciar AH00558
echo "ServerName ${APP_HOST}" > /etc/apache2/conf-available/servername.conf || true
a2enconf servername >/dev/null 2>&1 || true

# Módulos útiles
a2enmod rewrite >/dev/null 2>&1 || true
a2enmod headers >/dev/null 2>&1 || true
a2enmod expires >/dev/null 2>&1 || true

# Escuchar en $PORT
if [ -f /etc/apache2/ports.conf ]; then
  if grep -qE '^[# ]*Listen +80\b' /etc/apache2/ports.conf; then
    sed -ri "s/^[# ]*Listen +80\b/Listen ${PORT}/" /etc/apache2/ports.conf
  fi
  if ! grep -qE "^[# ]*Listen +${PORT}\b" /etc/apache2/ports.conf; then
    echo "Listen ${PORT}" >> /etc/apache2/ports.conf
  fi
fi

# VirtualHost
cat >/etc/apache2/sites-available/railway.conf <<EOF
<VirtualHost *:${PORT}>
    ServerName ${APP_HOST}
    DocumentRoot ${APP_DIR}/public

    <Directory ${APP_DIR}/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

a2dissite 000-default >/dev/null 2>&1 || true
a2ensite railway >/dev/null 2>&1

echo "Starting Apache..."
exec apache2-foreground