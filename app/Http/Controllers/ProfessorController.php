<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Disciplina;
use App\Models\AlunoCurso;
use App\Models\Curso;

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

    public function quantidadeAlunos(Request $request) {

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
        
        // 3. Buscar alunos relacionados aos cursos e contar a quantidade
        $quantidadeAlunos = AlunoCurso::whereIn('curso_id', $cursosIds)->distinct()->count('aluno_id');
        
        if ($quantidadeAlunos === 0) {
            return response()->json(['message' => 'Nenhum aluno encontrado para estes cursos.'], 404);
        }
        
        // Retornar a quantidade de alunos
        return response()->json(['quantidade_alunos' => $quantidadeAlunos], 200);


    }

    public function quantidadeDisciplinas (Request $request) {
        $professorId = $request->input('authenticated_user_id');

        // 1. Contar a quantidade de disciplinas que o professor leciona
        $quantidadeDisciplinas = Disciplina::where('professor_id', $professorId)->count();

        if ($quantidadeDisciplinas === 0) {
            return response()->json(['message' => 'Nenhuma disciplina encontrada para este professor.'], 404);
        }

        // Retornar a quantidade de disciplinas
        return response()->json(['quantidade_disciplinas' => $quantidadeDisciplinas], 200);

    }
}
