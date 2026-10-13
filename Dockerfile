FROM dunglas/frankenphp:1-php8.2-bookworm

# Install required PHP extensions for Laravel, PostgreSQL, Excel, and Image manipulation
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    zip \
    gd \
    intl \
    bcmath \
    opcache \
    pcntl \
    redis

WORKDIR /app

# Copy dependency definition files first for better layer caching
COPY composer.json composer.lock ./

# Install Composer dependencies (production only, skip scripts until entrypoint)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy the rest of the application code
COPY . /app

# Copy custom Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Copy and setup entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Prepare storage & bootstrap cache permissions
RUN mkdir -p /app/storage/framework/cache/data \
             /app/storage/framework/sessions \
             /app/storage/framework/views \
             /app/storage/logs \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Render injects the PORT env var; fallback to 80 locally
ENV PORT=80
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
