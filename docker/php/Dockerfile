# imagem base já vem com PHP 8.3 configurado em modo FPM (FastCGI Process Manager)
FROM php:8.3-fpm

# instala pacotes de sistema necessários pra compilar certas extensões do PHP
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip

# instala as extensões do PHP que o Laravel precisa:
# pdo_mysql -> conectar com MySQL
# mbstring  -> manipulação de strings multibyte (acentuação, etc)
# exif, gd  -> manipulação de imagem
# bcmath    -> cálculo com precisão decimal (ótimo pro seu caso, valores financeiros!)
# pcntl     -> controle de processos (usado por filas/queues do Laravel)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# copia o binário do Composer direto de uma imagem oficial dele
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# pasta de trabalho padrão dentro do container
WORKDIR /var/www/html