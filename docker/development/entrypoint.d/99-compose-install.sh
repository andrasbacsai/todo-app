#! /bin/sh

composer install
chown -R www-data:www-data /var/www/html/vendor
