#!/bin/sh
cd /niche
composer install --no-interaction
php-fpm
