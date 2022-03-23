<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use App\Models\PeopleType;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class PeopleReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_page_with_the_report_on_it()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.people'));

        $response->assertOk();
        $response->assertSeeLivewire('people-report');
    }

    /** @test */
    public function the_report_has_the_right_data_and_can_be_filtered_in_various_ways()
    {
        $user = User::factory()->create();
        $supervisor = People::factory()->create(['type' => PeopleType::ACADEMIC]);
        $person1 = People::factory()->create(['reports_to' => $supervisor->id, 'type' => PeopleType::PHD_STUDENT]);
        $person2 = People::factory()->create(['reports_to' => null, 'type' => PeopleType::MPA]);
        $person3 = People::factory()->create(['reports_to' => $supervisor->id, 'type' => PeopleType::PHD_STUDENT]);
        $leftPerson = People::factory()->create(['end_at' => now()->subDays(1), 'type' => PeopleType::MPA]);
        $leavingPerson = People::factory()->create(['end_at' => now()->addDays(1), 'type' => PeopleType::PHD_STUDENT]);
        $arrivedPerson = People::factory()->create(['start_at' => now()->subDays(1), 'type' => PeopleType::PHD_STUDENT]);
        $arrivingPerson = People::factory()->create(['start_at' => now()->addDays(1), 'type' => PeopleType::PHD_STUDENT]);

        Livewire::actingAs($user)->test('people-report')
            ->assertSee($person1->full_name);
    }
}
