<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlunoDisciplina extends Model
{
    use HasFactory;

    protected $table = 'alunos_disciplinas';
    protected $fillable = ['aluno_id', 'disciplina_id'];
}
