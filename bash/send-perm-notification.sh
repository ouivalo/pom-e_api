#!/bin/bash -l

cd "${APP_HOME}" || exit

php bin/console compost:user-notification