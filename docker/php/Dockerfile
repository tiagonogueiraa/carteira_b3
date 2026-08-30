# imagem base já vem com PHP 8.3 configurado em modo FPM (FastCGI Process Manager)
FROM php:8.3-fpm

ARG HOST_UID=1000
ARG HOST_GID=1000

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

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copia o binário do Composer direto de uma imagem oficial dele
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Faz o pool do FPM rodar com o mesmo uid/gid do host (em vez de www-data),
# já que o container passa a nascer como esse usuário, não como root.
RUN sed -i "s/^user = .*/user = ${HOST_UID}/" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/^group = .*/group = ${HOST_GID}/" /usr/local/etc/php-fpm.d/www.conf

# pasta de trabalho padrão dentro do container
WORKDIR /var/www/html