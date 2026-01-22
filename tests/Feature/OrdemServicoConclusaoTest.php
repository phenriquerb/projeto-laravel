<?php

namespace Tests\Feature;

use App\Enums\CargoEnum;
use App\Models\Cargo;
use App\Models\Cliente;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\OrdemServico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrdemServicoConclusaoTest extends TestCase
{
    use RefreshDatabase;

    protected Funcionario $tecnico;

    protected OrdemServico $os;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar cargos necessários
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Técnico']);
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);

        // Criar funcionário técnico
        $this->tecnico = Funcionario::create([
            'nome' => 'Técnico Teste',
            'email' => 'tecnico@test.com',
            'login' => 'tecnico.teste',
            'password' => 'password',
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        $atendente = Funcionario::create([
            'nome' => 'Atendente Teste',
            'email' => 'atendente@test.com',
            'login' => 'atendente.teste',
            'password' => 'password',
            'cargo_id' => CargoEnum::ATENDENTE->value,
        ]);

        // Criar cliente e equipamento
        $cliente = Cliente::create([
            'nome' => 'Cliente Teste',
            'email' => 'cliente@test.com',
            'cpf_cnpj' => '12345678900',
            'whatsapp' => '11999999999',
        ]);

        $equipamento = Equipamento::create([
            'cliente_id' => $cliente->id,
            'tipo' => 'Notebook',
            'marca' => 'Dell',
            'modelo' => 'Inspiron',
        ]);

        // Criar OS
        $this->os = OrdemServico::create([
            'protocolo' => '202601-001',
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'relato_cliente' => 'Equipamento não liga',
            'diagnostico_tecnico' => 'Após análise detalhada, identificamos problema na fonte de alimentação que necessita substituição completa',
            'status' => 'execucao',
            'prioridade' => 'media',
            'valor_total' => 150.00,
        ]);

        // Atribuir técnico à OS
        $this->os->responsaveis()->attach($this->tecnico->id);
    }

    public function test_deve_concluir_os_com_sucesso_quando_laudo_tem_mais_de_50_caracteres()
    {
        Queue::fake();
        Sanctum::actingAs($this->tecnico, ['*']);

        $response = $this->patchJson("/api/os/{$this->os->id}/concluir");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Ordem de serviço concluída com sucesso. Email enviado ao cliente.',
            ]);

        $this->os->refresh();
        $this->assertEquals('concluida', $this->os->status);
        $this->assertNotNull($this->os->data_conclusao);
    }

    public function test_nao_deve_concluir_os_quando_laudo_tem_menos_de_50_caracteres()
    {
        Sanctum::actingAs($this->tecnico, ['*']);

        $this->os->update(['diagnostico_tecnico' => 'Diagnóstico muito curto']);

        $response = $this->patchJson("/api/os/{$this->os->id}/concluir");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['diagnostico_tecnico']);
    }

    public function test_nao_deve_concluir_os_ja_concluida()
    {
        Sanctum::actingAs($this->tecnico, ['*']);

        $this->os->update(['status' => 'concluida']);

        $response = $this->patchJson("/api/os/{$this->os->id}/concluir");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_nao_deve_concluir_os_cancelada()
    {
        Sanctum::actingAs($this->tecnico, ['*']);

        $this->os->update(['status' => 'cancelada']);

        $response = $this->patchJson("/api/os/{$this->os->id}/concluir");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_apenas_tecnico_responsavel_pode_concluir_os()
    {
        $outroTecnico = Funcionario::create([
            'nome' => 'Outro Técnico',
            'email' => 'outro@test.com',
            'login' => 'outro.tecnico',
            'password' => 'password',
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        Sanctum::actingAs($outroTecnico, ['*']);

        $response = $this->patchJson("/api/os/{$this->os->id}/concluir");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tecnico']);
    }

    public function test_deve_exportar_pdf_com_sucesso()
    {
        Sanctum::actingAs($this->tecnico, ['*']);

        $response = $this->get("/api/os/{$this->os->id}/exportar-pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_nao_deve_exportar_pdf_sem_autenticacao()
    {
        $response = $this->get("/api/os/{$this->os->id}/exportar-pdf");

        $response->assertStatus(401);
    }
}
