#!/usr/bin/env bash
uid=$(id -u)
gid=$(id -g)

printf "UID=${uid}\nGID=${gid}\nCOMPOSE_PROJECT_NAME=readid" > .env

docker-compose up -d

# Install backend dependencies
docker-compose exec -T php-fpm.readid.stepup.example.com bash -c '
  composer install --prefer-dist -n -o && \
  ./bin/console cache:clear --env=test
'

# Install frontend dependencies
docker-compose exec -T php-fpm.readid.stepup.example.com bash -c '
  yarn install --frozen-lockfile
'