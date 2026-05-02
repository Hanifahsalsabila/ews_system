<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan', function (Blueprint $table) {
            // Balita specific
            $table->boolean('vitamin_a')->default(false)->after('catatan');
            $table->boolean('obat_cacing')->default(false)->after('vitamin_a');
            $table->boolean('asi_eksklusif')->default(false)->after('obat_cacing');
            $table->boolean('pmt')->default(false)->after('asi_eksklusif');

            // Ibu Hamil specific
            $table->boolean('suplementasi_fe')->default(false)->after('pmt');
            $table->string('status_tt')->nullable()->after('suplementasi_fe');

            // Lansia specific
            $table->string('gula_darah')->nullable()->after('status_tt');
            $table->string('kolesterol')->nullable()->after('gula_darah');
            $table->string('asam_urat')->nullable()->after('kolesterol');
            $table->decimal('imt', 5, 2)->nullable()->after('asam_urat');

            // Cohort type identifier
            $table->string('tipe_kohort')->nullable()->after('imt');

            // TFU & DJJ already exist, adding them for Ibu Hamil context
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan', function (Blueprint $table) {
            $table->dropColumn([
                'vitamin_a', 'obat_cacing', 'asi_eksklusif', 'pmt',
                'suplementasi_fe', 'status_tt',
                'gula_darah', 'kolesterol', 'asam_urat', 'imt',
                'tipe_kohort'
            ]);
        });
    }
};
