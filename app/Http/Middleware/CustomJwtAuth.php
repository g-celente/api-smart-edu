<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Instituicoe;

class CustomJwtAuth
{
    public function handle($request, Closure $next)
    {
        // Tente obter o token JWT
        $token = JWTAuth::parseToken()->getToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided.'], 401);
        }

        try {
            // Tente decodificar o token
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload->get('id'); // Obtenha o ID do usuário

            // Tente buscar o usuário na tabela `users`
            $user = User::find($userId);

            if ($user) {
                Auth::setUser($user);
                return $next($request);
            }

            // Se não encontrar o usuário, tente verificar a tabela `instituicao`
            $instituicao = Instituicoe::where('id', $userId)->first();

            if ($instituicao) {
                // Aqui, você pode configurar a autenticação específica para instituições
                Auth::guard('instituicao')->setUser($instituicao);
                return $next($request);
            }

            return response()->json(['error' => 'Unauthenticated.'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token is invalid or expired.'], 401);
        }
    }
}