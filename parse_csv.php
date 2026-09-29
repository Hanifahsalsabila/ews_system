<?php
$transcript = file_get_contents('C:\Users\LENOVO\.gemini\antigravity-ide\brain\8efaf08b-abde-4047-867b-a9ba8c1884e9\.system_generated\logs\transcript_full.jsonl');
$lines = explode("\n", $transcript);
$csv_data = [];
$current_csv = [];
$in_csv = false;

foreach($lines as $line) {
    if(empty(trim($line))) continue;
    $json = json_decode($line, true);
    if(!$json) continue;
    
    if(isset($json['type']) && $json['type'] == 'USER_INPUT') {
        $content = $json['content'];
        $lines_in_content = explode("\n", $content);
        
        foreach($lines_in_content as $content_line) {
            $trimmed = trim($content_line);
            
            if(strpos($trimmed, 'Height,L,M,S') === 0 || strpos($trimmed, 'Length,L,M,S') === 0) {
                if($in_csv) {
                    $csv_data[] = implode("\n", $current_csv);
                    $current_csv = [];
                }
                $in_csv = true;
            }
            
            if($in_csv) {
                if(empty($trimmed) && count($current_csv) > 0) {
                    $csv_data[] = implode("\n", $current_csv);
                    $current_csv = [];
                    $in_csv = false;
                } else if (!empty($trimmed)) {
                    $current_csv[] = $trimmed;
                }
            }
        }
        
        if($in_csv) {
            $csv_data[] = implode("\n", $current_csv);
            $current_csv = [];
            $in_csv = false;
        }
    }
}

foreach($csv_data as $i => $data) {
    file_put_contents('storage/app/who_data/csv_'.($i+1).'.csv', $data);
    echo "Saved csv_".($i+1).".csv with ".count(explode("\n", $data))." lines\n";
}
