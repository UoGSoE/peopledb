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
}
