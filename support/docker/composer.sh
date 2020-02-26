#!/usr/bin/env bash
cd $(dirname "$0")
docker-compose exec -T -u=$(id -u) php-fpm.readid.stepup.example.com composer $@
