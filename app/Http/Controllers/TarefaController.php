<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Id = $request->input('authenticated_user_id');

        $tarefas = Tarefa::where('professor_id', $Id);

        return response()->json($tarefas);
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
        $Id = $request->input('authenticated_user_id');

        $credentials = $request->validate([
            'nome' => 'required',
            'descricao' => 'required', 
            'disciplina_id' => 'required'
            
        ]);

        $tarefa = Tarefa::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'professor_id' => $Id,
            'disciplina_id' => $request->disciplina_id
        ]);

        return response()->json([
            'sucess' => 'tarefa cadastrada',
            'tarefa' => $tarefa,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function show(Tarefa $tarefa, Request $request)
    {
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::where('professor_id', $id)->where('id', $tarefa)->get();

        if ($tarefa) {
            return response()->json($tarefa);
        }

        return response()->json([
            'error' => 'tarefa não encontrada'
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarefa $tarefa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarefa $tarefa)
    {
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::where('professor_id', $id)->where('id', $tarefa)->get();

        if ($tarefa) {
            $tarefa->update($request->all());
            return response()->json([
                'sucess' => 'tarefa atualizada',
                'tarefa' => $tarefa
            ]);
        }

        return response()->json([
            'error' => 'tarefa não encontrada'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarefa $tarefa, Request $request)
    {
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::where('professor_id', $id)->where('id', $tarefa)->get();

        if ($tarefa) {
            $tarefa->delete();
            return response()->json([
                'success' => 'tarefa deletada'
            ]);
        }

        return response()->json([
            'error' => 'tarefa não encontrada'
        ]);
        
    }
}
