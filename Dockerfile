FROM php:8.2-apache

# Instalar y habilitar la extensión mysqli para la base de datos
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# COPIAR todo el contenido de tu repositorio dentro de la carpeta pública de Apache
COPY . /var/www/html/

# Asegurar los permisos correctos para que Apache pueda leer los archivos
RUN chown -R www-data:www-data /var/www/html
