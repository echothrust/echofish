# Dockerfile-web for echofish
# docker build -t a1 -f Dockerfile-web ..
# docker run --name echofish-web --link echofish-db:db -p 127.0.0.1:80:80 -v `pwd`/php-timezone.ini:/usr/local/etc/php/conf.d/php-timezone.ini -v /etc/timezone:/etc/timezone:ro -v /etc/localtime:/etc/localtime:ro -d a1

# Use PHP 5.6 with Apache for the base image
FROM php:5.6-apache

# Enable the Rewrite Apache mod
RUN cd /etc/apache2/mods-enabled && \
    ln -s ../mods-available/rewrite.load

# Install required PHP extensions
# -- GD
RUN apt-get update && \
    apt-get install -y libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd
# -- mysql
RUN docker-php-ext-install -j$(nproc) mysql pdo_mysql

# Copy HTTP server config, yii framework and echofish sources
# Note: paths refactored for docker-compose context (..)
COPY docker/000-default.conf /etc/apache2/sites-available/
COPY yii /var/www/yii
COPY --chown=www-data:www-data htdocs/ /var/www/html/
COPY docker/db.php /var/www/html/protected/config/
RUN install -o www-data -g www-data -m 0775 -d /var/www/html/assets
