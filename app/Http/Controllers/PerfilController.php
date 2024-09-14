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

}
