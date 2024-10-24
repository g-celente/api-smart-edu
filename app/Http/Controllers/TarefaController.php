<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\Tarefa;
use Illuminate\Http\Request;
use App\Models\Curso;

use function PHPUnit\Framework\isEmpty;

class TarefaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Id = $request->input('authenticated_user_id');
       
        $tarefas = Tarefa::where('professor_id', $Id)->get();

        if ($tarefas->isEmpty()) {
            return response()->json([]);
        }

        return response()->json($tarefas, 200);
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
        $Id = $request->input('authenticated_user_id');
        $instituicao_id = $request->input('authenticated_instituicao_id');

        $credentials = $request->validate([
            'nome' => 'required',
            'descricao' => 'required', 
            'disciplina_id' => 'required | integer',
            'data_entrega' => 'required | date'
            
        ]);
        
        $cursos = Curso::where('instituicao_id', $instituicao_id)->get();
        $cursoIds = $cursos->pluck('id');
        $disciplinas = Disciplina::whereIn('curso_id', $cursoIds)
                                ->where('id', $request->disciplina_id)
                                ->first();

        if (!$disciplinas) {
            return response()->json([
                'error' => 'Nenhuma disciplina encontrada'
            ], 404);
        }

        $tarefa = Tarefa::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'professor_id' => $Id,
            'disciplina_id' => $request->disciplina_id,
            'data_entrega' => $request->data_entrega
        ]);

        return response()->json([
            'sucess' => 'tarefa cadastrada',
            'tarefa' => $tarefa,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function show(Tarefa $tarefa, Request $request)
    {
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::where('professor_id', $id)->where('id', $tarefa)->get();

        if ($tarefa) {
            return response()->json($tarefa, 200);
        }

        return response()->json([
            'error' => 'tarefa não encontrada'
        ], 404);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarefa $tarefa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarefa $tarefa)
    {
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::where('professor_id', $id)->where('id', $tarefa)->get();

        if ($tarefa) {
            $tarefa->update($request->all());
            return response()->json([
                'sucess' => 'tarefa atualizada',
                'tarefa' => $tarefa
            ], 201);
        }

        return response()->json([
            'error' => 'tarefa não encontrada'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarefa $tarefa, Request $request)
    {
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::where('professor_id', $id)->where('id', $tarefa)->first();

        if ($tarefa) {
            $tarefa->delete();
            return response()->json([
                'success' => 'tarefa deletada'
            ], 200);
        }

        return response()->json([
            'error' => 'tarefa não encontrada'
        ], 404);
        
    }

  public function getTaskById(Request $request, $tarefa_id)
    {
        // Agora $request está disponível
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::find($tarefa_id);

        if ($tarefa) {
            return response()->json($tarefa);
        } else {
            return response()->json(['error' => 'Tarefa não encontrada'], 404);
        }
    }


  public function deleteTaskById(Request $request, $tarefa_id)
    {
        // Agora $request está disponível
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::find($tarefa_id);

        if ($tarefa) {
            $tarefa->delete();
            return response()->json([
                'success' => 'tarefa deletada'
            ], 200);
        } else {
            return response()->json(['error' => 'Tarefa não encontrada'], 404);
        }
    }

    public function getTarefaDisciplina(Request $request, $disciplina_id) {

        $tarefas = Tarefa::where('disciplina_id', $disciplina_id)->get();

        if ($tarefas->isEmpty()) {
            return response()->json(['error' => 'nenhuma tarefa encontrada'], 404);
        }

        return response()->json($tarefas, 200);
    } 
}
