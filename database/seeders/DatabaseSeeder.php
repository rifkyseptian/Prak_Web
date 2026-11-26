<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Media;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Rubrik demo: sediakan data media dan pengguna untuk dicoba lewat Postman.
        $user = User::firstOrCreate(
            ['email' => 'demo@flipplay.test'],
            ['name' => 'Pengguna Demo', 'password' => 'password']
        );

        $mediaItems = [
            ['title' => 'Laskar Pelangi', 'type' => 'film', 'creator' => 'Riri Riza', 'release_year' => 2008, 'duration_minutes' => 124],
            ['title' => 'Stardew Valley', 'type' => 'game', 'creator' => 'ConcernedApe', 'release_year' => 2016, 'platform' => 'PC / Switch'],
            ['title' => 'Keluarga Cemara', 'type' => 'film', 'creator' => 'Yandy Laurens', 'release_year' => 2019, 'duration_minutes' => 110],
        ];

        foreach ($mediaItems as $item) {
            Media::firstOrCreate(['title' => $item['title']], $item);
        }
    }
}
