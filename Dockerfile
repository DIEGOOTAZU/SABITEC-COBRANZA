FROM php:8.1-apache

# Instalar extensiones necesarias para MySQL y otras dependencias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar archivos del proyecto al contenedor
WORKDIR /var/www/html
COPY . .

# Exponer el puerto 80
EXPOSE 80

# Iniciar el servidor PHP
CMD ["php", "-S", "0.0.0.0:80", "-t", "."]
