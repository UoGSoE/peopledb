<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\People;
use App\Models\PeopleType;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_person_is_created_they_are_allocated_tasks_to_be_signed_off_by_admin_staff()
    {
        $this->setUpPeopleTypes();
        $task1 = Task::factory()->onboarding()->create();
        $task1->peopleTypes()->sync([$this->academicType->id, $this->phdType->id]);
        $task2 = Task::factory()->onboarding()->create();
        $task2->peopleTypes()->sync([$this->academicType->id, $this->mpaType->id]);
        $leavingTask = Task::factory()->leaving()->create();
        $leavingTask->peopleTypes()->sync([$this->academicType->id, $this->mpaType->id]);

        $academicPerson = People::factory()->create(['people_type_id' => $this->academicType->id]);
        $mpaPerson = People::factory()->create(['people_type_id' => $this->mpaType->id]);

        $this->assertEquals(2, $academicPerson->tasks()->count());
        $this->assertEquals(1, $mpaPerson->tasks()->count());
        $this->assertTrue($academicPerson->tasks->contains($task1));
        $this->assertTrue($academicPerson->tasks->contains($task2));
        $this->assertTrue($mpaPerson->tasks->contains($task2));
    }

    /** @test */
    public function some_time_before_a_person_leaves_they_are_allocated_tasks_to_be_signed_off_by_admin_staff()
    {
        $this->setUpPeopleTypes();
        config(['peopledb.schedule_leaving_tasks_days' => 30]);
        $task1 = Task::factory()->onboarding()->create();
        $task1->peopleTypes()->sync([$this->academicType->id, $this->phdType->id]);
        $task2 = Task::factory()->onboarding()->create();
        $task2->peopleTypes()->sync([$this->academicType->id, $this->mpaType->id]);
        $leavingTask = Task::factory()->leaving()->create();
        $leavingTask->peopleTypes()->sync([$this->academicType->id, $this->mpaType->id]);

        $academicPerson = People::factory()->create(['people_type_id' => $this->academicType->id]);
        $mpaPerson = People::factory()->create(['people_type_id' => $this->mpaType->id]);
        $leavingMpaPerson = People::factory()->create(['people_type_id' => $this->mpaType->id, 'end_at' => now()->addDays(30)]);
        $leavingInAgesMpaPerson = People::factory()->create(['people_type_id' => $this->mpaType->id, 'end_at' => now()->addDays(300)]);

        $this->artisan('peopledb:add-tasks-to-leaving-people');

        $this->assertEquals(2, $academicPerson->tasks()->count());
        $this->assertEquals(1, $mpaPerson->tasks()->count());
        $this->assertEquals(2, $leavingMpaPerson->tasks()->count());
        $this->assertEquals(1, $leavingInAgesMpaPerson->tasks()->count());
        $this->assertTrue($academicPerson->tasks->contains($task1));
        $this->assertTrue($academicPerson->tasks->contains($task2));
        $this->assertTrue($mpaPerson->tasks->contains($task2));
        $this->assertTrue($leavingMpaPerson->tasks->contains($task2));
        $this->assertTrue($leavingMpaPerson->tasks->contains($leavingTask));
    }

    /** @test */
    public function a_persons_tasks_can_be_signed_off_by_users()
    {
        $user = User::factory()->create();
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        $task3 = Task::factory()->create();
        $person1 = People::factory()->create();
        $person2 = People::factory()->create();
        $person1->tasks()->sync([$task1->id, $task3->id]);
        $person2->tasks()->sync([$task2->id]);

        $response = $this->actingAs($user)->post(route('person.task.update', $person1), [
            'task_id' => $task1->id,
            'task_notes' => 'hello',
            'task_completed_at' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('people.show', $person1->id));
        $response->assertSessionDoesntHaveErrors();
        $this->assertEquals(2, $person1->tasks()->count());
        $personTask = $person1->tasks()->wherePivot('task_id', '=', $task1->id)->firstOrFail()->pivot;
        $this->assertEquals(now()->format('Y-m-d'), $personTask->completed_at->format('Y-m-d'));
        $this->assertEquals('hello', $personTask->notes);
        $this->assertTrue($personTask->completer->is($user));
    }

    /** @test */
    public function a_persons_tasks_can_be_unsigned_off_by_users()
    {
        $this->fail('TODO');
        $user = User::factory()->create();
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        $task3 = Task::factory()->create();
        $person1 = People::factory()->create();
        $person2 = People::factory()->create();
        $person1->tasks()->sync([$task1->id, $task3->id]);
        $person2->tasks()->sync([$task2->id]);

        $response = $this->actingAs($user)->post(route('person.task.update', $person1), [
            'task_id' => $task1->id,
            'task_notes' => 'hello',
            'task_completed_at' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('people.show', $person1->id));
        $response->assertSessionDoesntHaveErrors();
        $this->assertEquals(2, $person1->tasks()->count());
        $personTask = $person1->tasks()->wherePivot('task_id', '=', $task1->id)->firstOrFail()->pivot;
        $this->assertEquals(now()->format('Y-m-d'), $personTask->completed_at->format('Y-m-d'));
        $this->assertEquals('hello', $personTask->notes);
        $this->assertTrue($personTask->completer->is($user));
    }

    /** @test */
    public function a_persons_tasks_can_have_just_its_notes_updated()
    {
        $user = User::factory()->create();
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        $task3 = Task::factory()->create();
        $person1 = People::factory()->create();
        $person2 = People::factory()->create();
        $person1->tasks()->sync([$task1->id, $task3->id]);
        $person2->tasks()->sync([$task2->id]);

        $response = $this->actingAs($user)->post(route('person.task.update', $person1), [
            'task_id' => $task1->id,
            'task_notes' => 'hello again',
            'task_completed_at' => '',
        ]);

        $response->assertRedirect(route('people.show', $person1->id));
        $response->assertSessionDoesntHaveErrors();
        $this->assertEquals(2, $person1->tasks()->count());
        $personTask = $person1->tasks()->wherePivot('task_id', '=', $task1->id)->firstOrFail()->pivot;
        $this->assertNull($personTask->completed_at);
        $this->assertEquals('hello again', $personTask->notes);
        $this->assertNull($personTask->completer);
    }
}
