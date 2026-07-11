<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Levels
        $primary = Level::create(['name' => 'Primary School', 'order' => 1]);
        $secondary = Level::create(['name' => 'Secondary School O-Level', 'order' => 2]);
        $advanced = Level::create(['name' => 'Secondary School A-Level', 'order' => 3]);

        // Primary classes
        $primaryClasses = [];
        for ($i = 1; $i <= 7; $i++) {
            $primaryClasses[] = ClassRoom::create([
                'level_id' => $primary->id,
                'name' => 'Standard ' . $i,
                'order' => $i,
            ]);
        }

        // O-Level classes
        $oLevelClasses = [];
        for ($i = 1; $i <= 4; $i++) {
            $oLevelClasses[] = ClassRoom::create([
                'level_id' => $secondary->id,
                'name' => 'Form ' . $i,
                'order' => $i,
            ]);
        }

        // A-Level classes
        $aLevelClasses = [];
        for ($i = 5; $i <= 6; $i++) {
            $aLevelClasses[] = ClassRoom::create([
                'level_id' => $advanced->id,
                'name' => 'Form ' . $i,
                'order' => $i - 4,
            ]);
        }

        // Primary subjects
        $primarySubjects = [
            'Mathematics', 'English Language', 'Kiswahili', 'Science and Technology',
            'Social Studies', 'Civics and Moral Education', 'Vocational Skills'
        ];
        foreach ($primaryClasses as $index => $class) {
            foreach ($primarySubjects as $j => $subject) {
                Subject::create([
                    'class_room_id' => $class->id,
                    'name' => $subject,
                    'order' => $j + 1,
                ]);
            }
        }

        // O-Level subjects
        $oLevelSubjects = [
            'Mathematics', 'English Language', 'Kiswahili', 'Physics', 'Chemistry',
            'Biology', 'Geography', 'History', 'Civics', 'Book-Keeping', 'Commerce'
        ];
        foreach ($oLevelClasses as $class) {
            foreach ($oLevelSubjects as $j => $subject) {
                Subject::create([
                    'class_room_id' => $class->id,
                    'name' => $subject,
                    'order' => $j + 1,
                ]);
            }
        }

        // A-Level subjects (common PCM, PCB, EGM, HGL)
        $aLevelSubjects = [
            'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Geography',
            'History', 'Economics', 'Accountancy', 'English Language'
        ];
        foreach ($aLevelClasses as $class) {
            foreach ($aLevelSubjects as $j => $subject) {
                Subject::create([
                    'class_room_id' => $class->id,
                    'name' => $subject,
                    'order' => $j + 1,
                ]);
            }
        }

        $this->command->info('✅ Catalog seeded successfully!');
    }
}
