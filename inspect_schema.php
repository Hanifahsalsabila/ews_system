<?php
// Quick schema inspection script - run via: php inspect_schema.php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== COLUMNS ===\n";
$cols = DB::select('SHOW COLUMNS FROM who_zscore_lms');
foreach ($cols as $c) {
    echo sprintf("%-20s | %-25s | NULL:%-3s | Default: %s\n",
        $c->Field, $c->Type, $c->Null, $c->Default ?? 'NULL');
}

echo "\n=== INDEXES ===\n";
$indexes = DB::select('SHOW INDEX FROM who_zscore_lms');
foreach ($indexes as $i) {
    echo sprintf("Key: %-35s | Col: %-20s | Unique: %s | Seq: %s\n",
        $i->Key_name, $i->Column_name, $i->Non_unique == 0 ? 'YES' : 'NO', $i->Seq_in_index);
}

echo "\n=== ROW COUNT ===\n";
$count = DB::table('who_zscore_lms')->count();
echo "Total rows: $count\n";

echo "\n=== DISTINCT INDICATORS ===\n";
$inds = DB::table('who_zscore_lms')->select('indicator', 'gender', 'unit')->selectRaw('COUNT(*) as cnt, MIN(ref_value) as min_ref, MAX(ref_value) as max_ref')->groupBy('indicator', 'gender', 'unit')->get();
foreach ($inds as $r) {
    echo sprintf("%-8s | %-3s | %-12s | rows: %-5d | min: %-8s | max: %s\n",
        $r->indicator, $r->gender, $r->unit, $r->cnt, $r->min_ref, $r->max_ref);
}

echo "\n=== SOURCE_VERSION COLUMN CHECK ===\n";
$hasSourceVersion = collect($cols)->contains('Field', 'source_version');
echo "source_version column exists: " . ($hasSourceVersion ? 'YES' : 'NO') . "\n";
