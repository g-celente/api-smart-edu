<?php

namespace App\Http\Controllers;

use App\Models\ComentariosTarefa;
use App\Models\User;
use App\Models\Tarefa;
use Illuminate\Http\Request;
use FFI\Exception;

class ComentariosTarefaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = $request->input('authenticated_user_id');

        $credentials = $request->validate([
            'comentario' => 'required',
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ComentariosTarefa  $comentariosTarefa
     * @return \Illuminate\Http\Response
     */
    public function show(ComentariosTarefa $comentariosTarefa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ComentariosTarefa  $comentariosTarefa
     * @return \Illuminate\Http\Response
     */
    public function edit(ComentariosTarefa $comentariosTarefa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComentariosTarefa  $comentariosTarefa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComentariosTarefa $comentariosTarefa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComentariosTarefa  $comentariosTarefa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request , $comentario_id)
    {
        $user_id = $request->input('authenticated_user_id');

        $comentario = ComentariosTarefa::where('id', $comentario_id)->where('user_id', $user_id)->first();

        if (!$comentario) {
            return response()->json([
                'error' => "comentário com id $comentario_id não encontrado"            
            ]);
        }

        try {
            $comentario->delete();

            return response()->json([
                'success' => 'comentário deletado'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'error ao deletar comentário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getComentariosPorTarefa(Request $request, $tarefa_id) 
    {
        $tarefas = ComentariosTarefa::where('tarefa_id', $tarefa_id)->get();

        if ($tarefas->isEmpty()) {
            return response()->json([
                'error' => "Nenhum comentário para a tarefa $tarefa_id"
            ], 404);
        }

        // Retornar todos os comentários como uma lista, incluindo dados do autor de cada comentário
        return response()->json([
            'comentarios' => $tarefas->map(function($comentario) {
                $autor = User::find($comentario->user_id); // Buscar o autor do comentário
                
                return [
                    'id' => $comentario->id,
                    'comentario' => $comentario->comentario,
                    'tarefa_id' => $comentario->tarefa_id,
                    'user' => $autor ? [
                        'id' => $autor->id,
                        'user_img' => $autor->user_img,
                        'nome' => $autor->nome,
                        'email' => $autor->email,
                        'type_id' => $autor->type_id
                    ] : null // Retornar null se o autor não for encontrado
                ];
            })
        ]);
    }
}
