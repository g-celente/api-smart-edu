<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $instituicao_id = $request->input('authenticated_user_id');
        
        // Verificar se a instituição existe
        $instituicao = User::find($instituicao_id);
        if (!$instituicao) {
            return response()->json(['error' => 'Instituição não encontrada'], 404);
        }

        // Buscar cursos relacionados à instituição
        $cursos = Curso::where('instituicao_id', $instituicao_id)->get();

        if ($cursos->isEmpty()) {
            return response()->json(['error' => 'Nenhum curso cadastrado nessa instituição'], 404);
        }

        return response()->json($cursos, 200);
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

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'periodos' => 'required|integer',
        ]);

        // Verificar se o curso já existe na instituição
        if (Curso::where('nome', $request->nome)->where('instituicao_id', $instituicao_id)->exists()) {
            return response()->json(['error' => 'Curso já cadastrado nessa instituição'], 409);
        }

        $instituicao = User::find($instituicao_id);

        if (!$instituicao) {
            return response()->json(['error' => 'Instituição não cadastrada'], 404);
        }

        $curso = Curso::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'periodos' => $request->periodos,
            'instituicao_id' => $instituicao_id
        ]);

        return response()->json(['success' => 'Curso cadastrado', 'Curso' => $curso], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $instituicao_id = $request->input('authenticated_user_id');
        
        // Buscar curso da instituição específica
        $curso = Curso::where('instituicao_id', $instituicao_id)->find($id);

        if (!$curso) {
            return response()->json(['error' => 'Curso não encontrado'], 404);
        }

        return response()->json($curso, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Curso $curso)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        // Verifica se o curso pertence à instituição
        if ($curso->instituicao_id !== $instituicao_id) {
            return response()->json(['error' => 'Curso não pertence a esta instituição'], 403);
        }

        // Valida dados
        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|required|string',
            'periodos' => 'sometimes|required|integer',
        ]);

        $curso->update($validatedData);

        return response()->json(['success' => 'Curso atualizado com sucesso', 'Curso' => $curso], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Curso $curso)
    {
        $instituicao_id = $request->input('authenticated_user_id');

        // Verificar se o curso pertence à instituição
        if ($curso->instituicao_id !== $instituicao_id) {
            return response()->json(['error' => 'Curso não pertence a esta instituição'], 403);
        }

        $curso->delete();

        return response()->json(['success' => 'Curso removido com sucesso'], 200);
    }
}