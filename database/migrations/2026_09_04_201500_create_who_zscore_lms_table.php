<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('who_zscore_lms', function (Blueprint $table) {
            $table->id();
            $table->string('standard', 20)->default('WHO2006');
            $table->string('indicator', 20); // WAZ, HAZ, WFL, WFH, BBU, TBU, BBTB
            $table->string('gender', 5); // L/P or M/F
            $table->decimal('ref_value', 8, 2); // Age in months or Length/Height in cm
            $table->string('unit', 20)->default('age_months'); // age_months, cm
            $table->float('l', 8, 4);
            $table->float('m', 8, 4);
            $table->float('s', 8, 5);
            $table->timestamps();

            $table->unique(['standard', 'indicator', 'gender', 'ref_value'], 'who_lms_unique');
            $table->index(['standard', 'indicator', 'gender', 'ref_value'], 'who_lms_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('who_zscore_lms');
    }
};

