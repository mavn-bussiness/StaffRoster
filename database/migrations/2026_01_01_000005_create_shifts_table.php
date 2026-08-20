<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roster_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained();
            $table->date('shift_date');
            $table->enum('code', ['D', 'E', 'N', 'O']);
            $table->timestamps();

            $table->unique(['roster_period_id', 'nurse_id', 'shift_date'], 'one_code_per_nurse_per_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
