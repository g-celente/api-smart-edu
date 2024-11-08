<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginRegisterController extends Controller
{

    public function registerInstituicao(Request $request)
    {
        $credentials = $request->validate([
            'nome' => 'required',
            'email' => 'required',
            'senha' => 'required',
            'user_img' => ''
        ]);
    
        try {
            $existingEmail = User::where('email', $request->email)->first();
            
            if ($existingEmail) {
                return response()->json([
                    'sign-in' => false,
                    'error' => 'Instituição já cadastrada'
                ], 409); 
            }
    
            
            $instituicao = User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => Hash::make($request->senha),
                'user_img' => $request->user_img,
                'type_id' => 3
            ]);

            $token = JWTAuth::fromUser($instituicao);
    
            return response()->json([
                'authenticated' => true,
                'token' => $token,
                'user' => [
                    'id' => $instituicao->id,
                    'email' => $instituicao->email,
                    'type_id' => $instituicao->type_id,
                ],
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocorreu um erro ao registrar a instituição, tente novamente.'
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'senha' => 'required|min:6',
            'type_id' => 'required|integer',
        ]);

        try {
            $existingUser = User::where('email', $request->email)->first();
            $instituicao_id = $request->input('authenticated_user_id'); 

            if ($existingUser) {
                return response()->json([
                    'register' => false,
                    'error' => 'Usuário já registrado'
                ], 409);
            }

            $user = User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => Hash::make($request->senha),
                'type_id' => $request->type_id,
                'instituicao_id' => $instituicao_id, 
            ]);

            return response()->json([
                'register' => true,
                'type_id' => $request->type_id,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'instituicao_id' => $user->instituicao_id
                ]
            ], 201); 

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocorreu um erro ao registrar o usuário, tente novamente.'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'senha' => 'required|min:6',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->senha, $user->senha)) {

                $token = JWTAuth::fromUser($user);

                return response()->json([
                    'authenticated' => true,
                    'token' => $token,
                    'user' => [
                        "id" => $user->id,
                        "email" => $user->email,
                        "type_id" => $user->type_id
                    ],
                ], 200);
            }

            return response()->json([
                'error' => 'Credenciais inválidas'
            ], 403);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocorreu um erro no processo de login, tente novamente mais tarde.'
            ], 500);
        }
    }  

    public function logout() {
        auth('api')->logout();

        return response()->json(['success' => 'logout efetuado']);
    }

    public function update_password(Request $request) {

        $user = User::where('email' , $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'email não encontrado'
            ], 404);
        }

        try {
            $user->update([
                'senha' => Hash::make($request->senha),
            ]);

            return response()->json([
                'success' => 'senha alterada com sucesso'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'problema ao tentar alterar senha',
                'erro' => $e
            ], 500);
        }
    }

    public function recoverPassword(Request $request)
    {
        $email = $request->input('email');
        $novaSenha = $request->input('nova_senha');
        
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'Conta não encontrada'], 404);
        }

        $user->update([
            'senha' => Hash::make($novaSenha),
        ]);

        return response()->json($user);
    }
}

