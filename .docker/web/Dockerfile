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

#apache
RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN rm -r /var/www/html

RUN echo 'alias art="php artisan"' >> /root/.bashrc

# vim configuration
ADD ./.docker/vim/.vimrc /root/

ARG VIM_STOCK=false

RUN if [ ${VIM_STOCK} = true ]; then \
    rm /root/.vimrc \
;fi

### REDIS
RUN pecl install redis-5.3.4 \
    && docker-php-ext-enable redis

# Cron entry for www-data

RUN echo "* * * * * www-data bash -l -c '/usr/local/bin/php /var/www/artisan schedule:run >> /var/www/storage/logs/cron-log 2>&1'" >> /etc/crontab

WORKDIR /var/www

RUN echo $(id -u) && \
    echo $(id -u -n) && \
    echo $(id -g) && \
    echo $(id -g -n) && \
    echo $(id -G -n)

ARG USERNAME=root
ARG UID=0
ARG GROUP=root

RUN if [ ${USERNAME} != "root" ]; then \
    useradd -ms /bin/bash ${USERNAME} -u ${UID} \
;fi

USER ${USERNAME}:${GROUP}

RUN echo $(id -u) && \
    echo $(id -u -n) && \
    echo $(id -g) && \
    echo $(id -g -n) && \
    echo $(id -G -n)
