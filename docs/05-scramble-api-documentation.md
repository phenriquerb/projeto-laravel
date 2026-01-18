# Scramble - Documentação da API

Este documento descreve o uso do Scramble para documentação interativa da API.

## O que é o Scramble?

O **Scramble** é uma ferramenta de documentação de API para Laravel que gera automaticamente documentação interativa baseada nas rotas, controllers, requests e responses do seu projeto. Ele cria uma interface similar ao Swagger/OpenAPI, mas totalmente integrada ao Laravel.

## Acessando a Documentação

A documentação da API está disponível em:

**URL:** `http://localhost:8080/api/documentation`

Ou em produção:
**URL:** `https://seu-dominio.com/api/documentation`

## Características

- **Documentação Automática**: Gera documentação automaticamente baseada no código
- **Interface Interativa**: Permite testar endpoints diretamente na interface
- **Esquemas de Request/Response**: Mostra exemplos de requisições e respostas
- **Validações**: Documenta regras de validação dos Form Requests
- **Códigos de Status**: Documenta possíveis códigos de resposta HTTP

## Configuração para Acesso Público

Por padrão, o Scramble só funciona em ambiente local. Para permitir acesso público (útil para portfólios), foi configurado no `AppServiceProvider`:

```php
public function boot(): void
{
    // Permitir acesso público ao Scramble para portfólio
    Scramble::openApi()->servers([]);
}
```

Esta configuração remove a restrição de ambiente local, permitindo que qualquer pessoa acesse a documentação.

## Como Funciona

O Scramble analisa:

1. **Rotas**: Define os endpoints disponíveis
2. **Controllers**: Extrai informações dos métodos
3. **Form Requests**: Documenta validações e regras
4. **Responses**: Gera exemplos de respostas baseados nos tipos de retorno
5. **Models**: Documenta estruturas de dados quando usadas em responses

## Exemplo de Uso

Ao acessar a documentação, você verá:

- Lista de todos os endpoints da API
- Métodos HTTP suportados (GET, POST, PUT, DELETE, etc.)
- Parâmetros de query, path e body
- Exemplos de requisições e respostas
- Códigos de status possíveis
- Botão "Try it out" para testar endpoints diretamente

## Personalização

Para personalizar a documentação, você pode:

1. Adicionar comentários PHPDoc nos controllers
2. Usar atributos do Scramble para adicionar descrições
3. Editar o arquivo `config/scramble.php` (publicado via `php artisan vendor:publish --tag=scramble-config`)
4. Configurar informações da API (título, versão, descrição, etc.)

## Benefícios para Portfólio

- **Demonstração Visual**: Mostra a qualidade da API de forma visual
- **Fácil Teste**: Visitantes podem testar os endpoints diretamente
- **Documentação Profissional**: Interface moderna e intuitiva
- **Acesso Público**: Configurado para ser acessível sem autenticação
