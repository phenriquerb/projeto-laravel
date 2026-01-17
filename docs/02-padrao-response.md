# Padrão de Response da API

Este documento descreve o padrão de resposta utilizado em todas as rotas da API.

## Estrutura Padrão

Todas as respostas da API seguem o seguinte formato:

```json
{
  "success": true|false,
  "data": {}
}
```

### Campos

- **success** (boolean): Indica se a requisição foi bem-sucedida
  - `true`: Requisição processada com sucesso
  - `false`: Ocorreu um erro na requisição

- **data** (mixed): Contém os dados da resposta ou informações de erro

## Respostas de Sucesso

### Exemplo de Sucesso

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "João Silva",
      "cargo": {
        "id": 1,
        "nome": "Desenvolvedor"
      }
    }
  ]
}
```

**Status Code:** 200 (ou outro código de sucesso apropriado)

## Respostas de Erro

### Erro de Validação

```json
{
  "success": false,
  "data": {
    "message": "Erro de validação",
    "errors": {
      "nome": [
        "O campo nome é obrigatório."
      ]
    }
  }
}
```

**Status Code:** 422 (Unprocessable Entity)

### Erro Genérico

```json
{
  "success": false,
  "data": {
    "message": "Mensagem de erro descritiva"
  }
}
```

**Status Code:** 400, 500 ou outro código de erro apropriado

## Classes de Response

### `ApiResponse`

Classe base para padronizar as respostas:

```php
ApiResponse::success($data, $statusCode);
ApiResponse::error($data, $statusCode);
```

### Classes Específicas

Cada entidade pode ter sua própria classe de response para formatação específica:

**Exemplo:** `FuncionarioResponse::formatCollection($funcionarios)`

## Uso no Controller

```php
public function index(ListarFuncionariosRequest $request)
{
    try {
        $filtros = $request->getValidatedData();
        $funcionarios = $this->funcionarioService->listar($filtros);
        $data = FuncionarioResponse::formatCollection($funcionarios);

        return ApiResponse::success($data);
    } catch (\Exception $e) {
        return ApiResponse::error([
            'message' => $e->getMessage(),
        ], 500);
    }
}
```

## Tratamento Automático de Exceções

O sistema está configurado para capturar automaticamente exceções e retorná-las no padrão da API através do `bootstrap/app.php`.
