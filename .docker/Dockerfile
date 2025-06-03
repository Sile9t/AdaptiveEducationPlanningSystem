FROM 999669/asap-nv-backend:01e9ac91

RUN docker-php-ext-install bcmath
RUN rm -rf /var/www/database
COPY ./ /var/www
# Set working directory
WORKDIR /var/www

# xdebug
COPY .ops/conf/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# add test debian repository
RUN cp .env.example .env
RUN composer install

RUN php artisan key:generate

# cleanup
RUN apt-get clean \
    && rm -fr /var/lib/apt/lists/* \
    && rm -fr /tmp/* \
    && rm -fr /var/tmp/*

#
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
