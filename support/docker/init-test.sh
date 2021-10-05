#!/usr/bin/env bash
uid=$(id -u)
gid=$(id -g)

printf "UID=${uid}\nGID=${gid}\nCOMPOSE_PROJECT_NAME=readid" > .env

docker-compose up -d

docker-compose exec -T php-fpm.readid.stepup.example.com bash -c '
  composer install --prefer-dist -n -o && \
  ./bin/console cache:clear --env=test && \
  yarn install --frozen-lockfile
'