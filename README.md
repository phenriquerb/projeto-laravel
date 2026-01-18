# 🚀 Laravel 12 API - Docker Environment

Este projeto é uma API desenvolvida em **Laravel 12** utilizando um ambiente conteinerizado com **Docker**. A arquitetura foi desenhada para oferecer um ambiente de desenvolvimento fluido no Linux/WSL, Mac e Windows, além de estar preparada para deploy em **Kubernetes (K8s)**.

## 📐 Arquitetura

O projeto utiliza uma **arquitetura em camadas (Layered Architecture)** com separação de responsabilidades, implementando **Repository Pattern** e **Service Layer** para organizar o código e reduzir o acoplamento. A estrutura separa as camadas em:

- **Domain**: Interfaces, contratos e exceções do domínio
- **Application**: Services com lógica de negócio
- **Infrastructure**: Implementações concretas (repositories, etc)
- **HTTP/Presentation**: Controllers, Requests e Responses

Para mais detalhes sobre a arquitetura, consulte a [documentação completa](docs/01-estrutura-projeto.md).



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
# HTTPS
git clone https://github.com/phenriquerb/projeto-laravel.git

# Ou SSH
git clone git@github.com:phenriquerb/projeto-laravel.git

cd projeto-laravel
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

**Scramble (Documentação da API):** http://localhost:8080/api/documentation

> **Nota:** O Scramble está configurado para acesso público, permitindo que visitantes do portfólio possam visualizar a documentação interativa da API.

Telescope (Debug): http://localhost:8080/telescope
