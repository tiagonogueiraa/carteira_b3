# Minha Carteira

Aplicação web para registrar e acompanhar uma carteira de ações e FIIs da B3
manualmente — sem conectar conta bancária ou corretora. Cadastre suas compras,
acompanhe o preço médio calculado a partir do histórico de lotes, e visualize a
evolução do capital investido ao longo do tempo.

> **Projeto de estudos.** Construído para aprender Laravel, Vue e Docker na
> prática — não só a "cola" das ferramentas, mas o porquê por trás de cada
> decisão (relacionamentos do Eloquent, Policies, dark mode via CSS variables,
> permissão de arquivo em container Docker, etc). O README documenta os
> comandos do dia a dia justamente para isso.

## Funcionalidades

- **Autenticação completa** — cadastro, login, recuperação de senha e verificação
  de e-mail (Laravel Breeze).
- **Cadastro de ações e FIIs** — cada compra vira um "lote" (quantidade, preço,
  data); o preço médio e a quantidade total são calculados automaticamente a
  partir do histórico, não digitados à mão.
- **Detalhe por ativo** — quantidade total, preço médio e o histórico completo
  de compras de cada ticker.
- **Dashboard** — gráfico do capital investido acumulado nos últimos 6 meses e
  um resumo da carteira.
- **Tema claro/escuro** — com persistência da escolha do usuário.
- **Design system próprio** (`/design-system`) — catálogo dos componentes de UI
  usados no projeto.

## Tecnologias

**Backend**
- Laravel 13.17 (PHP 8.3)
- MySQL 8
- [Inertia.js](https://inertiajs.com) — ponte entre Laravel e Vue sem precisar de API REST separada

**Frontend**
- Vue 3.4 (Composition API / `<script setup>`)
- [shadcn-vue](https://www.shadcn-vue.com) + Tailwind CSS
- [ApexCharts](https://apexcharts.com) — gráficos

**Infraestrutura**
- Docker Compose — Nginx, PHP-FPM, MySQL e Node, todos containerizados

## Como rodar localmente

Pré-requisitos: Docker e Docker Compose instalados.

```bash
# 1. clonar o projeto e entrar na pasta
git clone <url-do-repositorio>
cd carteira_b3

# 2. copiar o .env de exemplo
cp src/.env.example src/.env

# 3. subir os containers (nginx, php, mysql, node)
docker compose up -d

# 4. instalar dependências do PHP
docker compose exec --user $(id -u):$(id -g) php composer install

# 5. gerar a chave da aplicação
docker compose exec --user $(id -u):$(id -g) php php artisan key:generate

# 6. rodar as migrations
docker compose exec php php artisan migrate
```

A aplicação fica disponível em `http://localhost:8000`, e o servidor de
desenvolvimento do Vite (hot-reload do frontend) em `http://localhost:5173`.

### Comandos do dia a dia

Como não há PHP nem Node instalados na máquina host, todo comando roda **dentro
dos containers** — nunca direto no terminal do host.

**PHP / Artisan** (dentro do container `php`):
```bash
docker compose exec php php artisan migrate
docker compose exec php php artisan migrate:rollback
docker compose exec php php artisan tinker
docker compose exec php php artisan route:list
```

**Comandos que criam arquivo** (`make:model`, `make:controller`,
`make:migration`, `make:policy`) precisam de um detalhe a mais —
`--user $(id -u):$(id -g)` logo depois do `exec`:
```bash
docker compose exec --user $(id -u):$(id -g) php php artisan make:model Foo -m
```
**Por quê:** o container `php` roda como `root` por padrão. Sem esse `--user`,
qualquer arquivo criado dentro dele nasce com dono `root` no seu disco — o
editor não consegue salvar em cima depois (erro `EACCES: permission denied`).
Esse `--user` força o comando a rodar com o mesmo UID/GID do seu usuário no
host só naquela execução, sem afetar o container principal (que precisa
continuar rodando como root pra conseguir iniciar o PHP-FPM corretamente).

**Node / NPM** (dentro do container `node`):
```bash
docker compose exec node npm install <pacote>
docker compose exec node npm run build
```
Esse container já roda com o usuário certo por padrão (configurado no
`docker-compose.yml`), então não precisa do `--user` aqui.

**Se corrigir a permissão de um arquivo que já nasceu com dono `root`** (do
tempo antes de saber desse detalhe):
```bash
sudo chown $(whoami):$(whoami) caminho/do/arquivo.php
```

## Estrutura do domínio

- `Stock` — um ativo cadastrado (ticker + tipo: ação ou FII), pertence a um usuário.
- `PurchaseLot` — um lote de compra (quantidade, preço, data), pertence a um `Stock`.
- Quantidade total e preço médio de um `Stock` **não são colunas salvas** — são
  calculados somando os lotes associados, garantindo que o dado nunca fique
  dessincronizado.

## Roadmap

- [ ] Integração com [brapi.dev](https://brapi.dev) para cotação atual dos ativos
- [ ] Comparar capital investido vs. valor de mercado real no gráfico do Dashboard
- [ ] Editar/remover um lote de compra específico (correção de erro de digitação)
- [ ] Migrar checagem de permissão dos controllers para Laravel Policies
