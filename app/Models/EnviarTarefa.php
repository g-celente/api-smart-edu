<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnviarTarefa extends Model
{
    use HasFactory;
    protected $table = 'enviar_tarefa';
    protected $fillable = ['arquivo', 'texto', 'aluno_id', 'tarefa_id', 'status'];
}
