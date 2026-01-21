<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\FuncionarioResource;
use App\Models\Funcionario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Autentica um funcionário e retorna um token
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $dados = $request->validated();

        // Buscar funcionário pelo login
        $funcionario = Funcionario::where('login', $dados['login'])->first();

        // Verificar se funcionário existe e senha está correta
        if (! $funcionario || ! Hash::check($dados['password'], $funcionario->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas.',
            ], 401);
        }

        // Gerar token
        $token = $funcionario->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'token' => $token,
            'funcionario' => new FuncionarioResource($funcionario->load('cargo')),
        ], 200);
    }

    /**
     * Revoga o token atual do funcionário autenticado
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        // Revogar token atual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ], 200);
    }

    /**
     * Retorna os dados do funcionário autenticado
     *
     * @return FuncionarioResource
     */
    public function me(Request $request)
    {
        $funcionario = $request->user()->load('cargo');

        return new FuncionarioResource($funcionario);
    }
}
