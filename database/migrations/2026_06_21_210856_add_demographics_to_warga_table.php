<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('nik');
            $table->date('tanggal_lahir')->nullable()->after('jenis_kelamin');
        });

        // Migrate existing data from balita table to warga table
        if (Schema::hasTable('balita')) {
            $balitas = DB::table('balita')->get();
            foreach ($balitas as $balita) {
                DB::table('warga')
                    ->where('id', $balita->warga_id)
                    ->update([
                        'jenis_kelamin' => $balita->jenis_kelamin,
                        'tanggal_lahir' => $balita->tanggal_lahir,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'tanggal_lahir']);
        });
    }
};
