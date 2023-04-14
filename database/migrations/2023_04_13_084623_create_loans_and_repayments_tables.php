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
            $table->double('amount', 12, 2)->unsigned();
            $table->string('currency', 10)->default('USD');
            $table->smallInteger('term')->unsigned();
            $table->date('submit_date');
            $table->tinyInteger('status')->comment('1: Pending, 2: Paid, 3: Pending, 4: Partial Paid, 5: Rejected')->default(1);
            $table->timestamps();
            $table->index('user_id', 'loans_user_id_index');
        });

        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('loan_id')->unsigned();
            $table->double('amount', 12, 2)->unsigned();
            $table->string('currency', 10)->default('USD');
            $table->double('paid_amount', 12, 2)->default(0);
            $table->date('pay_date');
            $table->tinyInteger('status')->comment('1: Pending, 2: Paid, 3: Auto Paid')->default(1);
            $table->foreign('loan_id')->references('id')->on('loans')->cascadeOnDelete();
            $table->index('loan_id', 'loan_repayments_loan_id_index');
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
