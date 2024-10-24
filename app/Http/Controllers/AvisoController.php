<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use Illuminate\Http\Request;
use App\Models\Disciplina;
use FFI\Exception;

class AvisoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $avisos = Aviso::all();

        return response()->json($avisos);
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
        $professor_id = $request->input('authenticated_user_id');

        $credentials = $request->validate([
            'titulo' => 'required',
            'aviso' => 'required',
            'disciplina_id' => 'required'
        ]);

        $disciplina = Disciplina::where('id', $request->disciplina_id)->where('professor_id', $professor_id)->first();

        if (!$disciplina) {
            return response()->json([
                'error' => "disciplina com id $request->disciplina_id não encontrada ou você não leciona"
            ],404);
        }

        try {
            $aviso = Aviso::create([
                'titulo' => $request->titulo,
                'aviso' => $request->aviso,
                'disciplina_id' => $request->disciplina_id
            ]);

            return response()->json([
                'success' => 'aviso cadastrado com sucesso'
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'error' => 'erro ao cadastrar aviso',
                'motivo' => $error->getMessage() 
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Aviso  $aviso
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $disciplina_id)
    {
        $professor_id = $request->input('authenticated_user_id'); 
        $disciplina = Disciplina::where('id', $disciplina_id)->where('professor_id', $professor_id)->first();

        if (!$disciplina) {
            return response()->json([
                'error' => "disciplina com id $disciplina_id não encontrada ou você não leciona"
            ],404);
        }
        
        $avisos = Aviso::where('disciplina_id', $disciplina_id)->get();

        if ($avisos->isEmpty()) {
            return response()->json(['not found' => 'nenhum aviso cadastrado']);
        }
        return response()->json($avisos);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Aviso  $aviso
     * @return \Illuminate\Http\Response
     */
    public function edit(Aviso $aviso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Aviso  $aviso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Aviso $aviso)
    {
        $aviso = Aviso::find($aviso);

        if (!$aviso) {
            return response()->json([
                'error' => 'nenhum aviso encontrado'
            ], 404);
        }

        $aviso->update($request->all());

        return response()->json(['success' => 'aviso atualizado', 'aviso' => $aviso], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Aviso  $aviso
     * @return \Illuminate\Http\Response
     */
    public function destroy(Aviso $aviso)
    {
        $aviso = Aviso::find($aviso);

        if (!$aviso) {
            return response()->json([
                'error' => 'nenhum aviso encontrado'
            ], 404);
        }

        $aviso->delete();

        return response()->json(['success' => 'aviso deletado'], 200);
    }
}
