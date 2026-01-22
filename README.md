# 🚀 Laravel 12 API - Docker Environment

Este projeto é uma API desenvolvida em **Laravel 12** utilizando um ambiente conteinerizado com **Docker**. A arquitetura foi desenhada para oferecer um ambiente de desenvolvimento fluido no Linux/WSL, Mac e Windows, além de estar preparada para deploy em **Kubernetes (K8s)**.

## 📐 Arquitetura

O projeto utiliza uma **arquitetura em camadas (Layered Architecture)** com separação de responsabilidades, implementando **Repository Pattern** e **Service Layer** para organizar o código e reduzir o acoplamento. A estrutura separa as camadas em:

-   **Domain**: Interfaces, contratos e exceções do domínio
-   **Application**: Services com lógica de negócio
-   **Infrastructure**: Implementações concretas (repositories, etc)
-   **HTTP/Presentation**: Controllers, Requests e Responses

Para mais detalhes sobre a arquitetura, consulte a [documentação completa](docs/01-estrutura-projeto.md).

## 🛠️ Stack Tecnológica

-   **PHP:** 8.4 (FPM)
-   **Servidor Web:** Nginx 1.25
-   **Banco de Dados:** MySQL 8.4
-   **Ferramentas:** Laravel Telescope (Debug), Composer 2

---

## 🏗️ Estrutura do Docker

O projeto utiliza **Multi-stage Build**, separando o ambiente em dois estágios:

1.  **Stage `dev`**: Inclui o binário do Composer e ferramentas de auxílio ao desenvolvimento.
2.  **Stage `prod`**: Imagem otimizada e segura, sem o Composer, pronta para o cluster Kubernetes.

---

## 🚀 Como Iniciar (Quick Start)

Este guia vai te ajudar a executar a API localmente em poucos minutos. O projeto está configurado para funcionar "out of the box" com Docker.

### 📋 Pré-requisitos

-   Docker e Docker Compose instalados
-   Git instalado
-   Portas 8080, 8090 e 3306 disponíveis

### 🔧 Passo a Passo

#### 1. Clonar o projeto

```bash
# HTTPS
git clone https://github.com/phenriquerb/projeto-laravel.git

# Ou SSH
git clone git@github.com:phenriquerb/projeto-laravel.git

cd projeto-laravel
```

#### 2. Configurar variáveis de ambiente

```bash
cp .env.example .env
```

#### 3. Subir os containers Docker

```bash
docker compose up -d --build
```

Este comando vai:

-   Construir as imagens PHP, Nginx e MySQL
-   Iniciar todos os serviços (php-fpm, nginx, mysql, queue-worker, pulse-worker, reverb)
-   Aguarde alguns minutos na primeira execução

#### 4. Instalar dependências do PHP

```bash
docker compose exec php-fpm composer install
```

#### 5. Gerar chave de aplicação

```bash
docker compose exec php-fpm php artisan key:generate
```

#### 6. Executar migrations

```bash
docker compose exec php-fpm php artisan migrate
```

#### 7. Popular o banco de dados com dados de teste

```bash
docker compose exec php-fpm php artisan db:seed
```

Este comando cria:

-   **Cargos**: Atendente e Técnico
-   **Funcionários**: 8 funcionários (4 atendentes e 4 técnicos)
-   **Clientes**: 4 clientes de exemplo
-   **Equipamentos**: 6 equipamentos associados aos clientes

#### 8. Verificar se tudo está funcionando

```bash
# Verificar status dos containers
docker compose ps

# Verificar logs (se necessário)
docker compose logs php-fpm
```

---

## 🌐 Acessando a Aplicação

### 📚 **Scramble - Documentação Interativa da API** ⭐

**URL:** http://localhost:8080/docs/api

> **🎯 Esta é a ferramenta principal para testar a API!** O Scramble fornece uma interface interativa onde você pode:
>
> -   Ver todos os endpoints disponíveis
> -   Testar requisições diretamente no navegador
> -   Ver exemplos de requisições e respostas
> -   Autenticar e fazer chamadas reais à API

