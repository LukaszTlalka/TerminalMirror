FROM php:7.4-apache

MAINTAINER Lukasz Tlalka

RUN apt update
RUN apt install -y cron vim curl zip git tmux default-mysql-client
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        graphviz \
        optipng \
        imagemagick


# vim configuration
ADD ./.docker/vim/.vimrc /root/

# extensions
RUN docker-php-ext-install bcmath mbstring pdo_mysql gd zip mysqli

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# configure PHP
RUN sed -i -e "s/;\?max_input_vars = .*/max_input_vars = 5000/g"  /usr/local/etc/php/php.ini-production
RUN sed -i -e "s/post_max_size = 8M/post_max_size = 800M/g" /usr/local/etc/php/php.ini-production
RUN sed -i -e "s/upload_max_filesize = 2M/upload_max_filesize = 2000M/g" /usr/local/etc/php/php.ini-production
RUN sed -i -e "s/max_file_uploads = 20/max_file_uploads = 2000/g" /usr/local/etc/php/php.ini-production

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# NODE
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -

RUN apt -y install nodejs
RUN apt -y install locales-all

# cache composer and npm
#RUN cd /cache/ && composer install --no-scripts
#RUN cd /cache/ && npm install && npm run production
#
#RUN mkdir /cache-llm/
#RUN mv /cache/node_modules /cache-llm/
#RUN mv /cache/vendor /cache-llm/
#RUN rm -rf /cache

#apache
RUN a2enmod rewrite

RUN apt clean

ADD ./ /share

RUN rm -rf /var/www/html
RUN ln -s /share/public /var/www/html
RUN touch /share/storage/logs/laravel.log

RUN chmod 777 /share/storage -R
RUN chmod 777 /share/bootstrap/cache/ -R


### REDIS 
RUN pecl install redis-5.3.4 \
    && docker-php-ext-enable redis

RUN cd /share/ && composer install
RUN cd /share/ && npm install && npm run production


RUN echo "* * * * * bash -l -c '/usr/local/bin/php /share/artisan schedule:run >> /tmp/cron-log 2>&1'" > /var/spool/cron/crontabs/root
RUN crontab /var/spool/cron/crontabs/root

ENTRYPOINT ["bash", "-c"]
CMD ["/usr/local/bin/apache2-foreground"]

