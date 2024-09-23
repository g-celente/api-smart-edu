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
                'type_id' => $instituicao->type_id
            ]);
        }

        // Buscar usuários com base no type_id
        $usuarios = User::where('id', $Id)->first();

        return response()->json([
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
            'senha' => Hash::make($request->email)
        ]);
        return response()->json([
            'success' => 'usuário alterado',
            'user' => $user
        ], 200);
    }
    /*
    public function update_password(Request $request) {
        $user_id = $request->input('authenticated_user_id');

        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return response()->json([
                'error' => 'usuário não encontrado no banco'
            ], 404);
        }
        
        try{
            $user->update([
                'senha' => Hash::make($request->senha)
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
            ]);
        }
        
    }
    */


}
