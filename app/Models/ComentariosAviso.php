<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentariosAviso extends Model
{
    use HasFactory;
    protected $fillable = ['comentario', 'user_id', 'aviso_id'];
}
