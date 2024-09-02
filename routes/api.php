<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//ROTAS PARA REGISTRO E LOGIN INSTITUIÇÃO
Route::post('/register', 'App\Http\Controllers\LoginRegisterController@registerInstituicao');
Route::post('/login', 'App\Http\Controllers\LoginRegisterController@login');
Route::post('/logout', 'App\Http\Controllers\LoginRegisterController@logout')->middleware('jwt.auth');

//ROTAS PARA CRUD DE ALUNOS E PROFESSORES

//ROTA DE REGISTO DE PROFESSOR E ALUNOS
Route::post('/register', 'App\Http\Controllers\LoginRegisterController@register')->middleware('jwt.auth');

//ROTA CRUD DE PROFESSORES E ALUNOS
Route::apiResource('/alunos', 'App\Http\Controllers\UserController')->middleware('jwt.auth');
Route::apiResource('/professores','App\Http\Controllers\UserController')->middleware('jwt.auth');

//ROTA CRUD DE CURSOS E DISCIPLINAS
Route::apiResource('/cursos', 'App\Http\Controllers\CursoController')->middleware('jwt.auth');
Route::apiResource('/disciplinas', 'App\Http\Controllers\DisciplinaController')->middleware('jwt.auth');



/*

//MÉTODOS PARA INSTITUIÇÃO
2. Gerenciamento de Usuários
GET /usuarios: Listar todos os usuários (apenas para administradores).
POST /usuarios: Criar um novo usuário (aluno ou professor).
GET /usuarios/{id}: Obter informações de um usuário específico.
PUT /usuarios/{id}: Atualizar dados de um usuário.
DELETE /usuarios/{id}: Desativar ou remover um usuário.
3. Gerenciamento de Cursos
GET /cursos: Listar todos os cursos.
POST /cursos: Criar um novo curso.
GET /cursos/{id}: Obter informações de um curso específico.
PUT /cursos/{id}: Atualizar dados de um curso.
DELETE /cursos/{id}: Desativar um curso.
4. Gerenciamento de Disciplinas
GET /disciplinas: Listar todas as disciplinas.
POST /disciplinas: Criar uma nova disciplina.
GET /disciplinas/{id}: Obter informações de uma disciplina específica.
PUT /disciplinas/{id}: Atualizar dados de uma disciplina.
DELETE /disciplinas/{id}: Remover uma disciplina.
5. Gerenciamento de Matrículas
POST /disciplinas/{id}/matriculas: Matricular um aluno em uma disciplina.
DELETE /disciplinas/{id}/matriculas/{aluno_id}: Desmatricular um aluno de uma disciplina.


//ROTAS PARA ALUNOS

//ROTAS PARA PROFESSORES
6. Gerenciamento de Tarefas
GET /disciplinas/{id}/tarefas: Listar todas as tarefas de uma disciplina.
POST /disciplinas/{id}/tarefas: Criar uma nova tarefa para uma disciplina.
GET /tarefas/{id}: Obter informações de uma tarefa específica.
PUT /tarefas/{id}: Atualizar uma tarefa.
DELETE /tarefas/{id}: Remover uma tarefa.
7. Gerenciamento de Materiais Complementares
GET /tarefas/{id}/materiais: Listar todos os materiais complementares de uma tarefa.
POST /tarefas/{id}/materiais: Adicionar um novo material complementar.
GET /materiais/{id}: Obter informações de um material específico.
DELETE /materiais/{id}: Remover um material complementar.
8. Gerenciamento de Desempenho e Notas
GET /alunos/{id}/desempenho: Obter o desempenho de um aluno em suas disciplinas.
POST /disciplinas/{id}/notas: Inserir ou atualizar notas de um aluno.
GET /disciplinas/{id}/notas: Listar notas dos alunos em uma disciplina.
9. Avisos e Comunicação
GET /avisos: Listar todos os avisos.
POST /avisos: Criar um novo aviso.
GET /avisos/{id}: Obter detalhes de um aviso específico.
DELETE /avisos/{id}: Remover um aviso.

GET /disciplinas/{disciplina_id}/tarefas/{tarefa_id}/notas: Listar todas as notas de uma tarefa específica.
POST /disciplinas/{disciplina_id}/tarefas/{tarefa_id}/notas: Criar uma nova nota para um aluno em uma tarefa.
PUT /disciplinas/{disciplina_id}/tarefas/{tarefa_id}/notas/{nota_id}: Atualizar uma nota existente.
DELETE /disciplinas/{disciplina_id}/tarefas/{tarefa_id}/notas/{nota_id}: Remover uma nota.
*/


//Route::post('/login', 'App\Http\Controllers\LoginController@login');