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
        // this is a long, long test.  sorry future me/whoever.
        $user = User::factory()->create();
        $supervisor = People::factory()->create(['reports_to' => null, 'type' => PeopleType::ACADEMIC, 'group' => 'Default']);
        $currentPhd1 = People::factory()->create(['reports_to' => $supervisor->id, 'type' => PeopleType::PHD_STUDENT, 'group' => 'Default', 'surname' => 'afairlyuniquesurname']);
        $currentMpa1 = People::factory()->create(['reports_to' => null, 'type' => PeopleType::MPA, 'group' => 'Non Default']);
        $currentPhd2 = People::factory()->create(['reports_to' => $supervisor->id, 'type' => PeopleType::PHD_STUDENT, 'group' => 'Default']);
        $leftMpa = People::factory()->create(['end_at' => now()->subDays(10), 'type' => PeopleType::MPA, 'group' => 'Default']);
        $leavingPhd = People::factory()->create(['end_at' => now()->addDays(10), 'type' => PeopleType::PHD_STUDENT, 'group' => 'Non Default']);
        $arrivedPhd = People::factory()->create(['start_at' => now()->subDays(10), 'type' => PeopleType::PHD_STUDENT, 'group' => 'Default']);
        $arrivingPhd = People::factory()->create(['reports_to' => null, 'start_at' => now()->addDays(10), 'type' => PeopleType::PHD_STUDENT, 'group' => 'Default']);

        Livewire::actingAs($user)->test('people-report')
            ->assertSee($currentPhd1->full_name)
            ->assertSee($currentMpa1->full_name)
            ->assertSee($currentPhd2->full_name)
            ->assertSee($leftMpa->full_name)
            ->assertSee($leavingPhd->full_name)
            ->assertSee($arrivedPhd->full_name)
            ->assertSee($arrivingPhd->full_name)
            ->assertSee($supervisor->full_name)
            // filter by user type
            ->set('filterType', PeopleType::PHD_STUDENT->value)
            ->assertSee($currentPhd1->full_name)
            ->assertDontSee($currentMpa1->full_name)
            ->assertSee($currentPhd2->full_name)
            ->assertDontSee($leftMpa->full_name)
            ->assertSee($leavingPhd->full_name)
            ->assertSee($arrivedPhd->full_name)
            ->assertSee($arrivingPhd->full_name)
            ->assertSee($supervisor->full_name) // we always see the supervisor as they are in the drop-down filter for supervisors...
            // filter by reports_to
            ->set('filterReportsTo', $supervisor->id)
            ->assertSee($currentPhd1->full_name)
            ->assertDontSee($currentMpa1->full_name)
            ->assertSee($currentPhd2->full_name)
            ->assertDontSee($leftMpa->full_name)
            ->assertDontSee($leavingPhd->full_name)
            ->assertDontSee($arrivedPhd->full_name)
            ->assertDontSee($arrivingPhd->full_name)
            ->set('filterReportsTo', null)
            ->set('filterType', null)
            // filter by group
            ->set('filterGroup', 'Non Default')
            ->assertSee($currentMpa1->full_name)
            ->assertDontSee($currentPhd1->full_name)
            ->assertDontSee($currentPhd2->full_name)
            ->assertDontSee($leftMpa->full_name)
            ->assertSee($leavingPhd->full_name)
            ->assertDontSee($arrivedPhd->full_name)
            ->assertDontSee($arrivingPhd->full_name)
            ->set('filterGroup', null)
            // filter by various arrival/departure dates
            ->set('filterArrivingInDays', 20)
            ->assertDontSee($currentPhd1->full_name)
            ->assertDontSee($currentMpa1->full_name)
            ->assertDontSee($currentPhd2->full_name)
            ->assertDontSee($leftMpa->full_name)
            ->assertDontSee($leavingPhd->full_name)
            ->assertDontSee($arrivedPhd->full_name)
            ->assertSee($arrivingPhd->full_name)
            ->set('filterArrivingInDays', null)
            ->set('filterLeavingInDays', 20)
            ->assertDontSee($currentPhd1->full_name)
            ->assertDontSee($currentMpa1->full_name)
            ->assertDontSee($currentPhd2->full_name)
            ->assertDontSee($leftMpa->full_name)
            ->assertSee($leavingPhd->full_name)
            ->assertDontSee($arrivedPhd->full_name)
            ->assertDontSee($arrivingPhd->full_name)
            ->set('filterLeavingInDays', null)
            ->set('filterArrivedInDays', 20)
            ->assertDontSee($currentPhd1->full_name)
            ->assertDontSee($currentMpa1->full_name)
            ->assertDontSee($currentPhd2->full_name)
            ->assertDontSee($leftMpa->full_name)
            ->assertDontSee($leavingPhd->full_name)
            ->assertSee($arrivedPhd->full_name)
            ->assertDontSee($arrivingPhd->full_name)
            ->set('filterArrivedInDays', null)
            ->set('filterLeftInDays', 20)
            ->assertDontSee($currentPhd1->full_name)
            ->assertDontSee($currentMpa1->full_name)
            ->assertDontSee($currentPhd2->full_name)
            ->assertSee($leftMpa->full_name)
            ->assertDontSee($leavingPhd->full_name)
            ->assertDontSee($arrivedPhd->full_name)
            ->assertDontSee($arrivingPhd->full_name)
            ->set('filterLeftInDays', null)
            ->set('filterSearch', 'fairlyunique')
            ->assertSee($currentPhd1->full_name)
            ->assertDontSee($currentMpa1->full_name)
            ->assertDontSee($currentPhd2->full_name)
            ->assertDontSee($leftMpa->full_name)
            ->assertDontSee($leavingPhd->full_name)
            ->assertDontSee($arrivedPhd->full_name)
            ->assertDontSee($arrivingPhd->full_name)
            ;
    }

    /** @test */
    public function users_can_export_the_current_filtered_data_as_an_excel_file()
    {
        $user = User::factory()->create();

        $livewireResponse = Livewire::actingAs($user)->test('people-report')
            ->call('exportExcel')
            ->assertOk();
        $this->assertEquals('people_report_' . now()->format('d_m_Y_H_i') . '.xlsx', $livewireResponse->payload['effects']['download']['name']);
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $livewireResponse->payload['effects']['download']['contentType']);
    }
}
