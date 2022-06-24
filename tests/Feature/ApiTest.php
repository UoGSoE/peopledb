<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use App\Models\PeopleType;
use App\Models\Task;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected $apiSystemUser;

    public function setUp(): void
    {
        parent::setUp();

        // $this->validApiToken = ApiToken::factory()->create();
    }

    /** @test */
    public function we_need_to_supply_an_api_key_to_access_the_api_endpoints()
    {
        $this->markTestSkipped('TODO: finish the api key middleware package');
    }

    /** @test */
    public function we_can_get_a_list_of_all_current_people()
    {
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $leftAgesAgoPerson = People::factory()->create(['start_at' => now()->subYears(5), 'end_at' => now()->subYears(3)]);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20)]);

        $response = $this->getJson(route('api.people.index') . '?filter[current]=1');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $currentPerson1->id,
                    'full_name' => $currentPerson1->full_name,
                    'email' => $currentPerson1->email,
                    'start_at' => $currentPerson1->start_at->toJson(),
                    'end_at' => $currentPerson1->end_at->toJson(),
                ],
                [
                    'id' => $currentPerson2->id,
                    'full_name' => $currentPerson2->full_name,
                    'email' => $currentPerson2->email,
                    'start_at' => $currentPerson2->start_at->toJson(),
                    'end_at' => $currentPerson2->end_at->toJson(),
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_filter_the_people_by_type()
    {
        $this->setUpPeopleTypes();
        $currentPerson1 = People::factory()->academic()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $currentPerson2 = People::factory()->phd()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $leftAgesAgoPerson = People::factory()->academic()->create(['start_at' => now()->subYears(5), 'end_at' => now()->subYears(3)]);
        $notArrivedYetPerson = People::factory()->mpa()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20)]);

        $response = $this->getJson(route('api.people.index') . '?filter[current]=1&filter[peopleType]=' . PeopleType::ACADEMIC);

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $currentPerson1->id,
                    'full_name' => $currentPerson1->full_name,
                    'email' => $currentPerson1->email,
                    'start_at' => $currentPerson1->start_at->toJson(),
                    'end_at' => $currentPerson1->end_at->toJson(),
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_filter_the_people_by_group()
    {
        $this->setUpPeopleTypes();
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10), 'group' => 'xyz']);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10), 'group' => 'abc']);
        $leftAgesAgoPerson = People::factory()->create(['start_at' => now()->subYears(5), 'end_at' => now()->subYears(3), 'group' => '123']);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20), 'group' => 'wompwomp']);

        $response = $this->getJson(route('api.people.index') . '?filter[current]=1&filter[group]=abc');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $currentPerson2->id,
                    'full_name' => $currentPerson2->full_name,
                    'email' => $currentPerson2->email,
                    'start_at' => $currentPerson2->start_at->toJson(),
                    'end_at' => $currentPerson2->end_at->toJson(),
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_filter_the_people_by_start_date()
    {
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10), 'group' => 'xyz']);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10), 'group' => 'abc']);
        $leftAgesAgoPerson = People::factory()->create(['start_at' => now()->subYears(5), 'end_at' => now()->subYears(3), 'group' => '123']);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20), 'group' => 'wompwomp']);

        $response = $this->getJson(route('api.people.index') . '?filter[start_after]=' . now()->subDays(5)->format('Y-m-d'));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $notArrivedYetPerson->id,
                    'full_name' => $notArrivedYetPerson->full_name,
                    'email' => $notArrivedYetPerson->email,
                    'start_at' => $notArrivedYetPerson->start_at->toJson(),
                    'end_at' => $notArrivedYetPerson->end_at->toJson(),
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_filter_the_people_by_end_date()
    {
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10), 'group' => 'xyz']);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10), 'group' => 'abc']);
        $leftAgesAgoPerson = People::factory()->create(['start_at' => now()->subYears(5), 'end_at' => now()->subYears(3), 'group' => '123']);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20), 'group' => 'wompwomp']);

        $response = $this->getJson(route('api.people.index') . '?filter[end_before]=' . now()->subDays(5)->format('Y-m-d'));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $leftAgesAgoPerson->id,
                    'full_name' => $leftAgesAgoPerson->full_name,
                    'email' => $leftAgesAgoPerson->email,
                    'start_at' => $leftAgesAgoPerson->start_at->toJson(),
                    'end_at' => $leftAgesAgoPerson->end_at->toJson(),
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_include_the_person_the_people_report_to()
    {
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $leftAgesAgoPerson = People::factory()->create(['start_at' => now()->subYears(5), 'end_at' => now()->subYears(3)]);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20)]);
        $currentPerson1->reportsTo()->associate($currentPerson2);
        $currentPerson1->save();

        $response = $this->getJson(route('api.people.index') . '?filter[current]=1&include=reportsTo');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $currentPerson1->id,
                    'full_name' => $currentPerson1->full_name,
                    'email' => $currentPerson1->email,
                    'start_at' => $currentPerson1->start_at->toJson(),
                    'end_at' => $currentPerson1->end_at->toJson(),
                    'reports_to' => [
                        'id' => $currentPerson2->id,
                        'email' => $currentPerson2->email,
                        'start_at' => $currentPerson2->start_at->toJson(),
                        'end_at' => $currentPerson2->end_at->toJson(),
                    ],
                ],
                [
                    'id' => $currentPerson2->id,
                    'full_name' => $currentPerson2->full_name,
                    'email' => $currentPerson2->email,
                    'start_at' => $currentPerson2->start_at->toJson(),
                    'end_at' => $currentPerson2->end_at->toJson(),
                    'reports_to' => null,
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_include_the_people_who_report_to_the_people_as_it_were()
    {
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $leftAgesAgoPerson = People::factory()->create(['start_at' => now()->subYears(5), 'end_at' => now()->subYears(3)]);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20)]);
        $currentPerson1->reportsTo()->associate($currentPerson2);
        $currentPerson1->save();

        $response = $this->getJson(route('api.people.index') . '?filter[current]=1&include=reportees');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $currentPerson1->id,
                    'full_name' => $currentPerson1->full_name,
                    'email' => $currentPerson1->email,
                    'start_at' => $currentPerson1->start_at->toJson(),
                    'end_at' => $currentPerson1->end_at->toJson(),
                    'reportees' => null,
                ],
                [
                    'id' => $currentPerson2->id,
                    'full_name' => $currentPerson2->full_name,
                    'email' => $currentPerson2->email,
                    'start_at' => $currentPerson2->start_at->toJson(),
                    'end_at' => $currentPerson2->end_at->toJson(),
                    'reportees' => [
                        [
                            'id' => $currentPerson1->id,
                            'email' => $currentPerson1->email,
                            'start_at' => $currentPerson1->start_at->toJson(),
                            'end_at' => $currentPerson1->end_at->toJson(),
                            'full_name' => $currentPerson1->full_name,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_find_a_specific_person_by_their_id()
    {
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $leftAgesAgoPerson = People::factory()->create(['start_at' => now()->subYears(5), 'end_at' => now()->subYears(3)]);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20)]);

        $response = $this->getJson(route('api.people.index') . '?filter[id]=' . $currentPerson2->id);

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $currentPerson2->id,
                    'full_name' => $currentPerson2->full_name,
                    'email' => $currentPerson2->email,
                    'start_at' => $currentPerson2->start_at->toJson(),
                    'end_at' => $currentPerson2->end_at->toJson(),
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_find_a_specific_people_by_their_id()
    {
        // this also works for emails etc - just testing this one means the rest work too
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $leftAgesAgoPerson = People::factory()->create(['start_at' => now()->subYears(5), 'end_at' => now()->subYears(3)]);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20)]);

        $response = $this->getJson(route('api.people.index') . '?filter[id]=' . $currentPerson2->id . ',' . $currentPerson1->id);

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $currentPerson1->id,
                    'full_name' => $currentPerson1->full_name,
                    'email' => $currentPerson1->email,
                    'start_at' => $currentPerson1->start_at->toJson(),
                    'end_at' => $currentPerson1->end_at->toJson(),
                ],
                [
                    'id' => $currentPerson2->id,
                    'full_name' => $currentPerson2->full_name,
                    'email' => $currentPerson2->email,
                    'start_at' => $currentPerson2->start_at->toJson(),
                    'end_at' => $currentPerson2->end_at->toJson(),
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_find_a_specific_person_by_their_username()
    {
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $leftAgesAgoPerson = People::factory()->create(['username' => 'santa', 'start_at' => now()->subYears(5), 'end_at' => now()->subYears(3)]);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20)]);

        $response = $this->getJson(route('api.people.index') . '?filter[username]=santa');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $leftAgesAgoPerson->id,
                    'full_name' => $leftAgesAgoPerson->full_name,
                    'email' => $leftAgesAgoPerson->email,
                    'start_at' => $leftAgesAgoPerson->start_at->toJson(),
                    'end_at' => $leftAgesAgoPerson->end_at->toJson(),
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_find_a_specific_person_by_their_email()
    {
        $currentPerson1 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $currentPerson2 = People::factory()->create(['start_at' => now()->subDays(10), 'end_at' => now()->addDays(10)]);
        $leftAgesAgoPerson = People::factory()->create(['email' => 'santa@example.com', 'start_at' => now()->subYears(5), 'end_at' => now()->subYears(3)]);
        $notArrivedYetPerson = People::factory()->create(['start_at' => now()->addDays(10), 'end_at' => now()->addDays(20)]);

        $response = $this->getJson(route('api.people.index') . '?filter[email]=santa@example.com');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $leftAgesAgoPerson->id,
                    'full_name' => $leftAgesAgoPerson->full_name,
                    'email' => $leftAgesAgoPerson->email,
                    'start_at' => $leftAgesAgoPerson->start_at->toJson(),
                    'end_at' => $leftAgesAgoPerson->end_at->toJson(),
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_mark_a_given_task_for_a_given_person_as_complete()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $otherPerson = People::factory()->create();
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        $person->tasks()->sync([$task1->id, $task2->id]);
        $otherPerson->tasks()->sync([$task1->id, $task2->id]);

        $this->freezeTime();

        $response = $this->postJson(route('api.task.complete'), [
            'task_id' => $task1->id,
            'person_guid' => $person->username,
            'completer_guid' => $user->username,
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Task marked as complete',
            'data' => [
            ],
        ]);
        $this->assertDatabaseHas('people_task', [
            'task_id' => $task1->id,
            'people_id' => $person->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'completed_by' => $user->id,
        ]);
        $this->assertDatabaseHas('people_task', [
            'task_id' => $task2->id,
            'people_id' => $person->id,
            'completed_at' => null,
            'completed_by' => null,
        ]);
        $this->assertDatabaseHas('people_task', [
            'task_id' => $task1->id,
            'people_id' => $otherPerson->id,
            'completed_at' => null,
            'completed_by' => null,
        ]);
        $this->assertDatabaseHas('people_task', [
            'task_id' => $task2->id,
            'people_id' => $otherPerson->id,
            'completed_at' => null,
            'completed_by' => null,
        ]);
    }

    /** @test */
    public function we_can_mark_a_given_task_for_a_given_person_as_complete_with_optional_notes()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $otherPerson = People::factory()->create();
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        $person->tasks()->sync([$task1->id, $task2->id]);
        $otherPerson->tasks()->sync([$task1->id, $task2->id]);

        $this->freezeTime();

        $response = $this->postJson(route('api.task.complete'), [
            'task_id' => $task1->id,
            'person_guid' => $person->username,
            'completer_guid' => $user->username,
            'notes' => 'This is a note',
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Task marked as complete',
            'data' => [
            ],
        ]);
        $this->assertDatabaseHas('people_task', [
            'task_id' => $task1->id,
            'people_id' => $person->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'completed_by' => $user->id,
            'notes' => "This is a note\n",
        ]);
    }

    /** @test */
    public function if_notes_are_passed_we_prepend_them_to_any_existing_notes()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $otherPerson = People::factory()->create();
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        $person->tasks()->sync([$task1->id => ['notes' => 'Existing Note'], $task2->id]);
        $otherPerson->tasks()->sync([$task1->id, $task2->id]);

        $this->freezeTime();

        $response = $this->postJson(route('api.task.complete'), [
            'task_id' => $task1->id,
            'person_guid' => $person->username,
            'completer_guid' => $user->username,
            'notes' => 'This is a new note',
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Task marked as complete',
            'data' => [
            ],
        ]);
        $this->assertDatabaseHas('people_task', [
            'task_id' => $task1->id,
            'people_id' => $person->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'completed_by' => $user->id,
            'notes' => "This is a new note\nExisting Note",
        ]);
    }
}
