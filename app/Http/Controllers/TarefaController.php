<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\Tarefa;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\EnviarTarefa;
use App\Models\AlunoCurso;
use Symfony\Component\HttpKernel\Event\RequestEvent;

use function PHPUnit\Framework\isEmpty;

class TarefaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Id = $request->input('authenticated_user_id');
       
        $tarefas = Tarefa::where('professor_id', $Id)->get();

        if ($tarefas->isEmpty()) {
            return response()->json([]);
        }

        return response()->json($tarefas, 200);
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
        $Id = $request->input('authenticated_user_id');
        $instituicao_id = $request->input('authenticated_instituicao_id');

        $credentials = $request->validate([
            'nome' => 'required',
            'descricao' => 'required', 
            'disciplina_id' => 'required | integer',
            'data_entrega' => 'required | date'
            
        ]);
        
        $cursos = Curso::where('instituicao_id', $instituicao_id)->get();
        $cursoIds = $cursos->pluck('id');
        $disciplinas = Disciplina::whereIn('curso_id', $cursoIds)
                                ->where('id', $request->disciplina_id)
                                ->first();

        if (!$disciplinas) {
            return response()->json([
                'error' => 'Nenhuma disciplina encontrada'
            ], 404);
        }

        $tarefa = Tarefa::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'professor_id' => $Id,
            'disciplina_id' => $request->disciplina_id,
            'data_entrega' => $request->data_entrega
        ]);

        return response()->json([
            'sucess' => 'tarefa cadastrada',
            'tarefa' => $tarefa,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function show(Tarefa $tarefa, Request $request)
    {
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::where('professor_id', $id)->where('id', $tarefa)->get();

        if ($tarefa) {
            return response()->json($tarefa, 200);
        }

        return response()->json([
            'error' => 'tarefa não encontrada'
        ], 404);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarefa $tarefa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarefa $tarefa)
    {
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::where('professor_id', $id)->where('id', $tarefa)->get();

        if ($tarefa) {
            $tarefa->update($request->all());
            return response()->json([
                'sucess' => 'tarefa atualizada',
                'tarefa' => $tarefa
            ], 201);
        }

        return response()->json([
            'error' => 'tarefa não encontrada'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarefa $tarefa, Request $request)
    {
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::where('professor_id', $id)->where('id', $tarefa)->first();

        if ($tarefa) {
            $tarefa->delete();
            return response()->json([
                'success' => 'tarefa deletada'
            ], 200);
        }

        return response()->json([
            'error' => 'tarefa não encontrada'
        ], 404);
        
    }

    public function getTaskById(Request $request, $tarefa_id)
    {
        // Agora $request está disponível
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::find($tarefa_id);

        if ($tarefa) {
            return response()->json($tarefa);
        } else {
            return response()->json(['error' => 'Tarefa não encontrada'], 404);
        }
    }


    public function deleteTaskById(Request $request, $tarefa_id)
    {
        // Agora $request está disponível
        $id = $request->input('authenticated_user_id');
        $tarefa = Tarefa::find($tarefa_id);

        if ($tarefa) {
            $tarefa->delete();
            return response()->json([
                'success' => 'tarefa deletada'
            ], 200);
        } else {
            return response()->json(['error' => 'Tarefa não encontrada'], 404);
        }
    }

    public function getTarefaDisciplina(Request $request, $disciplina_id) {

        $tarefas = Tarefa::where('disciplina_id', $disciplina_id)->get();

        if ($tarefas->isEmpty()) {
            return response()->json(['error' => 'nenhuma tarefa encontrada'], 404);
        }

        return response()->json($tarefas, 200);
    } 

    public function enviarTarefa(Request $request) {

        $aluno_id = $request->input('authenticated_user_id');

        $credentials = $request->validate([
            'tarefa_id' => 'required',
            'texto' => 'nullable|string',
            'arquivo' => 'nullable|file|mimes:pdf,docx,png,jpeg|max:10240'
        ]);


        $tarefaEnviada = EnviarTarefa::where('aluno_id', $aluno_id)
            ->where('tarefa_id', $request->tarefa_id)
            ->first();

        if ($tarefaEnviada) {
            return response()->json(['error' => 'Você já enviou essa tarefa'], 400);
        }

        $arquivoUrl = null;

        if ($request->hasFile('arquivo')) {
            $arquivo = $request->file('arquivo');
            $arquivoUrl = $arquivo->store('tarefas_enviadas', 'public'); // Salvar arquivo no storage
        }

        try {
            // Criar envio da tarefa
            $tarefaEnviada = EnviarTarefa::create([
                'aluno_id' => $aluno_id,
                'tarefa_id' => $request->tarefa_id,
                'texto' => $request->texto, // Texto enviado pelo aluno
                'arquivo' => $arquivoUrl, // URL do arquivo
                'status' => 'concluida', // Marcar como concluída
                'data_envio' => now(), // Data de envio
            ]);

            return response()->json([
                'success' => 'Tarefa enviada com sucesso',
                'tarefa_enviada' => $tarefaEnviada
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getStatusTarefa(Request $request, $tarefa_id) {

        $aluno_id = $request->input('authenticated_user_id');

        $tarefaEnviada = EnviarTarefa::where('aluno_id', $aluno_id)
            ->where('tarefa_id', $tarefa_id)
            ->first();

        if ($tarefaEnviada) {
            return response()->json($tarefaEnviada);
        } else {
            return response()->json(null);
        }
    }

    public function tarefasAlunos(Request $request, $disciplina_id)
    {
        // Pega o ID do aluno autenticado
        $aluno_id = $request->input('authenticated_user_id');
        
        // Busca todas as tarefas associadas à disciplina
        $tarefasDisciplina = Tarefa::where('disciplina_id', $disciplina_id)->get();

        if ($tarefasDisciplina->isEmpty()) {
            return response()->json([
                'error' => 'Nenhuma tarefa cadastrada para essa disciplina.'
            ], 404);
        }

        // Prepara arrays para armazenar tarefas concluídas e não concluídas
        $tarefasConcluidas = [];
        $tarefasNaoConcluidas = [];

        foreach ($tarefasDisciplina as $tarefa) {
            // Verifica se o aluno já enviou a tarefa e se está concluída
            $enviada = EnviarTarefa::where('aluno_id', $aluno_id)
                                ->where('tarefa_id', $tarefa->id)
                                ->first();
            
            if ($enviada && $enviada->status == 'concluida') {
                $tarefasConcluidas[] = $tarefa;
            } else {
                $tarefasNaoConcluidas[] = $tarefa;
            }
        }

        // Retorna as tarefas divididas em concluídas e não concluídas
        return response()->json([
            'tarefasConcluidas' => $tarefasConcluidas,
            'tarefasNaoConcluidas' => $tarefasNaoConcluidas
        ], 200);
    }

    public function tarefasConcluidas(Request $request){
        $aluno_id = $request->input('authenticated_user_id');

        $tarefasAluno = EnviarTarefa::where('aluno_id', $aluno_id)->where('status', 'concluida')->get();

        if ($tarefasAluno->isEmpty()) {
            return response()->json([
                'error' => 'nenhum tarefa enviada'
            ], 404);
        }

        return response()->json($tarefasAluno, 200);
    }

    public function statusTarefasAluno(Request $request)
    {
        $aluno_id = $request->input('authenticated_user_id');

        // Obtém todos os IDs dos cursos que o aluno está matriculado
        $curso_aluno = AlunoCurso::where('aluno_id', $aluno_id)->pluck('curso_id');
        
        // Busca todas as disciplinas relacionadas aos cursos do aluno
        $disciplinas = Disciplina::whereIn('curso_id', $curso_aluno)->pluck('id');
        
        // Busca todas as tarefas relacionadas às disciplinas
        $tarefas = Tarefa::whereIn('disciplina_id', $disciplinas)->get();

        $tarefasConcluidas = [];
        $tarefasNaoConcluidas = [];

        // Itera sobre as tarefas para verificar o status de envio
        foreach ($tarefas as $tarefa) {
            // Verifica se o aluno já enviou a tarefa e se está concluída
            $enviada = EnviarTarefa::where('aluno_id', $aluno_id)
                                    ->where('tarefa_id', $tarefa->id)
                                    ->first();
            
            if ($enviada && $enviada->status == 'concluida') {
                $tarefasConcluidas[] = $tarefa;
            } else {
                $tarefasNaoConcluidas[] = $tarefa;
            }
        }

        // Verifica se o aluno não possui tarefas cadastradas
        if ($tarefas->isEmpty()) {
            return response()->json([
                'error' => 'Nenhuma tarefa cadastrada para este aluno'
            ], 404);
        }

        // Retorna as tarefas divididas em concluídas e não concluídas
        return response()->json([
            'tarefasConcluidas' => $tarefasConcluidas,
            'tarefasNaoConcluidas' => $tarefasNaoConcluidas
        ], 200);
    }

    public function getEnvioTarefaById(Request $request, $envio_id) {
        return response()->json([$envio_tarefa = EnviarTarefa::where('id', $envio_id)->first()]);
    }

}
