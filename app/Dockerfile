FROM php:8.0-apache

# Устанавливаем необходимые расширения
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Включаем модуль rewrite для Apache
RUN a2enmod rewrite

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем настройки Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Копируем код приложения
COPY . /var/www/html

# Включаем отображение ошибок PHP (для разработки)
RUN echo "display_errors=On\nerror_reporting=E_ALL" > /usr/local/etc/php/php.ini
