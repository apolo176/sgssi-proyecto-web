FROM php:8.4-apache

# Habilitar módulos esenciales
RUN a2enmod rewrite
RUN a2enmod mpm_prefork  # Asegurar que usamos prefork para PHP
RUN a2enmod headers #Habilitar el módulo que permite añadir cabeceras HTTP

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configurar Apache para procesar PHP
# Reemplaza la línea "ServerTokens OS" por "ServerTokens Prod usando una expresión regular" para ocultar la información del SO
RUN sed -i 's/^[ \t]*ServerTokens OS[ \t]*$/ServerTokens Prod/' /etc/apache2/conf-enabled/security.conf
# Reemplaza la línea "ServerSignature On" por "ServerSignature Off" para ocultar la información de páginas de error
RUN sed -i 's/^[ \t]*ServerSignature On[ \t]*$/ServerSignature Off/' /etc/apache2/conf-enabled/security.conf

RUN echo "<VirtualHost *:80>" > /etc/apache2/sites-available/000-default.conf
RUN echo "  ServerName localhost" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  DocumentRoot /var/www/html" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  <Directory /var/www/html>" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  Header always set Content-Security-Policy \"default-src 'self'; script-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self';\"" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  Header always set X-Frame-Options \"SAMEORIGIN\"" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  Header unset X-Powered-By" >> /etc/apache2/sites-available/000-default.conf
RUN echo "  Header always set X-Content-Type-Options \"nosniff\"" >> /etc/apache2/sites-available/000-default.conf
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
RUN mkdir -p /var/www/html/app/logs && chmod -R 777 /var/www/html/app/logs

EXPOSE 80
CMD ["apache2-foreground"]
