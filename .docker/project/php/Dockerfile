# Use the PHP 8 FPM image as the base image.
FROM php:8.3-fpm

# Sets the 'USER' environment variable to 'www'.
ENV USER=www
# Sets the 'GROUP' environment variable to 'www'.
ENV GROUP=www

# Installs the required dependencies for the PHP extensions and tools.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Cleans up the package cache.
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installs the Redis PHP extension and enables it.
RUN pecl install redis && docker-php-ext-enable redis

# Installs various PHP extensions.
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip  # Agrega 'zip' aquí

# Installs the MySQL PHP extension.
RUN docker-php-ext-install mysqli pdo_mysql

# Installs the PostgreSQL PHP extension.
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql


# Copies the Composer executable from the official Composer image to the container.
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer


# Sets the working directory to '/var/www/'.
WORKDIR /var/www/

# Adds a user and group with the specified 'USER' and 'GROUP' environment variables.
RUN groupadd -g 1000 ${GROUP} && useradd -u 1000 -ms /bin/bash -g ${GROUP} ${USER}

# Changes the ownership of the '/var/www/' directory to the new user.
RUN chown -R ${USER} /var/www

# Changes the user to the newly created user.
USER ${USER}

# Copies the application files to the container and sets the ownership to the new user.
COPY --chown=${USER}:${GROUP} . .

# Exposes port 9000.
EXPOSE 9000

# Starts the PHP-FPM process.
CMD ["php-fpm"]
