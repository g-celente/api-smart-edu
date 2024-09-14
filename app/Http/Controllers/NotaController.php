<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Disciplina;

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
        $tarefas = Tarefa::where('professor_id', $id)->get();

        if ($tarefas->isEmpty()){
            return response()->json([
                'error' => 'nenhuma nota encontrada para este professor'
            ],404);
        }

        foreach ($tarefas as $tarefa) {
            $notas = Nota::where('tarefa_id', $tarefa->id)->get();

            $tarefa->notas = $notas;
        }

        return response()->json($tarefas, 200);
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
        // ID da instituição autenticada
        $instituicaoId = $request->input('authenticated_instituicao_id');
        
        // Validação dos dados de entrada
        $credenciais = $request->validate([
            'nota' => 'required|integer',
            'aluno_id' => 'required|integer',
            'tarefa_id' => 'required|integer'
        ]);

        // Verificar se o aluno pertence à instituição
        $aluno = User::where('id', $request->aluno_id)
                    ->where('instituicao_id', $instituicaoId)
                    ->first();
        
        $tarefa = Tarefa::where('id', $request->tarefa_id)->first();

        if (!$aluno) {
            return response()->json([
                'error' => 'Aluno não encontrado ou não pertence à instituição'
            ], 404);
        }

        if (!$tarefa) {
            return response()->json([
                'error' => 'Tarefa não encontrada'
            ], 404);
        }

        $disciplina = Disciplina::where('id' , $tarefa->disciplina_id)->first();
        $curso = Curso::where('id', $disciplina->curso_id)
        ->where('instituicao_id', $instituicaoId)
        ->first();

        if (!$curso || !$disciplina) {
            return response()->json([
                'error' => 'Tarefa não pertence a um curso da instituição'
            ], 403);
        }

        // Criar a nota
        $nota = Nota::create([
            'nota' => $request->nota,
            'aluno_id' => $request->aluno_id,
            'tarefa_id' => $request->tarefa_id
        ]);

        // Retornar resposta de sucesso
        return response()->json([
            'success' => 'Nota cadastrada com sucesso',
            'nota' => $nota,
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
