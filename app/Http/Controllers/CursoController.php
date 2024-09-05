<?php

namespace App\Http\Controllers;

Use App\Models\User;
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
        $cursos = Curso::where('instituicao_id', $instituicao_id)->where('id', $instituicao_id)->get();

        if ($cursos) {
            return response()->json($cursos);
        }
        
        return response()->json([
            'error' => 'Nenhum curso cadastrado nessa instituicao'
        ]);
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
        $id = $request->input('authenticated_user_id');

        $credentials = $request->validate([
            'nome' => 'required',
            'descricao' => 'required',
            'periodos' => 'required', 
        ]);

        $name = Curso::where('nome', $request->nome)
        ->where('instituicao_id', $id)->first();

        if ($name) {
            return response()->json([
                'error' => 'curso já cadastrado nessa instituicao'
            ]);
        }

        $instituicao = User::where('id', $id)->first();

        if (!$instituicao) {
            return response()->json([
                'error' => 'Instituicao não cadastrada'
            ]);
        }

        $results = Curso::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'periodos' => $request->periodos,
            'instituicao_id' => $id
        ]);
        return response()->json([
            'success' => 'Curso cadastrado',
            'Curso' => $results
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $instituicao_id = $request->input('authenticated_user_id');
        $cursos = Curso::where('instituicao_id', $instituicao_id)->where('id', $id)->get();

        if (!$cursos) {
            return response()->json([
                'error' => 'curso não cadastrado'
            ]);
        }

        return response()->json($cursos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function edit(Curso $curso)
    {
        //
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
        $curso = Curso::where('instituicao_id', $instituicao_id)->where('id', $curso)->get();

        if (!$curso) {
            return response()->json([
                'error' => 'nenhum curso encontrado'
            ]);
        }

        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|required|string',
            'periodo' => 'sometimes|required|integer',
            'instituicao_id' => 'sometimes|required|exists:instituicoes,id'
        ]);

        $curso->update($validatedData);

        return response()->json([
            'success' => 'Curso Atualizado Com Sucesso', 
            'Curso' => $curso
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Curso $curso)
    {
        $instituicao_id = $request->input('authenticated_user_id');
        $curso = Curso::where('instituicao_id', $instituicao_id)->where('id', $curso)->get();

        if (!$curso) {
            return response()->json([
                'error' => 'nenhum curso encontrado'
            ]);
        }

        $curso->delete();

        return response()->json([
            'success' => 'curso removido'
        ]);
    }
}
