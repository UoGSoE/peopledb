<?php

namespace Database\Seeders;

use App\Models\People;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('secret'),
        ]);

        $regularPeople = People::factory()->times(1000)->create();
        foreach (range(1, rand(10, 20)) as $count) {
            People::factory()->arrivingSoon()->create();
        }
        foreach (range(1, rand(10, 20)) as $count) {
            People::factory()->leavingSoon()->create();
        }
        People::all()->each(function ($person) {
            if (rand(1, 3) === 1) {
                $person->update([
                    'reports_to' => People::inRandomOrder()->first()->id,
                ]);
            }
        });
    }
}
