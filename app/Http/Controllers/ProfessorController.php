<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Disciplina;

class ProfessorController extends Controller
{
    public function alunos(Request $request) {
        
        $id = $request->input('authenticated_user_id');

        $disciplina = Disciplina::where('professor_id', $id)->get();

    }
}
