<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nota;
use App\Models\Tarefa;
use App\Models\Disciplina;
use App\Models\Curso;
use App\Models\AlunoDisciplina;
use App\Models\User;


class AlunoController extends Controller
{
    public function notas(Request $request) {
        $id = $request->input('authenticated_user_id'); 
        $notas = Nota::where('aluno_id', $id)->get();
        $tarefa_id = Nota::where('aluno_id', $id)->pluck('tarefa_id');
        $tarefa = Tarefa::where('id', $tarefa_id)->first();

        if ($notas->isEmpty()) {
            return response()->json([
                'error' => 'nenhuma nota cadastrada'
            ]);
        }

        return response()->json([
            "Tarefa" => $tarefa,
            "Nota" => $notas
        ]);
    }
    public function tarefas(Request $request) {
        $alunoId = $request->input('authenticated_user_id');
    
        // Obter as IDs das disciplinas associadas ao aluno
        $disciplinasIds = AlunoDisciplina::where('aluno_id', $alunoId)->pluck('disciplina_id');
    
        // Obter as disciplinas associadas a essas IDs
        $disciplinas = Disciplina::whereIn('id', $disciplinasIds)->get();
    
        // Obter as tarefas associadas a essas disciplinas
        $tarefas = Tarefa::whereIn('disciplina_id', $disciplinasIds)->get();
    
        if ($tarefas->isEmpty()) {
            return response()->json(['error' => 'Nenhuma tarefa encontrada'], 404);
        }
    
        // Formatar a resposta com as tarefas e suas disciplinas
        $tarefasComDetalhes = $tarefas->map(function ($tarefa) use ($disciplinas) {
            $disciplina = $disciplinas->firstWhere('id', $tarefa->disciplina_id);
            return [
                'Nome Tarefa' => $tarefa->nome,
                'Descrição' => $tarefa->descricao,
                'Professor' => $tarefa->professor_id,
                'Data de Entrega' => $tarefa->data_entrega,
                'Disciplina' => [
                    'Nome' => $disciplina ? $disciplina->nome : 'Disciplina não encontrada',
                ]
            ];
        });
    
        return response()->json($tarefasComDetalhes);
    }
    public function disciplinas(Request $request) {
        $alunoId = $request->input('authenticated_user_id');
    
        // Obter a instituição do aluno
        $instituicaoId = User::find($alunoId)->instituicao_id;
    
        // Obter os cursos dessa instituição
        $cursos = Curso::where('instituicao_id', $instituicaoId)->pluck('id');
    
        // Obter as disciplinas associadas a esses cursos
        $disciplinas = Disciplina::whereIn('curso_id', $cursos)->get();
    
        if ($disciplinas->isEmpty()) {
            return response()->json(['error' => 'Nenhuma disciplina encontrada'], 404);
        }
    
        return response()->json($disciplinas);
    }   
    public function curso(Request $request) {
        $alunoId = $request->input('authenticated_user_id');
    
        // Obter a instituição do aluno
        $instituicaoId = User::find($alunoId)->instituicao_id;
    
        // Obter os cursos dessa instituição
        $cursos = Curso::where('instituicao_id', $instituicaoId)->first();

        if (!$cursos) {
            return response()->json([
                'error' => 'nenhum curso para este aluno'
            ]);
        }

        return response()->json($cursos);
    }
}
