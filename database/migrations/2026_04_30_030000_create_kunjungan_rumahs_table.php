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
        Schema::create('kunjungan_rumahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->foreignId('bidan_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('tipe', ['gizi_buruk', 'stunting', 'kek', '2t', 'lainnya'])->default('2t');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang');
            $table->date('tanggal_rencana')->nullable();
            $table->date('tanggal_kunjungan')->nullable();
            $table->enum('status', ['dijadwalkan', 'selesai', 'batal', 'ditunda'])->default('dijadwalkan');
            $table->text('catatan_sebelum')->nullable();
            $table->text('catatan_hasil')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan_rumahs');
    }
};
