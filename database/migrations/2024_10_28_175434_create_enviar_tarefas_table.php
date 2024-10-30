<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnviarTarefasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enviar_tarefa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aluno_id');
            $table->unsignedBigInteger('tarefa_id');
            $table->text('texto')->nullable(); // Texto opcional
            $table->string('arquivo')->nullable(); // Caminho do arquivo opcional
            $table->string('status')->default('pendente'); // Status da tarefa
            $table->timestamp('data_envio')->nullable();
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('aluno_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tarefa_id')->references('id')->on('tarefas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enviar_tarefas');
    }
}
