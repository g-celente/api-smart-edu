<?php

namespace App\Http\Controllers;

use App\Models\MaterialAtividade;
use App\Models\Disciplina;
use App\Models\Tarefa;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialAtividadeController extends Controller
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
            'material' => 'required|file|mimes:docx,xlsx,pdf,jpeg,png|max:10240',
            'tarefa_id' => 'required'
        ]);

        $tarefa = Tarefa::where('id', $request->tarefa_id)->first();

        if (!$tarefa) {
            return response()->json([
                'error' => "tarefa {$request->tarefa_id} não encontrada"
            ]);
        }

        $material = $request->file('material');
        $material_url = $material->store('materiais', 'public');

        try {
            $result = MaterialAtividade::create([
                'titulo' => $request->titulo,
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
     * @param  \App\Models\MaterialAtividade  $MaterialAtividade
     * @return \Illuminate\Http\Response
     */
    public function show($tarefa_id, Request $request)
    {
        $professor_id = $request->input('authenticated_user_id');

        // Verificar se a tarefa existe e pertence ao professor
        $tarefa = Tarefa::where('id', $tarefa_id)
                        ->where('professor_id', $professor_id)
                        ->first();

        if (!$tarefa) {
            return response()->json([
                'error' => "Tarefa com ID $tarefa_id não encontrada ou não pertence ao professor"
            ], 404);
        }

        // Obter todos os materiais associados à tarefa
        $materiais = MaterialAtividade::where('tarefa_id', $tarefa->id)->get();

        // Se não houver materiais cadastrados para a tarefa
        if ($materiais->isEmpty()) {
            return response()->json([
                'error' => 'Nenhum material cadastrado para esta tarefa'
            ], 404);
        }

        return response()->json($materiais, 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MaterialAtividade  $MaterialAtividade
     * @return \Illuminate\Http\Response
     */
    public function edit(MaterialAtividade $MaterialAtividade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MaterialAtividade  $MaterialAtividade
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialAtividade $materialAtividade)
    {
        $material = MaterialAtividade::find($materialAtividade);

        if (!$material) {
            return response()->json([
                'error' => 'material não encontrado'
            ], 404);
        }

        $material->update($request->all());

        return response()->json([
            'success' => 'material atualizado'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MaterialAtividade  $MaterialAtividade
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialAtividade $materialAtividade)
    {
        $material = MaterialAtividade::find($materialAtividade);

        if (!$material) {
            return response()->json([
                'error' => 'material não encontrado'
            ], 404);
        }

        $material->delete();

        return response()->json([
            'success' => 'material deletado com sucesso'
        ], 200);
    }
}
