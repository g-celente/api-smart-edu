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
        $id = $request->input('authenticated_user_id');

        $cursos = Curso::where('instituicao_id', $id)->get();

        if($cursos->isEmpty()){
            return response()->json([]);
        }

        $cursosId = $cursos->pluck('id');

        $disciplinas = Disciplina::whereIn('curso_id', $cursosId)->get();

        if ($disciplinas->isEmpty()){
            return response()->json([]);
        }
        
        return response()->json($disciplinas, 200);
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
        $instituicao_id = $request->input('authenticated_user_id');

        $credentials = $request->validate([
            'nome' => 'required', 
            'carga_horaria' => 'required',
            'disciplina_img' => '',
            'curso_id' => 'required',
            'professor_id' => 'required'
        ]);

        $curso = Curso::where('instituicao_id', $instituicao_id)
            ->where('id', $request->curso_id)
            ->first();

        $professor = User::where('id', $request->professor_id)->where('type_id' , 2)->first();

        if (!$curso || !$professor) {
            return response()->json([
                'error' => 'Professor ou Curso não encontrado!'
            ],404);
        }

        $results = Disciplina::create($request->all());

        return response()->json([
            'sucess' => 'Disciplina Cadastrada Com Sucesso',
            'disciplina' => $results
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Disciplina  $disciplina
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $Id)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        $curso = Curso::where('instituicao_id', $instituicao_id)->where('id', $Id)->first();

        if (!$curso) {
            return response()->json([
                'error' => 'Nenhum curso encontrado'
            ], 404);
        }

        $disciplinas = Disciplina::where('curso_id', $curso->id)->get();

        if ($disciplinas->isEmpty()) {
            return response()->json([]);
        }

        return response()->json($disciplinas, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Disciplina  $disciplina
     * @return \Illuminate\Http\Response
     */
    public function edit(Disciplina $disciplina)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Disciplina  $disciplina
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        // Buscar todos os cursos relacionados à instituição
        $cursos = Curso::where('instituicao_id', $instituicao_id)->get();

        // Verificar se há cursos
        if ($cursos->isEmpty()) {
            return response()->json([
                'error' => 'Nenhum curso encontrado'
            ], 404);
        }

        // Obter os IDs dos cursos
        $cursoIds = $cursos->pluck('id');

        // Buscar as disciplinas associadas aos cursos
        $disciplinas = Disciplina::whereIn('curso_id', $cursoIds)
                                ->where('id', $id)
                                ->first(); // Usa first() para buscar um único item

        // Verificar se a disciplina foi encontrada
        if (!$disciplinas) {
            return response()->json([
                'error' => 'Nenhuma disciplina encontrada'
            ], 404);
        }
        
        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'carga_horaria' => 'sometimes|required|integer',
            'curso_id' => 'sometimes|required|exists:cursos,id',
            'professor_id' => 'sometimes|required|exists:users,id'
        ]);

        $disciplinas->update($validatedData);

        return response()->json([
            'success' => 'Disciplina Atualizada',
            'disciplina' => $disciplinas
        ], 201);
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Disciplina  $disciplina
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        // Buscar todos os cursos relacionados à instituição
        $cursos = Curso::where('instituicao_id', $instituicao_id)->get();

        // Verificar se há cursos
        if ($cursos->isEmpty()) {
            return response()->json([
                'error' => 'Nenhum curso encontrado'
            ], 404);
        }

        // Obter os IDs dos cursos
        $cursoIds = $cursos->pluck('id');

        // Buscar as disciplinas associadas aos cursos
        $disciplinas = Disciplina::whereIn('curso_id', $cursoIds)
                                ->where('id', $id)
                                ->first(); // Usa first() para buscar um único item

        // Verificar se a disciplina foi encontrada
        if (!$disciplinas) {
            return response()->json([
                'error' => 'Nenhuma disciplina encontrada'
            ], 404);
        }

        $disciplinas->delete();

        return response()->json([
            'success' => 'Disciplina deletada'
        ], 201);
    }

    public function getDisciplina($disciplina_id)
    {
        
        //busca disciplina pelo ID

        $disciplina = Disciplina::find($disciplina_id);

        if($disciplina){
            return response()->json($disciplina);
        } else {
            return response()->json(['error' => 'Disciplina não encontrada'], 404);
        }
        // Verificar se há cursos
    }

    public function getProfessorDisciplina($disciplina_id)
    {
        
        //busca disciplina pelo ID

        $disciplina = Disciplina::find($disciplina_id);

        $professor = User::where('id', $disciplina->professor_id)->first();

        if($professor){
            return response()->json($professor);
        } else {
            return response()->json(['error' => 'Disciplina não encontrada'], 404);
        }
        // Verificar se há cursos
    }

      public function getDisciplinasByCursoId($curso_id)
        {
            // Busca todas as disciplinas que pertencem ao curso_id fornecido
            $disciplinas = Disciplina::where('curso_id', $curso_id)->get();

            // Verifica se há disciplinas encontradas
            if ($disciplinas->isEmpty()) {
                return response()->json(['error' => 'Disciplinas não encontradas'], 404);
            }

            // Retorna as disciplinas encontradas
            return response()->json($disciplinas);
        }
        
}