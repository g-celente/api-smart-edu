<?php

namespace App\Http\Controllers;

use App\Models\MaterialComplementar;
use App\Models\Tarefa;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialComplementarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $materias = Tarefa::all();

        return response()->json($materias);
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
            'titulo' => 'required',
            'descricao' => 'required',
            'material' => 'required|file|mimes:docx,xlsx,pdf,jpeg,png|max:10240',
            'tarefa_id' => 'required'
        ]);

        $tarefa = Tarefa::where('id', $request->tarefa_id)->first();

        if (!$tarefa) {
            return response()->json([
                'error' => "tarefa {$request->tarefa_id} não econtrada"
            ]);
        }

        $material = $request->file('material');
        $material_url = $material->store('materiais', 'public');

        try {
            $result = MaterialComplementar::create([
                'titulo' => $request->titulo,
                'descricao' => $request->descricao,
                'material' => $material_url,
                'tarefa_id' => $request->tarefa_id
            ]);

            return response()->json([
                'success' => 'material cadastrado com sucesso',
                'material' => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MaterialComplementar  $materialComplementar
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialComplementar $materialComplementar, Request $request)
    {
        $professor_id = $request->input('authenticated_user_id');

        // Obter todos os IDs de tarefas associados ao professor
        $tarefas = Tarefa::where('professor_id', $professor_id)->pluck('id');

        // Verificar se o material complementar pertence a uma das tarefas do professor
        $material = MaterialComplementar::where('id', $materialComplementar->id)
                                        ->whereIn('tarefa_id', $tarefas) // Usar whereIn para comparar com a lista de IDs
                                        ->first();

        // Se o material não for encontrado ou não for da tarefa do professor
        if (!$material) {
            return response()->json([
                'error' => 'Material não cadastrado pelo professor'
            ], 404);
        }

        // Retorna o material encontrado
        return response()->json($material, 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MaterialComplementar  $materialComplementar
     * @return \Illuminate\Http\Response
     */
    public function edit(MaterialComplementar $materialComplementar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MaterialComplementar  $materialComplementar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialComplementar $materialComplementar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MaterialComplementar  $materialComplementar
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialComplementar $materialComplementar)
    {
        //
    }
}
