# Use the Laravel Sail PHP-FPM image as the base image
FROM laravelsail/php80-composer

# Install dependencies for GD and DOM
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd dom