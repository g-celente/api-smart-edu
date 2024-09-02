<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CredAuth
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
        // Verifique se o token de autenticação está presente nos cabeçalhos
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['message' => 'Token não fornecido'], 401);
        }

        // Verifique se o token é válido
        // Aqui você pode decodificar o token e verificar o usuário no banco de dados
        // Exemplo básico:
        // Usando um método fictício para verificar o token
        $user = $this->getUserFromToken($token);

        if (!$user) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        // Se o token for válido, prossiga com a solicitação
        return $next($request);
    }

    // Função fictícia para obter o usuário com base no token
    protected function getUserFromToken($token)
    {
        // Aqui você pode decodificar o token e buscar o usuário no banco de dados
        // Para fins de exemplo, vamos supor que o token é o ID do usuário
        return \App\Models\User::find($token);
    }
}
