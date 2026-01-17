# Estrutura do Projeto

Este documento descreve a arquitetura e organização de pastas do projeto.

## Arquitetura

O projeto utiliza uma **arquitetura em camadas (Layered Architecture)** com separação de responsabilidades, implementando **Repository Pattern** e **Service Layer** para organizar o código e reduzir o acoplamento, sem isolar completamente o framework.

## Estrutura de Pastas

### `app/Application/Services`
Contém os **Services** da aplicação. Os services contêm a lógica de negócio e orquestram as chamadas aos repositories.

**Exemplo:**
- `FuncionarioService.php` - Service para gerenciar funcionários

### `app/Domain`
Camada de domínio que contém interfaces, contratos e exceções do domínio.

#### `app/Domain/Contracts/Repositories`
Contém as **interfaces** dos repositories. Define os contratos que devem ser implementados pela camada de infraestrutura.

**Exemplo:**
- `FuncionarioRepositoryInterface.php` - Interface do repository de funcionários

#### `app/Domain/Enums`
Contém os **Enums** do domínio.

#### `app/Domain/Exceptions`
Contém as **exceções customizadas** do domínio.

**Exemplo:**
- `AppException.php` - Exception base customizada

### `app/Infrastructure/Repositories`
Contém as **implementações concretas** dos repositories usando Eloquent ORM.

**Exemplo:**
- `FuncionarioRepository.php` - Implementação do repository de funcionários

### `app/Http`
Camada HTTP que lida com requisições e respostas.

#### `app/Http/Controllers/Api`
Controllers da API REST.

#### `app/Http/Requests`
Classes de validação de requisições (Form Requests).

#### `app/Http/Responses`
Classes para formatação de respostas da API.

### `app/Providers`
Service Providers do Laravel.

**Exemplo:**
- `RepositoryServiceProvider.php` - Provider para fazer o bind das interfaces com as implementações

## Fluxo de Dados

```
Request → FormRequest (validação) 
  → Controller 
    → Service (lógica de negócio)
      → Repository Interface (contrato)
        → Repository Eloquent (implementação)
          → Model
            → Response (formatação)
```

## Princípios

1. **Separação de Responsabilidades**: Cada camada tem uma responsabilidade específica
2. **Inversão de Dependência**: Dependemos de abstrações (interfaces), não de implementações concretas
3. **Repository Pattern**: Abstração da camada de acesso a dados
4. **Service Layer**: Centralização da lógica de negócio
5. **Testabilidade**: A arquitetura facilita a criação de testes unitários e de integração
6. **Manutenibilidade**: Código organizado e fácil de manter

## Padrões Utilizados

- **Repository Pattern**: Abstrai o acesso a dados através de interfaces
- **Service Layer Pattern**: Centraliza a lógica de negócio em services
- **Dependency Injection**: Injeção de dependências via construtor
- **Form Request Validation**: Validação de requisições HTTP
