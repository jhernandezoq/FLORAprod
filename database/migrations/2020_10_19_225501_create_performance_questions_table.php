<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('dimension_id');
            $table->boolean('leader');
            $table->text('self_text');
            $table->text('text');
            $table->boolean('active');
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('performance_question_types');
            $table->foreign('dimension_id')->references('id')->on('performance_dimensions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_questions');
    }
}
