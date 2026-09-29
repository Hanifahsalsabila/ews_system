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
            $table->double('raw_zscore_bb_u')->nullable()->after('zscore_bb_u');
            $table->double('raw_zscore_tb_u')->nullable()->after('zscore_tb_u');
            $table->double('raw_zscore_bb_tb')->nullable()->after('zscore_bb_tb');
            
            $table->boolean('flag_bb_u')->nullable()->after('status_gizi');
            $table->boolean('flag_tb_u')->nullable()->after('status_stunting');
            $table->boolean('flag_bb_tb')->nullable()->after('status_bb_tb');

            $table->string('source_version')->nullable()->after('measurement_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kalkulasi_zscore', function (Blueprint $table) {
            $table->dropColumn([
                'raw_zscore_bb_u', 'raw_zscore_tb_u', 'raw_zscore_bb_tb',
                'flag_bb_u', 'flag_tb_u', 'flag_bb_tb',
                'source_version'
            ]);
        });
    }
};
