<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Disciplina;
use App\Models\AlunoCurso;
use App\Models\Curso;
use App\Models\Tarefa;
use App\Models\EnviarTarefa;

class ProfessorController extends Controller
{
    public function alunos(Request $request) 
    {
        $professorId = $request->input('authenticated_user_id');

        // 1. Buscar disciplinas do professor
        $cursosDisciplinas = Disciplina::where('professor_id', $professorId)->pluck('curso_id');

        if ($cursosDisciplinas->isEmpty()) {
            return response()->json(['message' => 'Nenhuma disciplina encontrada para este professor.'], 404);
        }

        // 2. Buscar cursos relacionados às cursosDisciplinas
        $cursosIds = Curso::whereIn('id', $cursosDisciplinas)->pluck('id');

        if ($cursosIds->isEmpty()) {
            return response()->json(['message' => 'Nenhum curso encontrado para estas disciplinas.'], 404);
        }

        // 3. Buscar alunos relacionados aos cursos
        $alunosIds = AlunoCurso::whereIn('curso_id', $cursosIds)->pluck('aluno_id');

        if ($alunosIds->isEmpty()) {
            return response()->json(['message' => 'Nenhum aluno encontrado para estes cursos.'], 404);
        }

        // 4. Obter os dados dos alunos
        $alunos = User::whereIn('id', $alunosIds)->get();

        return response()->json($alunos, 200);
    }

    public function disciplinas(Request $request) {
        
        $id = $request->input('authenticated_user_id');

        $disciplina = Disciplina::where('professor_id', $id)->get();

        return response()->json($disciplina);

    }

    public function tarefasAlunosConcluidas(Request $request, $aluno_id)
    {
        // Pega o ID do professor autenticado do token
        $professor_id = $request->input('authenticated_user_id');
        
        // Obtém todas as disciplinas que o professor leciona
        $disciplinasProfessor = Disciplina::where('professor_id', $professor_id)->pluck('id');
        
        if ($disciplinasProfessor->isEmpty()) {
            return response()->json([
                'error' => 'O professor não leciona em nenhuma disciplina.'
            ], 404);
        }

        // Obtém as tarefas relacionadas às disciplinas que o professor leciona
        $tarefas = Tarefa::whereIn('disciplina_id', $disciplinasProfessor)->get();

        if ($tarefas->isEmpty()) {
            return response()->json([
                'error' => 'Nenhuma tarefa cadastrada para as disciplinas que o professor leciona.'
            ], 404);
        }

        // Inicializa as contagens
        $total_de_tarefas = $tarefas->count();
        $tarefasFeitas = 0;
        $tarefasNaoFeitas = 0;

        // Verifica quais tarefas o aluno enviou e se foram concluídas
        foreach ($tarefas as $tarefa) {
            // Verifica se o aluno enviou a tarefa e se está concluída
            $enviada = EnviarTarefa::where('aluno_id', $aluno_id)
                                    ->where('tarefa_id', $tarefa->id)
                                    ->first();

            if ($enviada && $enviada->status == 'concluida') {
                $tarefasFeitas++;
            } else {
                $tarefasNaoFeitas++;
            }
        }

        // Calcula a porcentagem de envio
        $porcentagemEnvio = $total_de_tarefas > 0 ? ($tarefasFeitas / $total_de_tarefas) * 100 : 0;

        // Retorna o payload no formato solicitado
        return response()->json([
            'total_de_tarefas' => $total_de_tarefas,
            'tarefas_feitas' => $tarefasFeitas,
            'tarefas_nao_feitas' => $tarefasNaoFeitas,
            'porcentagem_de_envio' => round($porcentagemEnvio, 2) // Arredondado para 2 casas decimais
        ], 200);
    }

}
