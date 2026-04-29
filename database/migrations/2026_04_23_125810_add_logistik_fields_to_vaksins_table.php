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
        Schema::table('vaksins', function (Blueprint $table) {
            $table->string('batch_number')->nullable()->after('jenis');
            $table->integer('stok')->default(0)->after('batch_number');
            $table->date('tanggal_kadaluarsa')->nullable()->after('stok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaksins', function (Blueprint $table) {
            $table->dropColumn(['batch_number', 'stok', 'tanggal_kadaluarsa']);
        });
    }
};
