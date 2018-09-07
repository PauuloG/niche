FROM php:7.1-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    jq \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get autoremove -y

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime
RUN "date"

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql calendar bcmath mbstring

RUN echo "memory_limit=4096M" > /usr/local/etc/php/conf.d/docker-docker-memory-limit.ini
