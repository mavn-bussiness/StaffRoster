<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roster_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['draft', 'agreed', 'notified', 'submitted'])->default('draft');
            $table->timestamp('agreed_at')->nullable();
            $table->timestamp('submitted_to_moh_at')->nullable();
            $table->timestamps();

            $table->unique(['unit_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roster_periods');
    }
};
