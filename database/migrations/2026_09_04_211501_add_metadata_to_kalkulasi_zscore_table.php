<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kalkulasi_zscore', function (Blueprint $table) {
            $table->integer('age_in_days')->nullable()->after('zscore_bb_tb');
            $table->string('measurement_type', 20)->nullable()->after('age_in_days');
            $table->string('status_bb_tb', 100)->nullable()->after('status_stunting');
        });
    }

    public function down(): void
    {
        Schema::table('kalkulasi_zscore', function (Blueprint $table) {
            $table->dropColumn(['age_in_days', 'measurement_type', 'status_bb_tb']);
        });
    }
};
