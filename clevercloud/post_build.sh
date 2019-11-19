#!/bin/bash
# Database migrations
echo 'On joue les migrations'
./bin/console doctrine:migrations:migrate

# Copie des clés pour générer les JWT
echo 'Start copie jwt config file'
echo $JWT_PRIVATE_KEY_64 | base64 --decode > ./config/jwt/private.pem
echo $JWT_PUBLIC_KEY_64 | base64 --decode > ./config/jwt/public.pem

