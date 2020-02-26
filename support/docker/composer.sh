#!/usr/bin/env bash
cd $(dirname "$0")
docker-compose exec -T php-fpm.readid.stepup.example.com composer $@
