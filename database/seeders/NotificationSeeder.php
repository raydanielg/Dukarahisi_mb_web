<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Welcome to Dukarahisi!',
                'message' => 'Thank you for joining Dukarahisi. Start exploring our learning materials.',
                'type' => 'info',
                'icon' => 'celebration',
                'read' => false,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'New Notes Available',
                'message' => 'Check out our latest Mathematics Form 4 notes added to the catalog.',
                'type' => 'success',
                'icon' => 'description',
                'read' => false,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Special Offer',
                'message' => 'Get 20% off on all Form 4 notes this week only!',
                'type' => 'warning',
                'icon' => 'local_offer',
                'read' => true,
            ]);
        }
    }
}
