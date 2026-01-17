# 🚀 Laravel 12 API - Docker Environment

Este projeto é uma API desenvolvida em **Laravel 12** utilizando um ambiente conteinerizado com **Docker**. A arquitetura foi desenhada para oferecer um ambiente de desenvolvimento fluido no Linux/WSL, Mac e Windows, além de estar preparada para deploy em **Kubernetes (K8s)**.



## 🛠️ Stack Tecnológica

* **PHP:** 8.4 (FPM)
* **Servidor Web:** Nginx 1.25
* **Banco de Dados:** MySQL 8.4
* **Ferramentas:** Laravel Telescope (Debug), Composer 2

---

## 🏗️ Estrutura do Docker

O projeto utiliza **Multi-stage Build**, separando o ambiente em dois estágios:
1.  **Stage `dev`**: Inclui o binário do Composer e ferramentas de auxílio ao desenvolvimento.
2.  **Stage `prod`**: Imagem otimizada e segura, sem o Composer, pronta para o cluster Kubernetes.

---

## 🚀 Como Iniciar (Quick Start)

### 1. Clonar o projeto e configurar o ambiente
```bash
git clone <url-do-repositorio>
cd <pasta-do-projeto>
cp .env.example .env
```

### 2. Sincronizar Permissões (Usuários Linux/WSL2)
Para evitar erros de Permission Denied nos logs e cache, exporte seu ID de usuário antes de subir os containers:
```bash
export UID=$(id -u)
export GID=$(id -g)
```
### 3. Subir os containers
```bash
docker compose up -d --build
```
### 4. Instalar dependências e preparar o app
```bash
docker compose exec php-fpm composer install
docker compose exec php-fpm php artisan key:generate
docker compose exec php-fpm php artisan migrate
```

## 🔍 Acessando a Aplicação
API: http://localhost:8080

Telescope (Debug): http://localhost:8080/telescope
