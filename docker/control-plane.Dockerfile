FROM php:8.5.12-cli-bookworm

RUN apt-get update \
    && apt-get install --no-install-recommends --yes unzip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
COPY control-plane/composer.json control-plane/composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-progress
COPY control-plane ./
COPY docker/control-plane-entrypoint.sh /usr/local/bin/control-plane-entrypoint
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod 0555 /usr/local/bin/control-plane-entrypoint

ENTRYPOINT ["/usr/local/bin/control-plane-entrypoint"]
