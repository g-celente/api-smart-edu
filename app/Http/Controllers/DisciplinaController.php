<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Curso;
use App\Models\Disciplina;
use Illuminate\Http\Request;

class DisciplinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        // Buscar disciplinas diretamente relacionadas aos cursos da instituição
        $disciplinas = Disciplina::whereHas('curso', function ($query) use ($instituicao_id) {
            $query->where('instituicao_id', $instituicao_id);
        })->get();

        // Verificar se há disciplinas associadas aos cursos
        if ($disciplinas->isEmpty()) {
            return response()->json([
                'error' => 'Nenhuma disciplina encontrada'
            ], 404);
        }

        // Retornar as disciplinas encontradas
        return response()->json($disciplinas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        // Validação dos dados
        $credentials = $request->validate([
            'nome' => 'required', 
            'carga_horaria' => 'required',
            'curso_id' => 'required|exists:cursos,id',
            'professor_id' => 'required|exists:users,id'
        ]);

        // Verificar se o curso pertence à instituição
        $curso = Curso::where('instituicao_id', $instituicao_id)
            ->where('id', $request->curso_id)
            ->first();

        // Verificar se o professor pertence à instituição
        $professor = User::where('id', $request->professor_id)
            ->where('instituicao_id', $instituicao_id)
            ->first();

        if (!$curso || !$professor) {
            return response()->json([
                'error' => 'Professor ou Curso não encontrado ou não pertencem à instituição!'
            ], 404);
        }

        // Criar a nova disciplina
        $disciplina = Disciplina::create($request->all());

        return response()->json([
            'success' => 'Disciplina Cadastrada Com Sucesso',
            'disciplina' => $disciplina
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        // Buscar a disciplina dentro dos cursos da instituição
        $disciplina = Disciplina::whereHas('curso', function ($query) use ($instituicao_id) {
            $query->where('instituicao_id', $instituicao_id);
        })->where('id', $id)->first();

        // Verificar se a disciplina foi encontrada
        if (!$disciplina) {
            return response()->json([
                'error' => 'Nenhuma disciplina encontrada'
            ], 404);
        }

        return response()->json($disciplina);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        // Buscar a disciplina dentro dos cursos da instituição
        $disciplina = Disciplina::whereHas('curso', function ($query) use ($instituicao_id) {
            $query->where('instituicao_id', $instituicao_id);
        })->where('id', $id)->first();

        // Verificar se a disciplina foi encontrada
        if (!$disciplina) {
            return response()->json([
                'error' => 'Nenhuma disciplina encontrada'
            ], 404);
        }

        // Validação dos dados atualizados
        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'carga_horaria' => 'sometimes|required|integer',
            'curso_id' => 'sometimes|required|exists:cursos,id',
            'professor_id' => 'sometimes|required|exists:users,id'
        ]);

        // Se for passado um novo curso, verificar se pertence à instituição
        if (isset($validatedData['curso_id'])) {
            $curso = Curso::where('id', $validatedData['curso_id'])
                          ->where('instituicao_id', $instituicao_id)
                          ->first();
            if (!$curso) {
                return response()->json(['error' => 'Curso não encontrado na instituição'], 404);
            }
        }

        // Atualizar a disciplina
        $disciplina->update($validatedData);

        return response()->json([
            'success' => 'Disciplina Atualizada',
            'disciplina' => $disciplina
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        // Buscar a disciplina dentro dos cursos da instituição
        $disciplina = Disciplina::whereHas('curso', function ($query) use ($instituicao_id) {
            $query->where('instituicao_id', $instituicao_id);
        })->where('id', $id)->first();

        // Verificar se a disciplina foi encontrada
        if (!$disciplina) {
            return response()->json([
                'error' => 'Nenhuma disciplina encontrada'
            ], 404);
        }

        // Deletar a disciplina
        $disciplina->delete();

        return response()->json([
            'success' => 'Disciplina deletada'
        ], 200);
    }
}