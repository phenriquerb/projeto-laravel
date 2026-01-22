<?php

namespace Tests\Unit;

use App\Enums\CargoEnum;
use App\Http\Requests\ConcluirOrdemServicoRequest;
use App\Models\Cargo;
use App\Models\Cliente;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\OrdemServico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ConcluirOrdemServicoRequestTest extends TestCase
{
    use RefreshDatabase;

    protected Funcionario $tecnico;

    protected OrdemServico $os;

    protected function setUp(): void
    {
        parent::setUp();

        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Técnico']);
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);

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

        $this->os->responsaveis()->attach($this->tecnico->id);
    }

    public function test_deve_falhar_quando_diagnostico_tecnico_tem_menos_de_50_caracteres()
    {
        $this->os->update(['diagnostico_tecnico' => 'Diagnóstico curto']);

        $request = new ConcluirOrdemServicoRequest;
        $request->setContainer(app());
        $request->setUserResolver(fn () => $this->tecnico);
        $request->setRouteResolver(fn () => new class($this->os)
        {
            public function __construct(private $os) {}

            public function parameter($name, $default = null)
            {
                return $name === 'ordemServico' ? $this->os : $default;
            }
        });

        $validator = Validator::make([], $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('diagnostico_tecnico'));
    }

    public function test_deve_falhar_quando_os_ja_esta_concluida()
    {
        $this->os->update(['status' => 'concluida']);

        $request = new ConcluirOrdemServicoRequest;
        $request->setContainer(app());
        $request->setUserResolver(fn () => $this->tecnico);
        $request->setRouteResolver(fn () => new class($this->os)
        {
            public function __construct(private $os) {}

            public function parameter($name, $default = null)
            {
                return $name === 'ordemServico' ? $this->os : $default;
            }
        });

        $validator = Validator::make([], $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('status'));
    }

    public function test_deve_falhar_quando_os_esta_cancelada()
    {
        $this->os->update(['status' => 'cancelada']);

        $request = new ConcluirOrdemServicoRequest;
        $request->setContainer(app());
        $request->setUserResolver(fn () => $this->tecnico);
        $request->setRouteResolver(fn () => new class($this->os)
        {
            public function __construct(private $os) {}

            public function parameter($name, $default = null)
            {
                return $name === 'ordemServico' ? $this->os : $default;
            }
        });

        $validator = Validator::make([], $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('status'));
    }

    public function test_deve_falhar_quando_usuario_nao_e_tecnico_responsavel()
    {
        $outroTecnico = Funcionario::create([
            'nome' => 'Outro Técnico',
            'email' => 'outro@test.com',
            'login' => 'outro.tecnico',
            'password' => 'password',
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        $request = new ConcluirOrdemServicoRequest;
        $request->setContainer(app());
        $request->setUserResolver(fn () => $outroTecnico);
        $request->setRouteResolver(fn () => new class($this->os)
        {
            public function __construct(private $os) {}

            public function parameter($name, $default = null)
            {
                return $name === 'ordemServico' ? $this->os : $default;
            }
        });

        $validator = Validator::make([], $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tecnico'));
    }
}
