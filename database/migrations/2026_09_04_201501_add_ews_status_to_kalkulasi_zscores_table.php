<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kalkulasi_zscore', function (Blueprint $table) {
            $table->enum('ews_status', ['normal', 'waspada', 'risiko_tinggi', 'bahaya'])->default('normal')->after('status_stunting');
            $table->text('ews_message')->nullable()->after('ews_status');
        });
    }

    public function down(): void
    {
        Schema::table('kalkulasi_zscore', function (Blueprint $table) {
            $table->dropColumn(['ews_status', 'ews_message']);
        });
    }
};
