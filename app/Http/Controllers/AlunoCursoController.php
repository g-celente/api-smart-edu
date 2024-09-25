<?php

namespace App\Http\Controllers;

use App\Models\AlunoCurso;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Curso;

class AlunoCursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $instituicaoId = $request->input('authenticated_user_id');

        // Buscar os cursos da instituição
        $cursos = Curso::where('instituicao_id', $instituicaoId)->get();

        if ($cursos->isEmpty()) {
            return response()->json([]); // Retorna um array vazio se não houver cursos
        }

        // Criar uma coleção para armazenar os cursos com alunos
        $cursosComAlunos = collect();

        foreach ($cursos as $curso) {
            // Obter os alunos associados ao curso
            $alunos = User::whereIn('id', function ($query) use ($curso) {
                $query->select('aluno_id')
                    ->from('aluno_cursos')
                    ->where('curso_id', $curso->id);
            })->get();

            // Adicionar os alunos ao curso
            $curso->alunos = $alunos;

            // Adicionar o curso com alunos à coleção
            $cursosComAlunos->push($curso);
        }

        return response()->json($cursosComAlunos);
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

        $instituicaoId = $request->input('authenticated_user_id');

        $credentials = $request->validate([
            'aluno_id' => 'required',
            'curso_id' => 'required'
        ]);

        $curso = Curso::where('instituicao_id', $instituicaoId)->where('id', $request->curso_id)->first();
        $aluno = User::where('instituicao_id', $instituicaoId)->where('id', $request->aluno_id)->where('type_id', 1)->first();

        if (!$curso || !$aluno) {
            return response()->json([
                'error' => 'aluno ou curso não encontrado na instituicao'
            ],404);
        }

        $validate = AlunoCurso::create([
            'aluno_id' => $request->aluno_id,
            'curso_id' => $request->curso_id
        ]);

        return response()->json([
            'success' => 'relação cadastrada',
            'relacao' => $validate
        ]);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AlunoCurso  $alunoCurso
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $instituicaoId = $request->input('authenticated_user_id');

        // Buscar o curso pelo ID e garantir que ele pertence à instituição autenticada
        $curso = Curso::where('id', $id)->where('instituicao_id', $instituicaoId)->first();

        // Se o curso não for encontrado ou não pertencer à instituição, retorna um erro 404
        if (!$curso) {
            return response()->json(['error' => 'Curso não encontrado ou não pertence à instituição'], 404);
        }

        // Obter os alunos associados ao curso
        $alunos = User::whereIn('id', function ($query) use ($curso) {
            $query->select('aluno_id')
                ->from('aluno_cursos')
                ->where('curso_id', $curso->id);
        })->get();

        // Adicionar os alunos ao curso
        $curso->alunos = $alunos;

        // Retornar o curso com seus alunos em formato JSON
        return response()->json($alunos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AlunoCurso  $alunoCurso
     * @return \Illuminate\Http\Response
     */
    public function edit(AlunoCurso $alunoCurso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AlunoCurso  $alunoCurso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AlunoCurso $alunoCurso)
    {
        // Se o relacionamento não for encontrado, retornar erro 404
        if (!$alunoCurso) {
            return response()->json([
                'error' => 'Relacionamento não encontrado'
            ], 404);
        }

        // Atualizar o relacionamento com os dados da requisição
        $alunoCurso->update($request->all());

        // Retornar resposta de sucesso
        return response()->json([
            'success' => 'Relacionamento atualizado com sucesso',
            'relacionamento' => $alunoCurso
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AlunoCurso  $alunoCurso
     * @return \Illuminate\Http\Response
     */
    public function destroy(AlunoCurso $alunoCurso)
    {
        // Se o relacionamento não for encontrado, retornar erro 404
        if (!$alunoCurso) {
            return response()->json([
                'error' => 'Relacionamento não encontrado'
            ], 404);
        }

        // Deletar o relacionamento
        $alunoCurso->delete();

        // Retornar resposta de sucesso
        return response()->json([
            'success' => 'Relacionamento deletado com sucesso',
        ], 200);
    }
}
