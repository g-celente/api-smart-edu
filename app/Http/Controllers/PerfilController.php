<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Instituicoe;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function get_perfil(Request $request)
    {
        // Obter o type_id do request
        $Id = $request->input('authenticated_user_id');
        $typeId = $request->input('authenticated_user_type_id');
        

        if ($typeId == 3) {
            $instituicao = User::where('id', $Id)->first();
            return response()->json([
                'nome' => $instituicao->nome,
                'email' => $instituicao->email,
                'type_id' => $instituicao->type_id,
                'user_img' => $instituicao->user_img
            ]);
        }

        // Buscar usuários com base no type_id
        $usuarios = User::where('id', $Id)->first();

        return response()->json([
            'id' => $usuarios->id,
            'nome' => $usuarios->nome,
            'email' => $usuarios->email,
            'instituicao_id' => $usuarios->instituicao_id,
            'type_id' => $usuarios->type_id
        ]);
    }

    public function update_perfil(Request $request) {
        $user_id = $request->input('authenticated_user_id');
        
        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return response()->json([
                'error' => 'user não encontrado'
            ], 404);
        }

        $user->update([
            'nome' => $request->nome,
            'email' => $request->email,
            'user_img' => $request->user_img
        ]);
        return response()->json([
            'success' => 'usuário alterado',
            'user' => $user
        ], 200);
    }

    public function update_password(Request $request) {
        $user_id = $request->input('authenticated_user_id');

        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return response()->json([
                'error' => 'usuário não encontrado no banco'
            ], 404);
        }

        if (Hash::check($request->senha_atual, $user->senha)) {
            try{
                $user->update([
                    'senha' => Hash::make($request->nova_senha)
                ]);
    
                return response()->json([
                    'success' => 'senha alterada',
                    'user' => $user
                ], 200);
            }
            catch (\Exception $e) {
                return response()->json([
                    'error' => 'erro ao tentar alterar a senha',
                    'problema' => $e
                ], 404);
            }
        }

        return response()->json([
            'error' => 'senha atual não é compatível',
        ], 404);
        
    }

    public function update_img(Request $request) {
        $user_id = $request->input('authenticated_user_id');

        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return response()->json([
                'error' => 'usuário não encontrado'
            ], 404);
        }

        $img = $user->update([
            'user_img' => $request->user_img
        ]);

        return response()->json([
            'success' => 'imagem adicionada com sucesso'
        ], 200);
    }


}
