<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialComplementar extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'descricao', 'material', 'tarefa_id'];

    protected $table = 'material_complementar';
}
