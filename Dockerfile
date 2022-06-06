# syntax=docker/dockerfile:1
FROM php:7.4

# Création du dossier app
RUN mkdir -p /app
WORKDIR /app

# copie des fichier des dépendances dans le workdir
COPY ["composer.json", "composer.lock*", "./"]

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# mise a jour de la liste des paquets
RUN apt update

# Install Imagick extension
RUN apt install -y libmagickwand-dev; \
    pecl install imagick; \
    docker-php-ext-enable imagick; \
    # Success
    true

# Install zip extention
RUN  apt install -y libzip-dev \
      && docker-php-ext-install zip

# install driver for mysql
RUN docker-php-ext-install pdo_mysql

# install dependancies
#RUN composer config allow-plugins.symfony/flex true
#RUN composer install
COPY . .
