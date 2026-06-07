# Build stage
FROM php:8.2-fpm-alpine as builder

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apk add --no-cache \
    build-base \
    curl \
    curl-dev \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    mariadb-dev \
    openssl-dev \
    postgresql-dev \
    sqlite-dev \
    zlib-dev \
    oniguruma-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    xml \
    curl \
    bcmath \
    ctype \
    json \
    mbstring \
    tokenizer \
    fileinfo

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Install Node dependencies and build assets
RUN npm ci && npm run production

# Final stage
FROM php:8.2-fpm-alpine

# Install runtime and build dependencies needed for compiling PHP extensions
RUN apk add --no-cache --virtual .build-deps \
    build-base \
    curl-dev \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libxml2-dev \
    mariadb-dev \
    openssl-dev \
    postgresql-dev \
    sqlite-dev \
    zlib-dev \
    oniguruma-dev \
    pkgconf \
    curl \
    && apk add --no-cache \
    libpng \
    libjpeg-turbo \
    freetype \
    libxml2 \
    mariadb-connector-c \
    openssl \
    postgresql-libs \
    sqlite-libs \
    zlib \
    file \
    nginx \
    supervisor \
    curl

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    xml \
    curl \
    bcmath \
    mbstring && \
    apk del .build-deps

# Set working directory
WORKDIR /app

# Copy files from builder stage
COPY --from=builder /app /app

# Create necessary directories
RUN mkdir -p /app/storage/logs && \
    mkdir -p /app/storage/framework/sessions && \
    mkdir -p /app/storage/framework/views && \
    mkdir -p /app/storage/framework/cache && \
    mkdir -p /app/bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /app && \
    chmod -R 755 /app/storage && \
    chmod -R 755 /app/bootstrap/cache

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/conf.d/default.conf

# Copy Supervisor configuration
COPY docker/supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# Copy PHP-FPM configuration
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Expose port
EXPOSE 8000

# Start script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

ENTRYPOINT ["/usr/local/bin/start.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisor.conf"]
