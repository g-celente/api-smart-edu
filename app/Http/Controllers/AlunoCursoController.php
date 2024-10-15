<?php

namespace App\Http\Controllers;

use App\Models\AlunoCurso;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Curso;


class AlunoCursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $instituicaoId = $request->input('authenticated_user_id');

        // Buscar os cursos da instituição
        $cursos = Curso::where('instituicao_id', $instituicaoId)->get();

        if ($cursos->isEmpty()) {
            return response()->json([]); // Retorna um array vazio se não houver cursos
        }

        // Criar uma coleção para armazenar os cursos com alunos
        $cursosComAlunos = collect();

        foreach ($cursos as $curso) {
            // Obter os alunos associados ao curso
            $alunos = User::whereIn('id', function ($query) use ($curso) {
                $query->select('aluno_id')
                    ->from('aluno_cursos')
                    ->where('curso_id', $curso->id);
            })->get();

            // Adicionar os alunos ao curso
            $curso->alunos = $alunos;

            // Adicionar o curso com alunos à coleção
            $cursosComAlunos->push($curso);
        }

        return response()->json($cursosComAlunos);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $instituicaoId = $request->input('authenticated_user_id');

        // Validação dos dados
        $request->validate([ 
            'curso_id' => 'required|integer',
            'alunos_id' => 'required|array'
        ]);

        // Verificar se o curso pertence à instituição
        $curso = Curso::where('instituicao_id', $instituicaoId)->where('id', $request->curso_id)->first();

        if (!$curso) {
            return response()->json(['error' => 'Curso não encontrado ou não pertence à instituição'], 404);
        }

        // Extrair os IDs dos alunos do array de objetos
        $alunosIds = array_map(function($aluno) {
            return $aluno['aluno_id'];
        }, $request->alunos_id);

        try {
            foreach ($alunosIds as $aluno) {

                $validate = User::where('instituicao_id', $instituicaoId)->where('id', $aluno)->where('type_id', 1)->first();
                $validate_create = AlunoCurso::where('aluno_id', $aluno)->where('curso_id', $request->curso_id)->first();
    
                if (!$validate || $validate_create) {
                    return response()->json([
                        'error' => 'aluno não encontrado ou já registrado'
                    ], 404);
                }
    
                AlunoCurso::create([
                    'aluno_id' => $aluno,  // Utiliza o ID de cada aluno encontrado
                    'curso_id' => $request->curso_id
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'não foi possível cadastrar os usuários',
                'erro' => $e->getMessage()
            ], 500);
        }
        
        return response()->json([
            'success' => 'Relações de alunos cadastradas com sucesso no curso.'
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AlunoCurso  $alunoCurso
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $usuarioId = $request->input('authenticated_user_id');

        $usuario = User::where('id', $usuarioId)->first(); 

        if(!$usuario){
            return response()->json(['error' => 'usuario n achado pelo token'], 404);
        }
        // Buscar o curso pelo ID e garantir que ele pertence à instituição autenticada
        $curso = Curso::where('id', $id)->where('instituicao_id', $usuario->instituicao_id)->first();

        // Se o curso não for encontrado ou não pertencer à instituição, retorna um erro 404
        if (!$curso) {
            return response()->json(['error' => 'Curso não encontrado ou não pertence à instituição'], 404);
        }

        // Obter os alunos associados ao curso
        $alunos = User::whereIn('id', function ($query) use ($curso) {
            $query->select('aluno_id')
                ->from('aluno_cursos')
                ->where('curso_id', $curso->id);
        })->get();

        // Adicionar os alunos ao curso
        $curso->alunos = $alunos;

        // Retornar o curso com seus alunos em formato JSON
        return response()->json($alunos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AlunoCurso  $alunoCurso
     * @return \Illuminate\Http\Response
     */
    public function edit(AlunoCurso $alunoCurso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AlunoCurso  $alunoCurso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AlunoCurso $alunoCurso)
    {
        // Se o relacionamento não for encontrado, retornar erro 404
        if (!$alunoCurso) {
            return response()->json([
                'error' => 'Relacionamento não encontrado'
            ], 404);
        }

        // Atualizar o relacionamento com os dados da requisição
        $alunoCurso->update($request->all());

        // Retornar resposta de sucesso
        return response()->json([
            'success' => 'Relacionamento atualizado com sucesso',
            'relacionamento' => $alunoCurso
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AlunoCurso  $alunoCurso
     * @return \Illuminate\Http\Response
     */
    public function destroy(AlunoCurso $alunoCurso)
    {
    
        if (!$alunoCurso) {
            return response()->json([
                'error' => 'Relacionamento não encontrado'
            ], 404);
        }

        $alunoCurso->delete();

        return response()->json([
            'success' => 'Relacionamento deletado com sucesso',
        ], 200);
    }
}
