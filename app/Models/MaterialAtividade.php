<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialAtividade extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'material', 'tarefa_id'];

    protected $table = 'material_atividade';
}
