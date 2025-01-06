# Use the official PHP image with Apache
FROM php:8.2-apache

# Install necessary dependencies for building extensions
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Copy your application files to the Apache web root
COPY . /var/www/html/

# Set permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for the HTTP server
EXPOSE 80