**Como usar:**

1. Acesse http://localhost:8080/docs/api
2. Faça login usando as credenciais abaixo
3. Explore e teste todos os endpoints disponíveis

### 🔍 Telescope - Debug e Monitoramento

**URL:** http://localhost:8080/telescope

Ferramenta de debug do Laravel que permite visualizar:

-   Requisições HTTP
-   Queries SQL
-   Logs
-   Jobs em fila
-   Exceções
-   E muito mais

**Acesso:** Público (configurado para portfólio)

### 📊 Pulse - Monitoramento em Tempo Real

**URL:** http://localhost:8080/pulse

Dashboard de monitoramento que mostra:

-   Métricas de performance
-   Requisições lentas
-   Erros em tempo real
-   Receita gerada
-   OS concluídas no dia

**Acesso:** Público (configurado para portfólio)

### 🔌 API REST

**Base URL:** http://localhost:8080/api

---

## 🔑 Credenciais de Teste

Após executar o seeder, você pode usar as seguintes credenciais para testar a API:

### Atendentes

```
Login: joao.silva
Senha: password

Login: ana.costa
Senha: password

Login: carlos.pereira
Senha: password

Login: fernanda.souza
Senha: password
```

### Técnicos

```
Login: maria.santos
Senha: password

Login: pedro.oliveira
Senha: password

Login: juliana.alves
Senha: password

Login: roberto.lima
Senha: password
```

### Exemplo de Login via API

```bash
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "joao.silva",
    "password": "password"
  }'
```

---

## 🛠️ Comandos Úteis

### Gerenciar containers

```bash
# Parar todos os containers
docker compose down

# Parar e remover volumes (limpar banco)
docker compose down -v

# Reiniciar containers
docker compose restart

# Ver logs
docker compose logs -f php-fpm
```

### Comandos Artisan

```bash
# Executar qualquer comando artisan
docker compose exec php-fpm php artisan [comando]

# Exemplos:
docker compose exec php-fpm php artisan route:list
docker compose exec php-fpm php artisan tinker
docker compose exec php-fpm php artisan migrate:fresh --seed
```

### Limpar cache

```bash
docker compose exec php-fpm php artisan cache:clear
docker compose exec php-fpm php artisan config:clear
docker compose exec php-fpm php artisan route:clear
docker compose exec php-fpm php artisan view:clear
```

---

## 📝 Dados de Teste Criados pelo Seeder

Após executar `php artisan db:seed`, você terá:

-   **4 Clientes** (IDs: 1-4)
-   **6 Equipamentos** distribuídos entre os clientes
-   **8 Funcionários** (4 atendentes + 4 técnicos)
-   **2 Cargos** (Atendente e Técnico)

Todos os dados têm IDs fixos para facilitar testes e relacionamentos previsíveis.

### Porta já em uso

Se as portas 8080, 8090 ou 3306 estiverem em uso, você pode alterá-las no arquivo `docker-compose.yml`.

---

## 🚦 CI/CD e Quality Gate

O projeto possui um **pipeline CI/CD** configurado no GitHub Actions que executa automaticamente em cada push para as branches `main` e `develop`.

### Quality Gate (Portão de Qualidade)

O pipeline implementa um **Quality Gate baseado em testes automatizados** que impede deploys instáveis. Antes de qualquer build ou deploy, o pipeline executa:

-   ✅ **Testes Unitários**: Validação da lógica de negócio e regras customizadas
-   ✅ **Testes de Integração**: Validação dos endpoints e fluxos completos da API
-   ✅ **Lint (Pint)**: Verificação de formatação e padrões de código

**Apenas builds que passam em todos os testes são aprovados para deploy**, garantindo qualidade e estabilidade do código em produção.

Para mais detalhes, consulte o arquivo `.github/workflows/main.yml`.
