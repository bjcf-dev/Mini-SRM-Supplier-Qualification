FROM php:8.4-cli-alpine

# Instalar pdo_pgsql para conectar PHP con Postgres
RUN apk add --no-cache libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copiar Composer oficial desde su imagen
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]