# Padrão de Response da API

Este documento descreve o padrão de resposta utilizado em todas as rotas da API.

## Estrutura de Resposta

O projeto utiliza **JsonResource** do Laravel para formatar todas as respostas da API. Cada endpoint retorna diretamente os dados formatados pelo Resource, sem wrappers adicionais.

## Respostas de Sucesso

### Exemplo de Sucesso (Collection)

```json
[
  {
    "id": 1,
    "nome": "João Silva",
    "email": "joao.silva@example.com",
    "ativo": true,
    "cargo": {
      "id": 1,
      "nome": "Desenvolvedor"
    }
  }
]
```

**Status Code:** 200 (ou outro código de sucesso apropriado)

### Exemplo de Sucesso (Item único)

```json
{
  "id": 1,
  "nome": "João Silva",
  "email": "joao.silva@example.com",
  "ativo": true,
  "cargo": {
    "id": 1,
    "nome": "Desenvolvedor"
  }
}
```

**Status Code:** 200 (ou outro código de sucesso apropriado)

## Respostas de Erro

### Erro de Validação

```json
{
  "message": "Erro de validação",
  "errors": {
    "nome": [
      "O campo nome é obrigatório."
    ]
  }
}
```

**Status Code:** 422 (Unprocessable Entity)

### Erro Genérico

```json
{
  "message": "Mensagem de erro descritiva"
}
```

**Status Code:** 400, 500 ou outro código de erro apropriado

## Resources (JsonResource)

O projeto utiliza **JsonResource** do Laravel para formatar as respostas. Cada entidade possui seu próprio Resource:

- `FuncionarioResource` - Formata dados de funcionários
- `CargoResource` - Formata dados de cargos

**Exemplo de Resource:**

```php
class FuncionarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'ativo' => (bool) $this->ativo,
            'cargo' => $this->whenLoaded('cargo', fn () => new CargoResource($this->cargo)),
        ];
    }
}
```

## Uso no Controller

### Retornando uma Collection

```php
public function index(ListarFuncionariosRequest $request)
{
    $filtros = $request->getValidatedData();
    $funcionarios = $this->funcionarioService->listar($filtros);

    return FuncionarioResource::collection($funcionarios);
}
```

### Retornando um Item Único

```php
public function show(int $id)
{
    $funcionario = $this->funcionarioService->buscarPorId($id);

    return new FuncionarioResource($funcionario);
}
```

## Benefícios dos Resources

- **Documentação Automática**: O Scramble detecta automaticamente a estrutura de retorno através do método `toArray()`
- **Type Safety**: Garante tipos corretos nos dados retornados
- **Flexibilidade**: Permite transformar dados antes de retornar
- **Padrão Laravel**: Utiliza recursos nativos do framework
- **Simplicidade**: Respostas diretas sem wrappers desnecessários

## Tratamento Automático de Exceções

O sistema está configurado para capturar automaticamente exceções e retorná-las no formato padrão através do `bootstrap/app.php`.
