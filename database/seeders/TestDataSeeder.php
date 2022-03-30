<?php

namespace Database\Seeders;

use App\Models\People;
use App\Models\PeopleType;
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
        People::where('type', '=', PeopleType::ACADEMIC)->get()->each(function ($person) {
            foreach (range(1, rand(5, 15)) as $i) {
                People::inRandomOrder()->first()->update(['reports_to' => $person->id]);
            }
        });
    }
}
