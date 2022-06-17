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
        $this->fail('TODO: should only be onboarding tasks for creation');
        $this->setUpPeopleTypes();
        $task1 = Task::factory()->create();
        $task1->peopleTypes()->sync([$this->academicType->id, $this->phdType->id]);
        $task2 = Task::factory()->create();
        $task2->peopleTypes()->sync([$this->academicType->id, $this->mpaType->id]);

        $academicPerson = People::factory()->create(['people_type_id' => $this->academicType->id]);
        $mpaPerson = People::factory()->create(['people_type_id' => $this->mpaType->id]);

        $this->assertEquals(2, $academicPerson->tasks()->count());
        $this->assertEquals(1, $mpaPerson->tasks()->count());
        $this->assertTrue($academicPerson->tasks->contains($task1));
        $this->assertTrue($academicPerson->tasks->contains($task2));
        $this->assertTrue($mpaPerson->tasks->contains($task2));
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
            'people_id' => $person1->id,
        ]);

        $response->assertRedirect(route('person.tasks.index', $person1->id));
        $response->assertSessionDoesntHaveErrors();
        $this->assertEquals(2, $person1->tasks()->count());
        $this->assertEquals(now()->format('Y-m-d'), $person1->tasks->find($task1->id)->pivot->completed_at->format('Y-m-d'));
    }
}
