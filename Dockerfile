# ---- base (php-fpm) ----
FROM php:8.3-fpm-alpine AS php-base

# System deps
RUN apk add --no-cache icu-dev oniguruma-dev git unzip libzip-dev \
  && docker-php-ext-install intl opcache \
  && docker-php-ext-enable opcache

# (opcjonalnie, jeśli użyjesz bazy)
# RUN docker-php-ext-install pdo_mysql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Ustawienia opcache (sensowne na prod)
RUN { \
  echo "opcache.enable=1"; \
  echo "opcache.enable_cli=1"; \
  echo "opcache.jit=1255"; \
  echo "opcache.jit_buffer_size=64M"; \
  echo "opcache.memory_consumption=192"; \
  echo "opcache.max_accelerated_files=20000"; \
} > /usr/local/etc/php/conf.d/opcache.ini

# ---- deps ----
FROM php-base AS deps
COPY composer.* ./
RUN composer install --no-scripts --no-progress --prefer-dist

# ---- app ----
FROM php-base AS app
ENV APP_ENV=prod
WORKDIR /var/www/html

# pliki aplikacji
COPY . .
# vendor z warstwy deps (cache kompozytora)
COPY --from=deps /var/www/html/vendor ./vendor

# cache/writable
RUN mkdir -p var && chown -R www-data:www-data var
USER www-data

# php-fpm domyślnie nasłuchuje na :9000
EXPOSE 9000

CMD ["php-fpm"]
