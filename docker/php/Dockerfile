# ---------------------------------------------------------
# 1️⃣ Estágio de Build (Comum para ambos)
# ---------------------------------------------------------
FROM composer:2 AS build
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# ---------------------------------------------------------
# 2️⃣ Estágio de Desenvolvimento (Base)
# ---------------------------------------------------------
FROM php:8.4-fpm AS dev

# Dependências de sistema + Git e Ferramentas de Dev
RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libicu-dev unzip git \
    && docker-php-ext-install pdo_mysql zip intl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Inclui o Composer para você usar no dia a dia
COPY --from=build /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
# Em Dev, o volume vai sobrescrever isso, mas mantemos para consistência
COPY --from=build --chown=www-data:www-data /app /var/www

USER www-data
CMD ["php-fpm"]

# ---------------------------------------------------------
# 3️⃣ Estágio de Produção (Limpo para Kubernetes)
# ---------------------------------------------------------
FROM php:8.4-fpm AS prod

# Apenas o essencial (sem Git, sem Composer)
RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libicu-dev unzip \
    && docker-php-ext-install pdo_mysql zip intl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Copia apenas o resultado do build
COPY --from=build --chown=www-data:www-data /app /var/www

# No K8s, rodamos como usuário restrito
USER www-data
CMD ["php-fpm"]