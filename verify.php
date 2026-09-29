<?php
$files = [
    'wfh_boys_2-to-5-years_zscores.csv' => ['indicator'=>'WFH', 'gender'=>'Male'],
    'wfh_girls_2-to-5-years_zscores.csv' => ['indicator'=>'WFH', 'gender'=>'Female'],
    'wfl_boys_0-to-2-years_zscores.csv' => ['indicator'=>'WFL', 'gender'=>'Male'],
    'wfl_girls_0-to-2-years_zscores.csv' => ['indicator'=>'WFL', 'gender'=>'Female']
];

foreach ($files as $file => $meta) {
    $path = 'storage/app/who_data/' . $file;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines) continue;

    $header = str_getcsv($lines[0]);
    $count = count($lines) - 1;
    $l_idx = array_search('L', $header);
    $m_idx = array_search('M', $header);
    $s_idx = array_search('S', $header);
    $ref_idx = 0; // Height or Length

    $l_vals = [];
    $m_vals = [];
    $s_vals = [];
    $nulls = 0;

    foreach (array_slice($lines, 1) as $line) {
        $row = str_getcsv($line);
        if ($row[$l_idx] == '' || $row[$m_idx] == '' || $row[$s_idx] == '') $nulls++;
        $l_vals[] = (float)$row[$l_idx];
        $m_vals[] = (float)$row[$m_idx];
        $s_vals[] = (float)$row[$s_idx];
    }

    $first = str_getcsv($lines[1]);
    $last = str_getcsv($lines[$count]);
    $second = str_getcsv($lines[2]);

    $interval = (float)$second[$ref_idx] - (float)$first[$ref_idx];
    $duplicates = count($lines) - count(array_unique($lines));

    echo "\nFile: $file\n";
    echo "Indicator: {$meta['indicator']}\n";
    echo "Sex: {$meta['gender']}\n";
    echo "Rows: $count\n";
    echo "Range: {$first[$ref_idx]} - {$last[$ref_idx]}\n";
    echo "Interval: $interval\n";
    echo "Unit: cm\n";
    echo "Columns: " . implode(',', $header) . "\n";
    echo "L available: " . ($l_idx !== false ? 'Yes' : 'No') . "\n";
    echo "M available: " . ($m_idx !== false ? 'Yes' : 'No') . "\n";
    echo "S available: " . ($s_idx !== false ? 'Yes' : 'No') . "\n";
    echo "Nulls: $nulls\n";
    echo "Duplicates: $duplicates\n";
    echo "Min/Max L: " . min($l_vals) . " / " . max($l_vals) . "\n";
    echo "Min/Max M: " . min($m_vals) . " / " . max($m_vals) . "\n";
    echo "Min/Max S: " . min($s_vals) . " / " . max($s_vals) . "\n";
    echo "First 5 rows:\n";
    for ($i = 1; $i <= min(5, $count); $i++) echo $lines[$i] . "\n";
}
