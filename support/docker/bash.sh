#!/usr/bin/env bash
cd $(dirname "$0")
docker-compose exec php-fpm.readid.stepup.example.com /bin/bash