# Tratamento de Exceções

Este documento descreve como as exceções são tratadas no projeto.

## Padrão de Resposta de Erro

Todas as exceções retornam no formato padrão:

```json
{
  "message": "Mensagem de erro"
}
```

## Configuração Global

O tratamento de exceções é configurado em `bootstrap/app.php`:

```php
->withExceptions(function (Exceptions $exceptions): void {
    // Tratamento de erros de validação
    $exceptions->render(function (ValidationException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        }
    });

    // Tratamento de erros genéricos
    $exceptions->render(function (Exception $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Erro interno do servidor',
            ], 500);
        }
    });
})
```

## Tipos de Exceções

### Exceções de Validação

Quando ocorre um erro de validação (FormRequest), a resposta inclui os erros específicos:

```json
{
  "message": "Erro de validação",
  "errors": {
    "campo": [
      "Mensagem de erro do campo"
    ]
  }
}
```

**Status Code:** 422

### Exceções Genéricas

Para outras exceções, apenas a mensagem é retornada:

```json
{
  "message": "Mensagem de erro descritiva"
}
```

**Status Code:** 500 (ou outro código apropriado)

## Exceções Customizadas do Domínio

### AppException

Exceção base customizada localizada em `app/Domain/Exceptions/AppException.php`:

```php
namespace App\Domain\Exceptions;

use Exception;

class AppException extends Exception
{
    //
}
```

### Uso

Você pode criar exceções específicas estendendo `AppException`:

```php
namespace App\Domain\Exceptions;

class FuncionarioNaoEncontradoException extends AppException
{
    public function __construct()
    {
        parent::__construct('Funcionário não encontrado.');
    }
}
```

## Tratamento no Controller

Os controllers podem tratar exceções específicas:

```php
public function show(int $id)
{
    try {
        $funcionario = $this->funcionarioService->buscarPorId($id);
        return new FuncionarioResource($funcionario);
    } catch (FuncionarioNaoEncontradoException $e) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 500);
    }
}
```

## Boas Práticas

1. **Mensagens Descritivas**: Sempre forneça mensagens de erro claras e descritivas
2. **Status Codes Apropriados**: Use os códigos HTTP corretos (404, 422, 500, etc.)
3. **Logs**: Exceções críticas devem ser logadas para análise posterior
4. **Segurança**: Não exponha informações sensíveis nas mensagens de erro em produção
