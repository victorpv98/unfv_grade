FROM php:8.2-apache

# Configure Apache to serve the Laravel public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!<Directory /var/www/>!<Directory ${APACHE_DOCUMENT_ROOT}/>!g' /etc/apache2/apache2.conf

# Install system dependencies, Node.js for Vite, and required PHP extensions
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        zip \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libpq-dev \
        postgresql-client \
        nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable useful Apache modules
RUN a2enmod rewrite headers expires

# Copy Composer from the official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# Install PHP dependencies first to leverage Docker layer caching
COPY composer.json composer.lock artisan ./
# Skip Composer scripts here; artisan bootstrap files are copied later in the build
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install Node dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy the remaining application source
COPY . .

# Build production assets
RUN npm run build

# Ensure directories used by Laravel are writable by Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# Provide the startup script within the image
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
