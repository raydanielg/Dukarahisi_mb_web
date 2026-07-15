<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\SubLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LevelSubLevelSeeder extends Seeder
{
    public function run(): void
    {
        $structure = [
            'PRIMARY' => [
                'icon' => 'assets/icons/primary.png',
                'sub_levels' => [
                    'English Medium',
                    'Kiswahili Medium',
                ],
            ],
            "O'LEVEL" => [
                'icon' => 'assets/icons/secondary.png',
                'sub_levels' => [
                    'General Stream',
                    'Vocational Stream',
                ],
            ],
            "A'Level" => [
                'icon' => 'assets/icons/advanced.png',
                'sub_levels' => [
                    'Form Five',
                    'Form Six',
                ],
            ],
        ];

        $order = 1;
        foreach ($structure as $levelName => $data) {
            $level = Level::firstOrCreate(
                ['name' => $levelName],
                [
                    'icon' => $data['icon'],
                    'order' => $order,
                    'is_active' => true,
                ]
            );

            foreach ($data['sub_levels'] as $i => $subName) {
                SubLevel::firstOrCreate(
                    ['level_id' => $level->id, 'name' => $subName],
                    [
                        'slug' => Str::slug($subName),
                        'order' => $i + 1,
                        'is_active' => true,
                    ]
                );
            }

            $order++;
        }

        $this->command->info('Levels and Sub-Levels seeded successfully!');
    }
}
