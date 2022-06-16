<?php

namespace Tests\Feature;

use App\Models\Task;
use Tests\TestCase;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnitsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_existing_organisation_units()
    {
        $user = User::factory()->create();
        $unit1 = Unit::factory()->create();
        $unit2 = Unit::factory()->create();
        $task1 = $unit1->tasks()->save(Task::factory()->make());
        $task2 = $unit2->tasks()->save(Task::factory()->make());

        $response = $this->actingAs($user)->get(route('units.index'));

        $response->assertOk();
        $response->assertSee($unit1->name);
        $response->assertSee($task1->description);
        $response->assertSee($unit2->name);
        $response->assertSee($task2->description);
    }

    /** @test */
    public function we_can_edit_an_existing_unit_and_its_set_of_tasks()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $unit1 = Unit::factory()->create();
        $unit2 = Unit::factory()->create();
        $task1 = $unit1->tasks()->save(Task::factory()->make());
        $task2 = $unit1->tasks()->save(Task::factory()->make());
        $task3 = $unit2->tasks()->save(Task::factory()->make());
        $task4 = $unit2->tasks()->save(Task::factory()->make());

        $response = $this->actingAs($user)->post(route('unit.update', $unit1->id), [
            'name' => 'New name',
            'owner_id' => $user2->id,
            'emails' => 'fred@example.com',
            'description' => [
                $task1->id => 'New description for task 1',
                $task2->id => 'New description for task 2',
            ],
            'is_optional' => [
                $task1->id => true,
                $task2->id => false,
            ],
            'is_onboarding' => [
                $task1->id => true,
                $task2->id => false,
            ],
            'is_active' => [
                $task1->id => true,
                $task2->id => false,
            ],
        ]);

        $response->assertRedirect(route('units.index'));
        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('success', 'Unit/tasks updated.');
        tap($unit1->fresh(), function ($unit) use ($user2) {
            $this->assertEquals('New name', $unit->name);
            $this->assertEquals($user2->id, $unit->owner_id);
            $this->assertEquals(1, $unit->emails()->count());
            $this->assertEquals('fred@example.com', $unit->emails()->first()->email);
        });
        tap($task1->fresh(), function ($task) {
            $this->assertEquals('New description for task 1', $task->description);
            $this->assertTrue($task->isOptional());
            $this->assertTrue($task->isOnboarding());
            $this->assertTrue($task->isActive());
        });
    }

    /** @test */
    public function we_can_supply_notification_email_addresses_for_the_unit()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $unit1 = Unit::factory()->create();
        $unit2 = Unit::factory()->create();
        $task1 = $unit1->tasks()->save(Task::factory()->make());
        $task2 = $unit1->tasks()->save(Task::factory()->make());
        $task3 = $unit2->tasks()->save(Task::factory()->make());
        $task4 = $unit2->tasks()->save(Task::factory()->make());

        $response = $this->actingAs($user)->post(route('unit.update', $unit1->id), [
            'emails' => [
                $unit1->id => 'fred@example.com, ginger@example.com',
            ],
            'description' => [
                $task1->id => 'New description for task 1',
                $task2->id => 'New description for task 2',
            ],
            'is_optional' => [
                $task1->id => true,
                $task2->id => false,
            ],
            'is_onboarding' => [
                $task1->id => true,
                $task2->id => false,
            ],
            'is_active' => [
                $task1->id => true,
                $task2->id => false,
            ],
        ]);

        $response->assertRedirect(route('units.index'));
        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('success', 'Unit/tasks updated.');
        tap($task1->fresh(), function ($task) {
            $this->assertEquals('New description for task 1', $task->description);
            $this->assertTrue($task->isOptional());
            $this->assertTrue($task->isOnboarding());
            $this->assertTrue($task->isActive());
        });
        $this->assertCount(2, $unit1->emails);
        $this->assertTrue($unit1->emails->pluck('email')->contains('fred@example.com'));
        $this->assertTrue($unit1->emails->pluck('email')->contains('ginger@example.com'));
    }

    /** @test */
    public function we_can_add_a_new_tesk_to_a_unit()
    {
        $user = User::factory()->create();
        $unit1 = Unit::factory()->create();
        $unit2 = Unit::factory()->create();
        $task1 = $unit1->tasks()->save(Task::factory()->make());
        $task2 = $unit1->tasks()->save(Task::factory()->make());
        $task3 = $unit2->tasks()->save(Task::factory()->make());
        $task4 = $unit2->tasks()->save(Task::factory()->make());

        $response = $this->actingAs($user)->post(route('unit.update', $unit1->id), [
            'emails' => [
                $unit1->id => 'fred@example.com',
            ],
            'description' => [
                $task1->id => 'New description for task 1',
                $task2->id => 'New description for task 2',
                'new' => 'New task',
            ],
            'is_optional' => [
                $task1->id => true,
                $task2->id => false,
                'new' => false,
            ],
            'is_onboarding' => [
                $task1->id => true,
                $task2->id => false,
                'new' => true,
            ],
            'is_active' => [
                $task1->id => true,
                $task2->id => false,
                'new' => true,
            ],
        ]);

        $response->assertRedirect(route('units.index'));
        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('success', 'Unit/tasks updated.');
        tap($task1->fresh(), function ($task) {
            $this->assertEquals('New description for task 1', $task->description);
            $this->assertTrue($task->isOptional());
            $this->assertTrue($task->isOnboarding());
            $this->assertTrue($task->isActive());
        });
        $newTask = $unit1->tasks()->where('description', 'New task')->first();
        $this->assertFalse($newTask->isOptional());
        $this->assertTrue($newTask->isOnboarding());
        $this->assertTrue($newTask->isActive());
    }

    /** @test */
    public function we_can_create_a_new_unit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('unit.store'), [
            'name' => 'New unit',
        ]);

        $response->assertRedirect(route('units.index'));
        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('success', 'Unit created.');
        $newUnit = Unit::where('name', '=', 'New unit')->first();
        $this->assertNotNull($newUnit);
        $this->assertTrue($newUnit->owner->is($user));
    }
}
