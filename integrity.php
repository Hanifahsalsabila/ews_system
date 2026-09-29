<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==================================================\n";
echo "G. DATABASE INTEGRITY CHECK\n";
echo "==================================================\n";

$issues = [];

// 1. Duplicate check
$duplicates = DB::table('who_zscore_lms')
    ->select('standard', 'indicator', 'gender', 'ref_value', DB::raw('count(*) as c'))
    ->whereIn('indicator', ['WFL', 'WFH'])
    ->groupBy('standard', 'indicator', 'gender', 'ref_value')
    ->having('c', '>', 1)
    ->get();
if ($duplicates->count() > 0) $issues[] = "Duplicates found: " . $duplicates->count();

// 2. NULL checks
$nulls = DB::table('who_zscore_lms')
    ->whereIn('indicator', ['WFL', 'WFH'])
    ->where(function($q) {
        $q->whereNull('l')->orWhereNull('m')->orWhereNull('s')
          ->orWhereNull('indicator')->orWhereNull('gender')
          ->orWhereNull('ref_value')->orWhereNull('unit');
    })->count();
if ($nulls > 0) $issues[] = "NULLs found: $nulls";

// 3. Ref Value out of range
$outOfRangeWFL = DB::table('who_zscore_lms')
    ->where('indicator', 'WFL')
    ->where(function($q) { $q->where('ref_value', '<', 45)->orWhere('ref_value', '>', 110); })
    ->count();
if ($outOfRangeWFL > 0) $issues[] = "WFL ref_value out of range (45-110): $outOfRangeWFL";

$outOfRangeWFH = DB::table('who_zscore_lms')
    ->where('indicator', 'WFH')
    ->where(function($q) { $q->where('ref_value', '<', 65)->orWhere('ref_value', '>', 120); })
    ->count();
if ($outOfRangeWFH > 0) $issues[] = "WFH ref_value out of range (65-120): $outOfRangeWFH";

// 4. Invalid gender
$invalidGender = DB::table('who_zscore_lms')
    ->whereIn('indicator', ['WFL', 'WFH'])
    ->whereNotIn('gender', ['L', 'P'])
    ->count();
if ($invalidGender > 0) $issues[] = "Invalid gender found: $invalidGender";

// 5. Invalid unit
$invalidUnit = DB::table('who_zscore_lms')
    ->whereIn('indicator', ['WFL', 'WFH'])
    ->where('unit', '!=', 'cm')
    ->count();
if ($invalidUnit > 0) $issues[] = "Invalid unit found: $invalidUnit";

if (empty($issues)) echo "All integrity checks PASSED.\n\n";
else {
    foreach ($issues as $issue) echo "- $issue\n";
    echo "\n";
}

$files = [
    'WFL Boys' => ['wfl_boys_0-to-2-years_zscores.csv', 'WFL', 'L'],
    'WFL Girls' => ['wfl_girls_0-to-2-years_zscores.csv', 'WFL', 'P'],
    'WFH Boys' => ['wfh_boys_2-to-5-years_zscores.csv', 'WFH', 'L'],
    'WFH Girls' => ['wfh_girls_2-to-5-years_zscores.csv', 'WFH', 'P']
];

echo "| Dataset | Source Rows | DB Rows | Difference |\n";
echo "|---|---:|---:|---:|\n";

foreach ($files as $name => $meta) {
    $path = 'storage/app/who_data/' . $meta[0];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $sourceCount = count($lines) - 1;
    
    $dbCount = DB::table('who_zscore_lms')
        ->where('indicator', $meta[1])
        ->where('gender', $meta[2])
        ->count();
        
    $diff = abs($sourceCount - $dbCount);
    echo "| $name | $sourceCount | $dbCount | $diff |\n";
}

echo "\n==================================================\n";
echo "H. SPOT CHECK NILAI LMS\n";
echo "==================================================\n";
echo "| Dataset | Ref | WHO L | DB L | WHO M | DB M | WHO S | DB S | Match |\n";
echo "|---|---:|---:|---:|---:|---:|---:|---:|---|\n";

foreach ($files as $name => $meta) {
    $path = 'storage/app/who_data/' . $meta[0];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $header = str_getcsv($lines[0]);
    $l_idx = array_search('L', $header);
    $m_idx = array_search('M', $header);
    $s_idx = array_search('S', $header);
    
    $rowCount = count($lines) - 1;
    // 5 start, 5 mid, 5 end
    $spots = array_merge(
        range(1, 5),
        range((int)floor($rowCount/2)-2, (int)floor($rowCount/2)+2),
        range($rowCount-4, $rowCount)
    );
    
    foreach ($spots as $i) {
        if (!isset($lines[$i])) continue;
        $row = str_getcsv($lines[$i]);
        $ref = (float)$row[0];
        $wl = (float)$row[$l_idx];
        $wm = (float)$row[$m_idx];
        $ws = (float)$row[$s_idx];
        
        $dbRow = DB::table('who_zscore_lms')
            ->where('indicator', $meta[1])
            ->where('gender', $meta[2])
            ->where('ref_value', $ref)
            ->first();
            
        if (!$dbRow) {
            echo "| $name | $ref | $wl | - | $wm | - | $ws | - | NO |\n";
            continue;
        }
        
        $dl = (float)$dbRow->l;
        $dm = (float)$dbRow->m;
        $ds = (float)$dbRow->s;
        
        $match = (abs($wl - $dl) < 0.00001 && abs($wm - $dm) < 0.00001 && abs($ws - $ds) < 0.00001) ? 'YES' : 'NO';
        echo "| $name | $ref | $wl | $dl | $wm | $dm | $ws | $ds | $match |\n";
    }
}
