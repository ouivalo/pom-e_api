#!/bin/bash
echo 'Start copie jwt config file'
mkdir config/jwtt
echo $JWT_PRIVATE_KEY | base64 -D > config/jwtt/private.pem
echo $JWT_PUBLIC_KEY | base64 -D > config/jwtt/public.pem
# Start default script for PHP apps
$HOME/bin/run
