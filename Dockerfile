FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql zip

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy application code
COPY . /var/www/html

# Configure Apache to serve from the public directory
RUN sed -i -e 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf
RUN sed -i -e 's/<Directory \/var\/www\/html>/<Directory \/var\/www\/html\/public>/g' /etc/apache2/apache2.conf

# Set environment variable for PHP application
ENV APP_ENV=development

# Expose port 80
EXPOSE 80

# Set working directory
WORKDIR /var/www/html

# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Adjust permissions
RUN chown -R www-data:www-data /var/www/html
RUN find /var/www/html -type d -exec chmod 755 {} \;
RUN find /var/www/html -type f -exec chmod 644 {} \;

# Ensure logs and uploads directories are writable
RUN mkdir -p /var/www/html/logs /var/www/html/public/uploads
RUN chown -R www-data:www-data /var/www/html/logs /var/www/html/public/uploads
