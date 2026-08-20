<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('off_day_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nurse_id')->constrained();
            $table->foreignId('roster_period_id')->constrained()->cascadeOnDelete();
            $table->date('requested_date');
            $table->string('reason')->nullable();
            $table->date('written_request_date')->nullable();
            $table->enum('status', ['requested', 'granted', 'denied'])->default('requested');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('off_day_requests');
    }
};
