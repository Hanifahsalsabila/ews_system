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
        Schema::table('who_zscore_lms', function (Blueprint $table) {
            $table->string('source_version', 50)->default('legacy')->after('unit');
            $table->char('lorh', 1)->nullable()->after('s'); // L or H

            // Drop existing unique index and lookup index
            $table->dropUnique('who_lms_unique');
            $table->dropIndex('who_lms_lookup_idx');

            // Recreate with source_version
            $table->unique(['standard', 'indicator', 'gender', 'ref_value', 'unit', 'source_version'], 'who_lms_unique');
            $table->index(['standard', 'indicator', 'gender', 'ref_value', 'unit', 'source_version'], 'who_lms_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('who_zscore_lms', function (Blueprint $table) {
            $table->dropUnique('who_lms_unique');
            $table->dropIndex('who_lms_lookup_idx');

            $table->dropColumn('source_version');
            $table->dropColumn('lorh');

            // Recreate original indexes
            $table->unique(['standard', 'indicator', 'gender', 'ref_value'], 'who_lms_unique');
            $table->index(['standard', 'indicator', 'gender', 'ref_value'], 'who_lms_lookup_idx');
        });
    }
};
