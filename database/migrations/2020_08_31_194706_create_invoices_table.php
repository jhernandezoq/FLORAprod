<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number', 100);
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('flow_id');
            $table->date('create_date');
            $table->date('due_date');
            $table->decimal('subtotal', 16, 0);
            $table->decimal('iva', 16, 0);
            $table->decimal('total', 16, 0);
            $table->text('concept');
            $table->boolean('priority');
            $table->text('file');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('flow_id')->references('id')->on('flows');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
