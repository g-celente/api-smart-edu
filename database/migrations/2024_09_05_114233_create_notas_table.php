<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->float('nota', 8, 2);
            $table->unsignedBigInteger('aluno_id');
            $table->unsignedBigInteger('tarefa_id');
            $table->timestamps();

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
        Schema::dropIfExists('notas');
    }
}
