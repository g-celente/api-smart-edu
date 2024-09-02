<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Instituicoe extends Authenticatable implements JWTSubject
{
    // Implementar os métodos necessários da interface JWTSubject

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'type_id' => $this->type_id,
        ];
    }

    protected $fillable = ['nome', 'email', 'senha', 'type_id'];
}
