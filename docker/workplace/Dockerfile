FROM phusion/baseimage:0.11

RUN DEBIAN_FRONTEND=noninteractive
RUN locale-gen en_US.UTF-8

ENV LANGUAGE=en_US.UTF-8
ENV LC_ALL=en_US.UTF-8
ENV LC_CTYPE=en_US.UTF-8
ENV LANG=en_US.UTF-8
ENV TERM xterm

# Add the "PHP 7" ppa
RUN apt-get install -y software-properties-common && \
    add-apt-repository -y ppa:ondrej/php

RUN echo 'DPkg::options { "--force-confdef"; };' >> /etc/apt/apt.conf

# yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

# Install "PHP Extentions", "libraries", "Software's"
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y --allow-downgrades --allow-remove-essential \
        --allow-change-held-packages \
        apt-utils \
        libldap2-dev \
        libzip-dev zip unzip \
        php7.4-cli \
        php7.4-common \
        php7.4-curl \
        php7.4-intl \
        php7.4-json \
        php7.4-xml \
        php7.4-mbstring \
        php7.4-mysql \
        php7.4-zip \
        php7.4-bcmath \
        php7.4-memcached \
        php7.4-gd \
        php7.4-dev \
        php7.4-ldap \
        php7.4-zip \
        php7.4-xdebug \
        pkg-config \
        libcurl4-openssl-dev \
        libedit-dev \
        libssl-dev \
        libxml2-dev \
        xz-utils \
        libsqlite3-dev \
        sqlite3 \
        git \
        curl \
        vim \
        nasm \
        nano

#####################################
# User
#####################################

ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN set -xe; \
    groupadd -g ${PGID} dev && \
    useradd -l -u ${PUID} -g dev -m dev -G docker_env && \
    usermod -p "*" dev -s /bin/bash

RUN pecl channel-update pecl.php.net

#####################################
# Composer:
#####################################

RUN curl -s http://getcomposer.org/installer | php && \
    echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc && \
    mv composer.phar /usr/local/bin/composer

RUN . ~/.bashrc

#####################################
# xDebug
#####################################

RUN sed -i 's/^;//g' /etc/php/7.4/cli/conf.d/20-xdebug.ini

# ADD for REMOTE debugging
COPY ./xdebug.ini /etc/php/7.4/cli/conf.d/xdebug.ini

######################################
# Node Yarn:
######################################

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -y yarn

######################################
# CS Fixer
######################################

RUN curl -L https://cs.symfony.com/download/php-cs-fixer-v2.phar -o php-cs-fixer \
    && chmod a+x php-cs-fixer \
    && mv php-cs-fixer /usr/local/bin/php-cs-fixer

######################################
# Bash updates
######################################

# makefile autocomplete
RUN echo "complete -W \"\`grep -oE '^[a-zA-Z0-9_-]+:([^=]|$)' ?akefile | sed 's/[^a-zA-Z0-9_.-]*$//'\`\" make" >> ~/.bashrc && \
    echo "complete -W \"\`grep -oE '^[a-zA-Z0-9_-]+:([^=]|$)' ?akefile | sed 's/[^a-zA-Z0-9_.-]*$//'\`\" make" >> ~/.bash_profile && \
    echo "complete -W \"\`grep -oE '^[a-zA-Z0-9_-]+:([^=]|$)' ?akefile | sed 's/[^a-zA-Z0-9_.-]*$//'\`\" make" >> /home/dev/.bashrc && \
    echo "complete -W \"\`grep -oE '^[a-zA-Z0-9_-]+:([^=]|$)' ?akefile | sed 's/[^a-zA-Z0-9_.-]*$//'\`\" make" >> /home/dev/bash_profile

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

######################################
# PHP MND (conflicts with local repo)
######################################
USER dev

RUN composer global require povils/phpmnd && \
    echo "export PATH=${PATH}:${HOME}/.composer/vendor/bin" >> ~/.bashrc

USER root

# Set default work directory
WORKDIR /var/www