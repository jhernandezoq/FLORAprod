<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('evaluator_id');
            $table->unsignedBigInteger('evaluated_id');
            $table->decimal('value', 8, 2);
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('performance_questions');
            $table->foreign('evaluator_id')->references('id')->on('users');
            $table->foreign('evaluated_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_responses');
    }
}
