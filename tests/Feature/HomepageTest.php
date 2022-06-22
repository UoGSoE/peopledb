<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_homepage_shows_a_list_of_recent_arrivals_and_arriving_soon_and_also_people_leaving_and_left_recently()
    {
        $this->withoutExceptionHandling();
        $this->setUpPeopleTypes();
        $user = User::factory()->create();
        $arrivedRecentlyPerson = People::factory()->create(['start_at' => now()->subDays(1)]);
        $arrivingSoonPerson = People::factory()->create(['start_at' => now()->addDays(1)]);
        $leavingSoonPerson = People::factory()->create(['end_at' => now()->addDays(1)]);
        $leftRecentlyPerson = People::factory()->create(['end_at' => now()->subDays(2)]);
        $regularPerson = People::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee($arrivedRecentlyPerson->surname);
        $response->assertSee($arrivingSoonPerson->surname);
        $response->assertSee($leavingSoonPerson->surname);
        $response->assertSee($leftRecentlyPerson->surname);
        $response->assertDontSee($regularPerson->surname);
    }

    /** @test */
    public function users_can_export_the_data_as_an_excel_sheet()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('export.arrivals_departures'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename=arrivals_departures_' . now()->format('d_m_Y_H_i') . '.xlsx');
    }
}
