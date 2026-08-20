<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nurses do not log in — they never appear in `users`.
        // In-charges add and manage nurse records for their own unit.
        Schema::create('nurses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('grade')->nullable();
            $table->string('employment_type')->nullable();
            $table->foreignId('home_unit_id')->constrained('units');
            $table->foreignId('current_unit_id')->constrained('units');
            $table->date('date_joined')->nullable();
            $table->enum('status', ['active', 'on_leave', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};
