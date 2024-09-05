<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Institution;

class VerifyTypeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Obtenha o usuário autenticado usando JWT
            $user = JWTAuth::parseToken()->authenticate();
            
            // Verifique as informações do usuário na tabela 'users'
            if ($user->type_id == 3) {
                return $next($request);
            }

            // Caso contrário, negue o acesso
            return response()->json(['message' => 'Access Denied.'], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}