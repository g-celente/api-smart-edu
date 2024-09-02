<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\Instituicoe;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginRegisterController extends Controller
{

    public function registerInstituicao(Request $request){
        $credentials = $request->validate([
            'nome' => 'required',
            'email' => 'required',
            'senha' => 'required',
        ]);

        $email = Instituicoe::where('email', $request->email)->first();
        
        if ($email) {
            return response()->json([
                'sign-in' => 'false',
                'error' => 'instituição já cadastrada'
            ]);
        }
        else {
            $instituicao = Instituicoe::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => Hash::make($request->senha)
            ]);

            return response()->json(['autenticated' => 'true']);
        }
        

    }

    public function register(Request $request) {
        $credentials = $request->validate([
            'nome' => 'required',
            'email' => 'required|email', 
            'senha' => 'required',
            'type_id' => 'required',
            'instituicao_id' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json([
                'register' => 'false',
                'error' => 'user já registrado'
            ]);
        } 

        $user = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'type_id' => $request->type_id,
            'instituicao_id' => $request->instituicao_id,
        ]);

        return response()->json([
            'authenticate' => 'true',
            'type_id' => $request->type_id,
        ]);
        
            
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email'=> 'required|email',
            'senha' => 'required',
        ]);

        //Hash::check($request->senha, $user->senha)

        $user = User::where('email', $request->email)->first();
        
        if ($user && Hash::check($request->senha, $user->senha)) {

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'authenticated' => true,
                'token' => $token,
                'type_id' => $user->type_id,
                'entity' => 'user',
            ]);
        }
        
        $instituicao = Instituicoe::where('email', $request->email)->first();

        if ($instituicao && Hash::check($request->senha, $instituicao->senha)) {

            $token = JWTAuth::fromUser($instituicao);

            return response()->json([
                'authenticated' => true,
                'token' => $token,
                'entity' => 'instituicao'
            ]);
        }

        return response()->json(['error' => 'Senha ou email inválidos']);
    }   

    public function logout() {
        auth('api')->logout();

        return response()->json(['success' => 'logout efetuado']);
    }
}
