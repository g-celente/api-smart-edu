<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComentariosTarefa;
use App\Models\Tarefa;
use Exception;

class ComentariosTarefaController extends Controller
{
    public function getComentarioById (Request $request, $tarefa_id) {

        $user_id = $request->input('authenticated_user_id');

        $tarefas = ComentariosTarefa::where('tarefa_id', $tarefa_id)->where('user_id', $user_id)->get();

        if ($tarefas->isEmpty()) {
            return response()->json([
                'error' => "nenhum comentário para a tarefa $tarefa_id ou para o usuário $user_id"
            ], 404);
        }

        return response()->json($tarefas);

    }

    public function createComentario (Request $request) {

        $user_id = $request->input('authenticated_user_id');

        $credentials = $request->validate([
            'comentario ' => 'required',
            'tarefa_id' => 'required'
        ]);

        $tarefa = Tarefa::where('id', $request->tarefa_id)->first();

        if (!$tarefa) {
            return response()->json([
                'error' => "tarefa com id $request->tarefa_id não encontrada no banco"
            ], 404);
        }

        try {

            $comentario = ComentariosTarefa::create([
                'comentario' => $request->comentario,
                'tarefa_id' => $request->tarefa_id,
                'user_id' => $user_id
            ]);

            return response()->json([
                'sucess' => 'comentário cadastrado'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'erro ao realizar comentário',
                'erro' => $e->getMessage()
            ], 500);
        }
        
    }
}
