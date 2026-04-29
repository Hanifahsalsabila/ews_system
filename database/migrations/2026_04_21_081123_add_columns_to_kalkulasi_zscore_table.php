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
            $table->string('status_stunting')->nullable()->after('id');
            $table->string('status_gizi')->nullable()->after('status_stunting');
            $table->float('zscore_bb_u')->nullable()->after('status_gizi');
            $table->float('zscore_tb_u')->nullable()->after('zscore_bb_u');
            $table->float('zscore_bb_tb')->nullable()->after('zscore_tb_u');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kalkulasi_zscore', function (Blueprint $table) {
            $table->dropColumn(['status_stunting', 'status_gizi', 'zscore_bb_u', 'zscore_tb_u', 'zscore_bb_tb']);
        });
    }
};
