<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use App\Models\People;
use App\Models\UnitEmail;
use App\Models\PeopleType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding test data...');

        collect([
            PeopleType::ACADEMIC,
            PeopleType::PHD,
            PeopleType::PDRA,
            PeopleType::MPA,
            PeopleType::TECHNICAL
        ])->each(fn ($type) => PeopleType::factory()->create(['name' => $type]));

        $admin = User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('secret'),
        ]);

        collect(['IT', 'Facilities', 'Teaching', 'Research Office', 'School Admin'])->each(fn ($name) => Unit::factory()->create([
            'name' => $name,
            'owner_id' => $admin->id,
        ]));
        Unit::get()->each(fn ($unit) => $unit->tasks()->saveMany(Task::factory()->times(rand(1, 10))->make(['unit_id' => $unit->id])));
        Unit::get()->each(fn ($unit) => $unit->emails()->saveMany(UnitEmail::factory()->times(rand(1, 3))->make()));

        $regularPeople = People::factory()->times(1000)->create();
        foreach (range(1, rand(10, 20)) as $count) {
            People::factory()->arrivingSoon()->create();
        }
        foreach (range(1, rand(10, 20)) as $count) {
            People::factory()->leavingSoon()->create();
        }
        $academicId = PeopleType::where('name', '=', PeopleType::ACADEMIC)->firstOrCreate(['name' => PeopleType::ACADEMIC])->id;
        People::where('people_type_id', '=', $academicId)->get()->each(function ($person) {
            foreach (range(1, rand(5, 15)) as $i) {
                People::inRandomOrder()->first()->update(['reports_to' => $person->id]);
            }
        });

        People::get()->each(fn ($person) => $person->tasks()->sync(Task::inRandomOrder()->take(rand(1, 5))->get()->pluck('id')));
    }
}
