<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Instituicoe;

class PerfilController extends Controller
{
    public function get_perfil(Request $request)
    {
        // Obter o type_id do request
        $Id = $request->input('authenticated_user_id');
        $typeId = $request->input('authenticated_user_type_id');
        

        if ($typeId == 3) {
            $instituicao = User::where('id', $Id)->get();
            return response()->json($instituicao);
        }

        // Buscar usuários com base no type_id
        $usuarios = User::where('id', $Id)->get();

        return response()->json($usuarios);
    }

    /*
    public function update(Request $request, $id)
    {
        $authenticatedUser = $request->input('authenticated_user');

        if ($authenticatedUser->type_id == 1 || $authenticatedUser->type_id == 2) {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado'], 404);
            }

            $user->update($request->all());

            return response()->json($user);
        }

        return response()->json(['error' => 'Acesso negado'], 403);
    }

    public function destroy(Request $request, $id)
    {
        $authenticatedUser = $request->input('authenticated_user');

        if ($authenticatedUser->type_id == 1 || $authenticatedUser->type_id == 2) {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado'], 404);
            }

            $user->delete();

            return response()->json(['msg' => 'Usuário removido com sucesso!']);
        }

        return response()->json(['error' => 'Acesso negado'], 403);
    }
    */
}
