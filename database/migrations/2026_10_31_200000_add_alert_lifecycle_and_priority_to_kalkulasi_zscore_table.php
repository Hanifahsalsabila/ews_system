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
        Schema::table('kalkulasi_zscore', function (Blueprint $table) {
            $table->decimal('priority_score', 6, 2)->default(0.00)->after('ews_message');
            $table->text('priority_details')->nullable()->after('priority_score');
            $table->enum('alert_status', ['pending', 'in_progress', 'resolved'])->default('pending')->after('priority_details');
            $table->timestamp('resolved_at')->nullable()->after('alert_status');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete()->after('resolved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kalkulasi_zscore', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn(['priority_score', 'priority_details', 'alert_status', 'resolved_at', 'resolved_by']);
        });
    }
};
