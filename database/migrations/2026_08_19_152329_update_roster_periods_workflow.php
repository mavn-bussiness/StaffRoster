<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roster_periods', function (Blueprint $table) {
            // Drop old enum and replace with new workflow statuses
            // SQLite doesn't support ALTER COLUMN, so we use a string column
            $table->string('status')->default('draft')->change();

            $table->unsignedInteger('minimum_duty_days')->default(18)->after('status');
            $table->text('review_note')->nullable()->after('minimum_duty_days');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->after('review_note');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('reviewed_by');
            $table->timestamp('submitted_for_review_at')->nullable()->after('approved_by');
            $table->timestamp('forwarded_to_hn_at')->nullable()->after('submitted_for_review_at');
            $table->timestamp('approved_at')->nullable()->after('forwarded_to_hn_at');
        });
    }

    public function down(): void
    {
        Schema::table('roster_periods', function (Blueprint $table) {
            $table->dropColumn([
                'minimum_duty_days',
                'review_note',
                'reviewed_by',
                'approved_by',
                'submitted_for_review_at',
                'forwarded_to_hn_at',
                'approved_at',
            ]);
        });
    }
};
