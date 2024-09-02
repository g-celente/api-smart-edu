<?php

namespace App\Http\Controllers;

Use App\Models\Instituicoe;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = Curso::all();

        return response()->json($results);
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
            'nome' => 'required',
            'descricao' => 'required',
            'periodos' => 'required', 
            'instituicao_id' => 'required'
        ]);

        $name = Curso::where('nome', $request->nome)
        ->where('instituicao_id', $request->instituicao_id)->first();

        if ($name) {
            return response()->json([
                'error' => 'curso já cadastrado'
            ]);
        }

        $instituicao = Instituicoe::where('id', $request->instituicao_id)->first();

        if (!$instituicao) {
            return response()->json([
                'error' => 'Instituicao não cadastrada'
            ]);
        }

        $results = Curso::create($request->all());
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
    public function show($id)
    {
        $cursos = Curso::find($id);

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
    public function update(Request $request, $id, Curso $curso)
    {
        $curso = Curso::find($id);

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
    public function destroy($id)
    {
        $curso = Curso::find($id);

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
