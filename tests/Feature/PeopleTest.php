<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PeopleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_see_the_page_for_an_individial_person()
    {
        $user = User::factory()->create();
        $supervisor = People::factory()->create();
        $person1 = People::factory()->create(['reports_to' => $supervisor->id]);
        $person2 = People::factory()->create(['reports_to' => null]);

        $response = $this->actingAs($user)->get(route('people.show', $person1));

        $response->assertOk();
        $response->assertSee($person1->full_name);
        $response->assertSee($supervisor->full_name);

        $response = $this->actingAs($user)->get(route('people.show', $person2));

        $response->assertOk();
        $response->assertSee($person2->full_name);
        $response->assertDontSee($supervisor->full_name);
    }
}
