#!/usr/bin/env bash

if [ -z "$1" ]
then
  echo 'Enter the environment by first argument [dev, prod, test]'
  exit 1
fi

#docker
PUID=$(id -u)
PGID=$(id -g)
DB_ROOT_PASSWORD=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 6 ; echo '')
DB_PASSWORD=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 6 ; echo '')

#app
APP_ENV=dev
APP_SECRET=$(xxd -l 16 -p /dev/random)
DATABASE_URL="mysql:\/\/task:${DB_PASSWORD}@db:3306\/task?serverVersion=8.0"

#docker
cp ./docker/.env.dist ./docker/.env && \
sed -i "s/^PUID=.*/PUID=${PUID}/g" ./docker/.env && \
sed -i "s/^PGID=.*/PGID=${PGID}/g" ./docker/.env && \
sed -i "s/^DB_ROOT_PASSWORD=.*/DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD}/g" ./docker/.env && \
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/g" ./docker/.env

#application
cp .env .env.local && \
sed -i "s/^APP_ENV=.*/APP_ENV=${APP_ENV}/g" .env.local && \
sed -i "s/^APP_SECRET=.*/APP_SECRET=${APP_SECRET}/g" .env.local && \
sed -i "s/^DATABASE_URL=.*/DATABASE_URL=${DATABASE_URL}/g" .env.local

#jwt keys
mkdir -p config/jwt && \
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 && \
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

echo -n "Enter PEM password to save in .env.local: "
read -s JWT_PASSPHRASE
sed -i "s/^JWT_PASSPHRASE=.*/JWT_PASSPHRASE=${JWT_PASSPHRASE}/g" .env.local
