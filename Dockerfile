# syntax=docker/dockerfile:1
FROM php:7.4

# Création du dossier app
RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app

# copie des fichier des dépendances dans le workdir
COPY ["composer.json", "composer.lock*", "./"]

# https://stackoverflow.com/questions/65513366/docker-php-adding-zip-extension/69011965#69011965?newreg=4f02b341fb08434caf4407828166aa6a
# Install system dependencies
RUN apt-get update && apt-get install -y zip
COPY . .
