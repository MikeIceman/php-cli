FROM php:8.1-fpm-alpine

RUN apt-get update && \
    apt-get install -y  git \
            wget \
            libzip-dev \
            zip \
            libwebp-dev \
            libjpeg62-turbo-dev \
            libxpm-dev \
            libpng-dev \
            libpq-dev \
            libicu-dev \
            libfreetype6-dev \
            libmemcached-dev \
            libjpeg62-turbo-dev \
            libpng16-16

## Composer
RUN cd / && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install \
    intl \
    exif \
    zip \
    mbstring \
    iconv \
    pdo \
    pdo_mysql \
    mysqli \
    pcntl \
    bcmath \
    sockets

RUN docker-php-ext-configure gd --with-gd --with-webp-dir --with-jpeg-dir \
    --with-png-dir --with-zlib-dir --with-xpm-dir --with-freetype-dir \
    --enable-gd-native-ttf

RUN docker-php-ext-install gd

# Install xdebug
RUN yes | pecl install xdebug
RUN docker-php-ext-enable xdebug

# Install APCu
RUN pecl install apcu
RUN docker-php-ext-enable apcu

# Instal CodeSniffer
RUN cd /tmp/ && curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar  && \
    cp /tmp/phpcs.phar /usr/local/bin/phpcs && \
    chmod +x /usr/local/bin/phpcs

# Instal CodeBeautifier
RUN cd /tmp/ && curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar && \
    cp /tmp/phpcbf.phar /usr/local/bin/phpcbf && \
    chmod +x /usr/local/bin/phpcbf

# Install nodejs
RUN apt-get install -y gnupg
RUN curl -sL https://deb.nodesource.com/setup_13.x | bash -
RUN apt-get install -y nodejs

# Install Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get -y install yarn

WORKDIR /var/www
