<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMaterialAtividade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_atividade', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 100);
            $table->text('material');
            $table->unsignedBigInteger('tarefa_id');
            $table->timestamps();

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
        Schema::dropIfExists('material_complementars');
    }
}
