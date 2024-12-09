# Use the PHP 8 FPM image as the base image
FROM php:8.3-fpm

# Set environment variables
ENV USER=www
ENV GROUP=www

# Install minimal dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install only core required PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    zip

# Get composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/

# Create user and group
RUN groupadd -g 1000 ${GROUP} && useradd -u 1000 -ms /bin/bash -g ${GROUP} ${USER}

# Set directory ownership
RUN chown -R ${USER} /var/www

# Switch to non-root user
USER ${USER}

# Copy application files
COPY --chown=${USER}:${GROUP} . .

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]