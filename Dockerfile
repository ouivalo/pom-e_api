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
#RUN composer update -n --no-cache
RUN composer install --no-scripts --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-ansi --no-progress
#RUN yes | composer install -n --no-cache

COPY . .







#RUN openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass env:${JWT_PASSPHRASE}

#RUN openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:"beef3957a98872a91b3dd3bcf1d3bd87"
#RUN openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout