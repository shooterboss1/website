# Use the official PHP image with Apache
FROM php:8.1-apache

# Enable Apache modules (like rewrite for .htaccess)
RUN a2enmod rewrite

# Install required PHP extensions for MySQL and other features
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# Copy the application code to the container
COPY . /var/www/html/

# Expose port 80 (Render will automatically map this)
EXPOSE 80
