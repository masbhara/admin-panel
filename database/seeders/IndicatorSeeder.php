<?php

namespace Database\Seeders;

use App\Models\Indicator;
use Illuminate\Database\Seeder;

class IndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $indicators = [
            [
                'name' => 'Rendement Canne à Sucre',
                'current_value' => 75.00,
                'target_value' => 85.00,
                'unit' => 't/ha',
                'type' => 'production'
            ],
            [
                'name' => 'Qualité Banane Export',
                'current_value' => 88.00,
                'target_value' => 95.00,
                'unit' => '%',
                'type' => 'quality'
            ],
            [
                'name' => 'Rentabilité Ananas',
                'current_value' => 70.00,
                'target_value' => 80.00,
                'unit' => '%',
                'type' => 'financial'
            ],
            [
                'name' => 'Certification Bio',
                'current_value' => 75.00,
                'target_value' => 90.00,
                'unit' => '%',
                'type' => 'certification'
            ],
            [
                'name' => 'Innovation Igname',
                'current_value' => 65.00,
                'target_value' => 75.00,
                'unit' => '%',
                'type' => 'innovation'
            ]
        ];

        foreach ($indicators as $indicator) {
            Indicator::create($indicator);
        }
    }
}
