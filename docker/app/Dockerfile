# BASE IMAGE
FROM dunglas/frankenphp:1-php8.5 AS base

RUN install-php-extensions \
    mysqli \
    pdo \
    pdo_mysql \
    gd \
    intl \
    zip \
    opcache

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# DEPENDENCIES STAGE
FROM base AS deps

WORKDIR /var/www/html

RUN install-php-extensions \
    @composer

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN npm install -g pnpm@12

# BUILD STAGE
FROM deps AS build

WORKDIR /var/www/html

COPY ./app /var/www/html

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

RUN pnpm install --frozen-lockfile \
    && pnpm run build

# RUN STAGE
FROM base AS run

WORKDIR /var/www/html

COPY --from=build /var/www/html/ ./

RUN ln -s /var/www/html/storage/app/public /var/www/html/public/storage