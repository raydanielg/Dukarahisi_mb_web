<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Activity::create([
                'user_id' => $user->id,
                'type' => 'registration',
                'title' => 'Akaunti Mpya',
                'description' => 'Umejisajili kwenye Dukarahisi',
                'icon' => 'person_add',
            ]);

            Activity::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'title' => 'Ununuzi Mpya',
                'description' => 'Umenunua Mathematics Form 4',
                'icon' => 'shopping_bag',
            ]);

            Activity::create([
                'user_id' => $user->id,
                'type' => 'payment',
                'title' => 'Malipo',
                'description' => 'Order #ORD-001 imelipwa',
                'icon' => 'check_circle',
            ]);
        }
    }
}
