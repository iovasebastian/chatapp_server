# Use the official PHP image with Apache
FROM php:8.2-apache

# Install SQLite and required PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Copy your application files to the Apache web root
COPY . /var/www/html/

# Set permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for the HTTP server
EXPOSE 80
