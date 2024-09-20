<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Método para verificar tipo de usuário
    private function checkUserType(User $user, int $typeId)
    {
        if ($user->type_id !== $typeId) {
            $type = $typeId == 1 ? 'Aluno' : 'Professor';
            return response()->json(['error' => "Usuário não é $type"], 403);
        }
        return null;
    }

    // Listar alunos ou professores
    public function index(Request $request)
    {
        $typeId = $request->route()->getName() === 'alunos.index' ? 1 : 2;
        $instituicaoId = $request->input('authenticated_user_id');

        $usuarios = User::where('instituicao_id', $instituicaoId)
                        ->where('type_id', $typeId)
                        ->get();

        if ($usuarios->isEmpty()) {
            return response()->json(['error' => 'Nenhum dado encontrado'], 404);
        }

        return response()->json($usuarios);
    }

    // Exibir detalhes de um aluno ou professor
    public function show(Request $request, $id)
    {
        $instituicaoId = $request->input('authenticated_user_id');
        $user = User::where('instituicao_id', $instituicaoId)
                    ->where('id', $id)
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        $typeId = $request->route()->getName() === 'alunos.show' ? 1 : 2;
        $errorResponse = $this->checkUserType($user, $typeId);

        if ($errorResponse) {
            return $errorResponse;
        }

        return response()->json($user);
    }

    // Atualizar aluno ou professor
    public function update(Request $request, $id)
    {
        $instituicaoId = $request->input('authenticated_user_id');
        $user = User::where('instituicao_id', $instituicaoId)
                    ->where('id', $id)
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        $typeId = $request->route()->getName() === 'alunos.update' ? 1 : 2;
        $errorResponse = $this->checkUserType($user, $typeId);

        if ($errorResponse) {
            return $errorResponse;
        }

        $user->update($request->all());

        return response()->json($user);
    }

    // Remover aluno ou professor
    public function destroy(Request $request, $id)
    {
        $instituicaoId = $request->input('authenticated_user_id');
        $user = User::where('instituicao_id', $instituicaoId)
                    ->where('id', $id)
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        $typeId = $request->route()->getName() === 'alunos.destroy' ? 1 : 2;

       $errorResponse = $this->checkUserType($user, $typeId);

        if ($errorResponse) {
            return $errorResponse;
        }

        $user->delete();

        return response()->json(['msg' => 'Usuário removido com sucesso!'], 200);
        
    }
}
