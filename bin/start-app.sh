#!/bin/bash
echo 'Start copie jwt config file'
echo $JWT_PRIVATE_KEY_64 | base64 --decode > /app/config/jwt/private.pem
echo $JWT_PUBLIC_KEY_64 | base64 --decode > /app/config/jwt/public.pem
# Start default script for PHP apps
$HOME/bin/run
