FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /app
COPY . /app

CMD ["php", "-S", "0.0.0.0:8080", "router.php"]
