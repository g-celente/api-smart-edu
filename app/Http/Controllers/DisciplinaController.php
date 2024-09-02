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
    public function index()
    {
        $results = Disciplina::all();

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
            'carga_horaria' => 'required',
            'curso_id' => 'required',
            'professor_id' => 'required'
        ]);

        $curso = Curso::find($request->curso_id);
        $professor = User::find($request->professor_id);

        if (!$curso || !$professor) {
            return response()->json([
                'error', 'Professor ou Curso não encontrado!'
            ]);
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
    public function show($id)
    {
        $disciplina = Disciplina::find($id);

        if (!$disciplina) {
            return response()->json([
                'error' => 'disciplina não cadastrada'
            ], 404);
        }

        return response()->json($disciplina);
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
        $disciplina = Disciplina::find($id);

        if (!$disciplina) {
            return response()->json([
                'error' => 'Disciplina não encontrada'
            ], 404);
        }
        
        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'carga_horaria' => 'sometimes|required|integer',
            'curso_id' => 'sometimes|required|exists:cursos,id',
            'professor_id' => 'sometimes|required|exists:users,id'
        ]);

        $disciplina->update($validatedData);

        return response()->json([
            'success' => 'Disciplina Atualizada',
            'disciplina' => $disciplina
        ], 201);
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Disciplina  $disciplina
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $disciplina = Disciplina::find($id);

        if (!$disciplina) {
            return response()->json([
                'error' => 'Disciplina não encontrada'
            ], 404);
        }

        $disciplina->delete();

        return response()->json([
            'success' => 'Disciplina deletada'
        ], 201);
    }
}
