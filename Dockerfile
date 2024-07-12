FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application Symfony dans le conteneur
#COPY . /code


# Copier le composer.json et composer.lock
#COPY app/composer.json app/composer.lock /home/dev/code

# Set working directory
#WORKDIR /code


# Install Symfony Flex and other dependencies
RUN composer install --no-scripts --no-autoloader && composer dump-autoload --optimize
