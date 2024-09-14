<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->input('authenticated_user_id');
        $type_id = $request->input('authenticated_user_type_id');

        if ($type_id == 1) {
            $notas = Nota::where('aluno_id', $id);

            if (!$notas) {
                return response()->json([
                    'error' => 'nenhuma nota encontrada para este aluno'
                ], 404);
            }

            return response()->json($notas, 200);
        }

        return response()->json([
            'error' => 'nenhuma nota cadastrada para este usuário'
        ], 404);
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
        $credencials = $request->validate([
            'nota' => 'required|integer',
            'aluno_id' => 'required|integer',
            'tarefa_id' => 'required|integer'
        ]);

        $aluno = User::where('id', $request->aluno_id)->first();
        $tarefa = Tarefa::where('id', $request->tarefa_id)->first();

        if (!$aluno || !$tarefa) {
            return response()->json([
                'error' => 'aluno ou tarefa não encontrados'
            ],404);
        }

        $create = Nota::create([
            'nota' => $request->nota,
            'aluno_id' => $request->aluno_id,
            'tarefa_id' => $request->tarefa_id
        ]);

        return response()->json([
            'success' => 'nota cadastrada',
            'nota' => $create,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function show(Nota $nota)
    {
        $nota = Nota::find($nota);

        if (!$nota) {
            return response()->json([
                'error' => 'nenhuma nota encontrada'
            ], 404);
        }

        return response()->json($nota, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function edit(Nota $nota)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Nota $nota)
    {
        $nota = Nota::find($nota);

        if (!$nota) {
            return response()->json([
                'error' => 'nenhuma nota encontrada'
            ], 404);
        }

        $nota->update([
            'nota' => $request->nota,
            'aluno_id' => $request->aluno_id,
            'tarefa_id' => $request->tarefa_id
        ]);

        return response()->json([
            'success' => 'nota alterada',
            'nota' => $nota
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nota $nota)
    {
        $nota = Nota::find($nota);

        if (!$nota) {
            return response()->json([
                'error' => 'nenhuma nota encontrada'
            ], 404);
        }

        $nota->delete();

        return response()->json([
            'success' => 'nota deletada com sucesso'
        ], 200);
    }
}
