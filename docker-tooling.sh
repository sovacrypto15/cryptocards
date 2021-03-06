#!/bin/bash
cd "$(dirname "$0")"
case "$1" in
    backend-migrate)
        docker-compose exec fpm php artisan migrate
        ;;
    backend-tail)
        docker-compose exec fpm php artisan tail
        ;;
    backend-init)
        cp backend/.env.example backend/.env
        docker-compose exec fpm php artisan jwt:secret
        docker-compose exec fpm php artisan key:generate
        docker-compose exec fpm php artisan migrate
        ;;
    backend-test)
        docker-compose exec fpm vendor/bin/phpunit
        ;;
    backend-freshdb)
        docker-compose exec fpm php artisan migrate:refresh --seed
        ;;
    generate)
        docker-compose up codegen_tools_abigen
        docker-compose up codegen_tools_abigen_js
        docker-compose up codegen_tools_proto
        ;;
    *)
        echo "No command specified!"
        exit 1
esac
