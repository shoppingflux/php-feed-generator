# Utiliser l'image officielle PHP 8.4 avec Apache
FROM php:8.4-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    && docker-php-ext-install zip pdo_mysql gd mbstring

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet dans le conteneur
COPY . /var/www/html

# Installer les dépendances PHP via Composer
RUN composer install --no-interaction --no-progress --optimize-autoloader

# Exposer le port HTTP par défaut
EXPOSE 80

# Commande de démarrage du conteneur
CMD ["apache2-foreground"]
