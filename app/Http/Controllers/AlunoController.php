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

    public function meusCursos(Request $request) {
        $alunoId = $request->input('authenticated_user_id');

        // Busca os relacionamentos entre aluno e cursos
        $relacionamentos = AlunoCurso::where('aluno_id', $alunoId)->get();

        // Array para armazenar os cursos completos
        $cursos = [];

        // Itera sobre cada relacionamento para buscar o curso correspondente
        foreach ($relacionamentos as $relacionamento) {
            $curso = Curso::find($relacionamento->curso_id);
            if ($curso) {
                $cursos[] = $curso;
            }
        }

        // Verifica se há cursos encontrados
        if (empty($cursos)) {
            return response()->json([]);
        }

        // Retorna os dados dos cursos
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

    public function getAlunoById($aluno_id) {

        $aluno = User::find($aluno_id);
        
        if ($aluno) {
            return response()->json($aluno);
        } else {
            return response()->json(['error'=> 'aluno não encontrado'], 400);
        }
        
    }

    public function getTarefasENotas(Request $request, $disciplina_id)
    {
        // ID do aluno autenticado
        $aluno_id = $request->input('authenticated_user_id');

        // Verificar se o aluno está matriculado em algum curso
        $cursoAluno = AlunoCurso::where('aluno_id', $aluno_id)->first();

        if (!$cursoAluno) {
            return response()->json(['error' => 'Aluno não está matriculado em nenhum curso'], 404);
        }

        // Verificar se a disciplina informada pertence ao curso do aluno
        $disciplina = Disciplina::where('curso_id', $cursoAluno->curso_id)
                                ->where('id', $disciplina_id)
                                ->first();

        if (!$disciplina) {
            return response()->json(['error' => 'Disciplina não encontrada para o curso em que o aluno está matriculado'], 404);
        }

        // Buscar todas as tarefas relacionadas à disciplina
        $tarefas = Tarefa::where('disciplina_id', $disciplina_id)->get();

        if ($tarefas->isEmpty()) {
            return response()->json(['error' => 'Nenhuma tarefa encontrada para essa disciplina'], 404);
        }

        // Inicializar variáveis para cálculo da média final
        $totalTarefas = 0;
        $somaNotas = 0;

        // Preparar o response
        $response = $tarefas->map(function ($tarefa) use ($aluno_id, &$totalTarefas, &$somaNotas) {
            // Buscar a nota da tarefa para o aluno
            $nota = Nota::where('tarefa_id', $tarefa->id)
                        ->where('aluno_id', $aluno_id)
                        ->first();

            // Incrementar a quantidade de tarefas
            $totalTarefas++;

            // Se houver nota, adicionar à soma, senão considerar 0
            $notaValor = $nota ? $nota->nota : 0;
            $somaNotas += $notaValor;

            // Retornar as informações da tarefa e nota
            return [
                'tarefa_id' => $tarefa->id,
                'tarefa_nome' => $tarefa->nome,
                'nota' => $notaValor
            ];
        });

        // Calcular a média final
        $mediaFinal = $totalTarefas > 0 ? $somaNotas / $totalTarefas : 0;

        // Adicionar a média final ao response
        return response()->json([
            'tarefas' => $response,
            'media_final' => $mediaFinal
        ], 200);
    }


}
