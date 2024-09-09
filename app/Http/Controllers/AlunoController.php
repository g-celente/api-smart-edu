<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nota;
use App\Models\Tarefa;
use App\Models\Disciplina;
use App\Models\Curso;


class AlunoController extends Controller
{
    public function notas(Request $request) {
        $id = $request->input('authenticated_user_id'); 
        $notas = Nota::where('aluno_id', $id)->get();

        if ($notas) {
            return response()->json($notas);
        }

        return response()->json([
            'error' => 'nenhuma nota cadastrada'
        ]);

    }
    public function tarefas(Request $request) {
        
    }
    public function disciplinas(Request $request) {
        
    }
    public function curso(Request $request) {
        
    }
}
