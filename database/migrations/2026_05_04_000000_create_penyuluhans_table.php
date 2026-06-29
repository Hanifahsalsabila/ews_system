<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyuluhans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->longText('konten')->nullable();
            $table->string('gambar')->nullable(); // path to uploaded image
            $table->enum('kategori', ['gizi', 'imunisasi', 'kebersihan', 'ibu_hamil', 'balita', 'lansia', 'umum'])->default('umum');
            $table->foreignId('penulis_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyuluhans');
    }
};
