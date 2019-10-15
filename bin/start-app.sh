#!/bin/bash
echo 'Start copie jwt config file'
echo $JWT_PUBLIC_KEY
echo $JWT_PRIVATE_KEY | base64 --decode > config/jwt/private.pem
echo $JWT_PUBLIC_KEY | base64 --decode > config/jwt/public.pem
# Start default script for PHP apps
$HOME/bin/run
