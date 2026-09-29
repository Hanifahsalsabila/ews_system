<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhoAnthroV110Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $basePath = database_path('data/who/anthro/v1.1.0');

            // 1. WAZ (weianthro.txt)
            $this->seedDataset("$basePath/weianthro.txt", function($row) {
                $sex = (int)$row[0];
                $age = (int)$row[1];
                $l = (float)$row[2];
                $m = (float)$row[3];
                $s = (float)$row[4];
                $gender = $sex === 1 ? 'L' : 'P';

                return [
                    ['indicator' => 'WAZ', 'gender' => $gender, 'ref_value' => $age, 'unit' => 'age_days', 'l' => $l, 'm' => $m, 's' => $s, 'source_version' => 'who_anthro_v1.1.0'],
                    ['indicator' => 'BBU', 'gender' => $gender, 'ref_value' => $age, 'unit' => 'age_days', 'l' => $l, 'm' => $m, 's' => $s, 'source_version' => 'who_anthro_v1.1.0']
                ];
            });

            // 2. HAZ (lenanthro.txt)
            $this->seedDataset("$basePath/lenanthro.txt", function($row) {
                $sex = (int)$row[0];
                $age = (int)$row[1];
                $l = (float)$row[2];
                $m = (float)$row[3];
                $s = (float)$row[4];
                $lorh = trim($row[5] ?? '');
                $gender = $sex === 1 ? 'L' : 'P';

                return [
                    ['indicator' => 'HAZ', 'gender' => $gender, 'ref_value' => $age, 'unit' => 'age_days', 'l' => $l, 'm' => $m, 's' => $s, 'lorh' => $lorh, 'source_version' => 'who_anthro_v1.1.0'],
                    ['indicator' => 'TBU', 'gender' => $gender, 'ref_value' => $age, 'unit' => 'age_days', 'l' => $l, 'm' => $m, 's' => $s, 'lorh' => $lorh, 'source_version' => 'who_anthro_v1.1.0']
                ];
            });

            // 3. WFL (wflanthro.txt)
            $this->seedDataset("$basePath/wflanthro.txt", function($row) {
                $sex = (int)$row[0];
                $length = (float)$row[1];
                $l = (float)$row[2];
                $m = (float)$row[3];
                $s = (float)$row[4];
                $lorh = trim($row[5] ?? 'L');
                $gender = $sex === 1 ? 'L' : 'P';

                return [
                    ['indicator' => 'WFL', 'gender' => $gender, 'ref_value' => $length, 'unit' => 'cm', 'l' => $l, 'm' => $m, 's' => $s, 'lorh' => $lorh, 'source_version' => 'who_anthro_v1.1.0'],
                    ['indicator' => 'BBTB', 'gender' => $gender, 'ref_value' => $length, 'unit' => 'cm', 'l' => $l, 'm' => $m, 's' => $s, 'lorh' => $lorh, 'source_version' => 'who_anthro_v1.1.0']
                ];
            });

            // 4. WFH (wfhanthro.txt)
            $this->seedDataset("$basePath/wfhanthro.txt", function($row) {
                $sex = (int)$row[0];
                $height = (float)$row[1];
                $l = (float)$row[2];
                $m = (float)$row[3];
                $s = (float)$row[4];
                $lorh = trim($row[5] ?? 'H');
                $gender = $sex === 1 ? 'L' : 'P';

                $inserts = [
                    ['indicator' => 'WFH', 'gender' => $gender, 'ref_value' => $height, 'unit' => 'cm', 'l' => $l, 'm' => $m, 's' => $s, 'lorh' => $lorh, 'source_version' => 'who_anthro_v1.1.0']
                ];
                
                // For legacy compatibility, WFH > 110cm maps to BBTB
                if ($height > 110.0) {
                    $inserts[] = ['indicator' => 'BBTB', 'gender' => $gender, 'ref_value' => $height, 'unit' => 'cm', 'l' => $l, 'm' => $m, 's' => $s, 'lorh' => $lorh, 'source_version' => 'who_anthro_v1.1.0'];
                }

                return $inserts;
            });
        });
    }

    private function seedDataset(string $path, callable $mapper): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $header = true;
        
        $chunk = [];
        $now = now();
        
        foreach ($lines as $line) {
            if ($header) {
                $header = false;
                continue;
            }

            $row = preg_split('/\t+/', $line);
            if (count($row) < 5) continue;

            $mappedRows = $mapper($row);
            
            foreach ($mappedRows as $mappedRow) {
                $mappedRow['standard'] = 'WHO2006';
                $mappedRow['created_at'] = $now;
                $mappedRow['updated_at'] = $now;
                $chunk[] = $mappedRow;
            }

            if (count($chunk) >= 500) {
                DB::table('who_zscore_lms')->insertOrIgnore($chunk);
                $chunk = [];
            }
        }

        if (count($chunk) > 0) {
            DB::table('who_zscore_lms')->insertOrIgnore($chunk);
        }
    }
}
