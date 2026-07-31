# Imagen de aplicación para Railway (servicios web, worker y scheduler).
# Base con PHP-FPM y nginx ya resueltos para Laravel, según docs/02-arquitectura-y-datos.md §7.2.

# ---- Etapa 1: compilación de assets con Vite ----
FROM node:22-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

# ---- Etapa 2: aplicación PHP ----
FROM serversideup/php:8.3-fpm-nginx AS base

USER root

# Extensiones requeridas por el sistema: PostgreSQL, Redis, imágenes, internacionalización,
# precisión numérica para métricas y compresión para respaldos.
RUN install-php-extensions \
    pdo_pgsql \
    redis \
    gd \
    intl \
    bcmath \
    zip \
    opcache

ENV PHP_OPCACHE_ENABLE=1 \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=20000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=192 \
    AUTORUN_ENABLED=true \
    SSL_MODE=off

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

USER www-data

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

USER root
