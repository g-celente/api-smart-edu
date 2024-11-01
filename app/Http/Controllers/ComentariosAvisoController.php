<?php

namespace App\Http\Controllers;

use App\Models\ComentariosAviso;
use App\Models\User;
use App\Models\Aviso;
use Illuminate\Http\Request;

class ComentariosAvisoController extends Controller
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
            'aviso_id' => 'required'
        ]);

        $aviso = Aviso::where('id', $request->aviso_id)->first();

        if (!$aviso) {
            return response()->json([
                'error' => "tarefa com id $request->aviso_id não encontrada no banco"
            ], 404);
        }

        try {

            $comentario = ComentariosAviso::create([
                'comentario' => $request->comentario,
                'aviso_id' => $request->aviso_id,
                'user_id' => $user_id
            ]);

            return response()->json([
                'sucess' => 'comentário cadastrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'erro ao realizar comentário',
                'erro' => $e->getMessage()
            ], 500);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ComentariosAviso  $comentariosAviso
     * @return \Illuminate\Http\Response
     */
    public function show(ComentariosAviso $comentariosAviso)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ComentariosAviso  $comentariosAviso
     * @return \Illuminate\Http\Response
     */
    public function edit(ComentariosAviso $comentariosAviso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComentariosAviso  $comentariosAviso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComentariosAviso $comentariosAviso)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComentariosAviso  $comentariosAviso
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $aviso_id)
    {
        $user_id = $request->input('authenticated_user_id');

        $comentario = ComentariosAviso::where('id', $aviso_id)->where('user_id', $user_id)->first();

        if (!$comentario) {
            return response()->json([
                'error' => "comentário com id $aviso_id não encontrado"            
            ]);
        }

        try {
            $comentario->delete();

            return response()->json([
                'success' => 'comentário deletado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'error ao deletar comentário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getComentariosPorAviso(Request $request, $aviso_id) 
    {
        $tarefas = ComentariosAviso::where('aviso_id', $aviso_id)->get();

        if ($tarefas->isEmpty()) {
            return response()->json([
                'error' => "Nenhum comentário para a disciplina $aviso_id"
            ], 404);
        }

        // Retornar todos os comentários como uma lista, incluindo dados do autor de cada comentário
        return response()->json([
            'comentarios' => $tarefas->map(function($comentario) {
                $autor = User::find($comentario->user_id); // Buscar o autor do comentário
                
                return [
                    'id' => $comentario->id,
                    'comentario' => $comentario->comentario,
                    'disciplina' => $comentario->aviso_id,
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
