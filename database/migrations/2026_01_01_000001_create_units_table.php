<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // e.g. Maternity, Neonatal ICU, General Ward
            $table->string('code')->unique();        // short code, e.g. MAT, NICU
            $table->boolean('has_evening_shift')->default(true);
            $table->boolean('has_night_shift')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
