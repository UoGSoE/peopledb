<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use App\Models\Task;
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

    /** @test */
    public function we_can_also_see_the_tasks_for_the_given_user()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        $task3 = Task::factory()->create();
        $person->tasks()->sync([$task1->id, $task3->id]);

        $response = $this->actingAs($user)->get(route('people.show', $person));

        $response->assertOk();
        $response->assertSee($person->full_name);
        $response->assertSee($task1->description);
        $response->assertSee($task3->description);
        $response->assertDontSee($task2->description);
    }
}
