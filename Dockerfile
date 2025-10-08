# Use official PHP Apache image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy all project files to Apache root
COPY . .

# Enable Apache rewrite (optional but useful)
RUN a2enmod rewrite

# Render uses PORT env variable
ENV PORT=10000
EXPOSE 10000

# Start Apache server
CMD ["apache2-foreground"]
