#!/bin/bash
mkdir config/jwt
echo $JWT_PRIVATE_KEY | base64 -d > config/jwt/private.pem
echo $JWT_PUBLIC_KEY | base64 -d > config/jwt/public.pem
# Start default script for PHP apps
$HOME/bin/run
