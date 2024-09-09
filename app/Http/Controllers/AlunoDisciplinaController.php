<?php

namespace App\Http\Controllers;

use App\Models\AlunoDisciplina;
use Illuminate\Http\Request;
use App\Models\Disciplina;
Use App\Models\User;
use App\Models\Curso;

class AlunoDisciplinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // ID da instituição autenticada
        $instituicaoId = $request->input('authenticated_user_id');

        // Verificar se a instituição existe
        $instituicao = User::where('id', $instituicaoId)->where('type_id', 3)->first(); // type_id 3 seria o da instituição

        if (!$instituicao) {
            return response()->json(['error' => 'Instituição não encontrada'], 404);
        }

        // Obter os cursos dessa instituição
        $cursos = Curso::where('instituicao_id', $instituicaoId)->get();

        // Obter as disciplinas associadas a esses cursos
        $disciplinasComAlunos = collect();

        foreach ($cursos as $curso) {
            $disciplinas = Disciplina::where('curso_id', $curso->id)->get();

            // Para cada disciplina, buscar os alunos relacionados
            foreach ($disciplinas as $disciplina) {
                $alunos = User::whereIn('id', function ($query) use ($disciplina) {
                    $query->select('aluno_id')
                        ->from('alunos_disciplinas')
                        ->where('disciplina_id', $disciplina->id);
                })->get();

                // Adicionar a lista de alunos à disciplina
                $disciplina->alunos = $alunos;

                // Adicionar a disciplina com os alunos à coleção de disciplinas
                $disciplinasComAlunos->push($disciplina);
            }
        }

        return response()->json($disciplinasComAlunos);
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
        $credentials = $request->validate([
            'aluno_id' => 'required',
            'disciplina_id' => 'required'
        ]);

        $aluno_id = User::where('id', $request->aluno_id)->first();
        $disciplina_id = Disciplina::where('id', $request->disciplina_id)->first();

        if ($aluno_id->type_id != 1) {
            return response()->json([
                'error' => 'somente aluno pode ser cadastrado'
            ]);
        }

        if (!$aluno_id || !$disciplina_id) {
            return response()->json([
                'error' => 'aluno ou disciplina não encontrada'
            ]);
        }

        $instituicaoId = $request->input('authenticated_user_id');
        $curso = Curso::where('id', $disciplina_id->curso_id)->where('instituicao_id', $instituicaoId)->first();

        if (!$curso) {
            return response()->json(['error' => 'Disciplina não pertence ao curso da instituição autenticada'], 403);
        }

        $relacionamento = AlunoDisciplina::create($request->all());

        return response()->json([
            'success' => 'Relacionamento adicionado',
            'relacionamento' => $relacionamento
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AlunoDisciplina  $alunoDisciplina
     * @return \Illuminate\Http\Response
     */
    public function show(AlunoDisciplina $alunoDisciplina)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AlunoDisciplina  $alunoDisciplina
     * @return \Illuminate\Http\Response
     */
    public function edit(AlunoDisciplina $alunoDisciplina)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AlunoDisciplina  $alunoDisciplina
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $alunoDisciplina = AlunoDisciplina::find($id);

        if (!$alunoDisciplina) {
            return response()->json(['error' => 'Relacionamento não encontrado'], 404);
        }

        $disciplina = Disciplina::find($request->input('disciplina_id'));

        if (!$disciplina) {
            return response()->json(['error' => 'Disciplina não encontrada'], 404);
        }

        // Obter a instituição do aluno
        $instituicaoId = $request->input('authenticated_user_id');
        $curso = Curso::where('id', $disciplina->curso_id)->where('instituicao_id', $instituicaoId)->first();

        if (!$curso) {
            return response()->json(['error' => 'Disciplina não pertence ao curso da instituição autenticada'], 403);
        }

        $alunoDisciplina->update($request->all());

        return response()->json(['success' => 'Relacionamento atualizado']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AlunoDisciplina  $alunoDisciplina
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $alunoDisciplina = AlunoDisciplina::find($id);

        if (!$alunoDisciplina) {
            return response()->json(['error' => 'Relacionamento não encontrado'], 404);
        }

        $disciplina = Disciplina::find($alunoDisciplina->disciplina_id);

        if (!$disciplina) {
            return response()->json(['error' => 'Disciplina não encontrada'], 404);
        }

        // Obter a instituição do aluno
        $instituicaoId = $request->input('authenticated_user_id');
        $curso = Curso::where('id', $disciplina->curso_id)->where('instituicao_id', $instituicaoId)->first();

        if (!$curso) {
            return response()->json(['error' => 'Disciplina não pertence ao curso da instituição autenticada'], 403);
        }

        $alunoDisciplina->delete();

        return response()->json(['success' => 'Relacionamento deletado']);
    }
}
