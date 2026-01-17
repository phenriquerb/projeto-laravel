# Repositories e Services

Este documento descreve o padrão de Repositories e Services utilizado no projeto.

## Repositories

### Conceito

Repositories abstraem a lógica de acesso a dados, permitindo que a camada de aplicação não dependa diretamente do ORM ou banco de dados.

### Estrutura

#### Interface (Domain Layer)

As interfaces dos repositories ficam em `app/Domain/Contracts/Repositories/`:

```php
namespace App\Domain\Contracts\Repositories;

interface FuncionarioRepositoryInterface
{
    public function listar(array $filtros = []);
}
```

#### Implementação (Infrastructure Layer)

As implementações concretas ficam em `app/Infrastructure/Repositories/`:

```php
namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\FuncionarioRepositoryInterface;

class FuncionarioRepository implements FuncionarioRepositoryInterface
{
    public function listar(array $filtros = []): Collection
    {
        $query = Funcionario::with('cargo');

        if (isset($filtros['id'])) {
            $query->whereIn('id', $filtros['id']);
        }

        if (isset($filtros['nome'])) {
            $query->where('nome', 'like', '%' . $filtros['nome'] . '%');
        }

        return $query->get();
    }
}
```

### Registro no Service Provider

O bind entre interface e implementação é feito no `RepositoryServiceProvider`:

```php
namespace App\Providers;

use App\Domain\Contracts\Repositories\FuncionarioRepositoryInterface;
use App\Infrastructure\Repositories\FuncionarioRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FuncionarioRepositoryInterface::class,
            FuncionarioRepository::class
        );
    }
}
```

O provider deve ser registrado em `bootstrap/providers.php`.

## Services

### Conceito

Services contêm a lógica de negócio da aplicação e orquestram as chamadas aos repositories.

### Estrutura

Os services ficam em `app/Application/Services/`:

```php
namespace App\Application\Services;

use App\Domain\Contracts\Repositories\FuncionarioRepositoryInterface;

class FuncionarioService
{
    public function __construct(
        private FuncionarioRepositoryInterface $funcionarioRepository
    ) {
    }

    public function listar(array $filtros = []): Collection
    {
        return $this->funcionarioRepository->listar($filtros);
    }
}
```

### Injeção de Dependência

Os services recebem as dependências via construtor, utilizando **Dependency Injection** do Laravel.

## Fluxo de Uso

1. **Controller** chama o **Service**
2. **Service** chama o **Repository Interface**
3. **Laravel** resolve automaticamente a **Implementação Concreta** do Repository
4. **Repository** acessa o **Model** e retorna os dados
5. **Service** retorna os dados para o **Controller**

## Vantagens

1. **Testabilidade**: Fácil criar mocks das interfaces para testes
2. **Flexibilidade**: Pode trocar a implementação (Eloquent, Query Builder, etc.) sem alterar o código que usa
3. **Separação de Responsabilidades**: Cada camada tem sua responsabilidade bem definida
4. **Manutenibilidade**: Código mais organizado e fácil de manter

## Soft Deletes

Todos os repositories devem respeitar os soft deletes nas queries, utilizando apenas registros não deletados.
