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
        Schema::table('sesi_posyandu', function (Blueprint $table) {
            $table->date('tanggal')->after('id');
            $table->string('nama_sesi')->after('tanggal');
            $table->string('status')->default('aktif')->after('nama_sesi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_posyandu', function (Blueprint $table) {
            $table->dropColumn(['tanggal', 'nama_sesi', 'status']);
        });
    }
};
