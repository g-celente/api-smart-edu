<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nota;
use App\Models\Tarefa;
use App\Models\Disciplina;
use App\Models\Curso;
use App\Models\AlunoCurso;
use App\Models\User;
use Symfony\Component\CssSelector\Node\FunctionNode;

class AlunoController extends Controller
{
    public function notas(Request $request, $Id) {
        $aluno_id = $request->input('authenticated_user_id'); 

        $notas = Nota::where('tarefa_id', $Id)->where('aluno_id', $aluno_id)->get();

        if ($notas->isEmpty()) {
            return response()->json([]);
        }

        return response()->json([$notas]);
    }
    public function tarefas(Request $request) {
        $alunoId = $request->input('authenticated_user_id');

        $curso_id = AlunoCurso::where('aluno_id', $alunoId)->pluck('curso_id');
        $disciplina_id = Disciplina::whereIn('curso_id', $curso_id)->pluck('id');
    
        $tarefas = Tarefa::whereIn('disciplina_id', $disciplina_id)->get();

        if($tarefas->isEmpty()) {
            return response()->json([]);
        }

        return response()->json([$tarefas]);

    }
    public function disciplinas(Request $request) {
        $alunoId = $request->input('authenticated_user_id');

        $cursos = AlunoCurso::where('aluno_id', $alunoId)->pluck('curso_id');

        $disciplinas = Disciplina::where('curso_id', $cursos)->get();

        if($disciplinas->isEmpty()) {
            return response()->json([]);
        }

        return response()->json([$disciplinas]);
    }   
    public function curso(Request $request) {
        $alunoId = $request->input('authenticated_user_id');

        $cursos = AlunoCurso::where('aluno_id', $alunoId)->get();

        if ($cursos->isEmpty()) {
            return response()->json([]);
        }

        return response()->json($cursos, 200);
    }

    public function getTarefasById($id_disciplina) {

        $tarefas = Tarefa::where('disciplina_id', $id_disciplina)->get();
        
        if ($tarefas->isEmpty()) {
            return response()->json([
                'error' => 'Nenhuma tarefa encontrada'
            ],404);
        }
        
        return response()->json($tarefas);
    }
}
