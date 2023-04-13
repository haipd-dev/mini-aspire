<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->double('amount', 12,2)->unsigned();
            $table->smallInteger('term')->unsigned();
            $table->enum('status', [1,2,3])->comment('1: Pending, 2: Paid, 3: Pending');
            $table->timestamps();
        });

        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('loan_id');
            $table->double('amount', 12,2)->unsigned();
            $table->date('pay_date',);
            $table->enum('status', [1,2])->comment('1: Pending, 2: Paid');
            $table->foreign('loan_id')->references('id')->on('loans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
        Schema::dropIfExists('loans');
    }
};
