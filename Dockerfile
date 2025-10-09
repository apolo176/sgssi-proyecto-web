FROM php:8.1-apache

# Habilitar módulos esenciales
RUN a2enmod rewrite
RUN a2enmod mpm_prefork  # Asegurar que usamos prefork para PHP

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configurar Apache para procesar PHP
RUN echo "<VirtualHost *:80>" > /etc/apache2/sites-available/000-default.conf
RUN echo "  ServerName localhost" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  DocumentRoot /var/www/html" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  <Directory /var/www/html>" >> /etc/apache2/sites-available/000-default.conf
RUN echo "    Options -Indexes +FollowSymLinks" >> /etc/apache2/sites-available/000-default.conf
RUN echo "    AllowOverride All" >> /etc/apache2/sites-available/000-default.conf
RUN echo "    Require all granted" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  </Directory>" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  ErrorLog \${APACHE_LOG_DIR}/error.log" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  CustomLog \${APACHE_LOG_DIR}/access.log combined" >> /etc/apache2/sites-available/000-default.conf
RUN echo "</VirtualHost>" >> /etc/apache2/sites-available/000-default.conf

# Asegurar que el handler de PHP está configurado
RUN echo "<FilesMatch \\.php$>" > /etc/apache2/conf-available/php-handler.conf
RUN echo "  SetHandler application/x-httpd-php" >> /etc/apache2/conf-available/php-handler.conf
RUN echo "</FilesMatch>" >> /etc/apache2/conf-available/php-handler.conf
RUN a2enconf php-handler

# Crear test PHP
RUN echo "<?php echo 'PHP IS WORKING! Version: ' . phpversion(); ?>" > /var/www/html/test.php

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]