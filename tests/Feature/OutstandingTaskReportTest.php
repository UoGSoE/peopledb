<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\People;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OutstandingTaskReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_see_the_outstanding_tasks_by_user_report_page()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $task1 = Task::factory()->create(['description' => 'This is task 1']);
        $task2 = Task::factory()->create(['description' => 'This is task 2']);
        $person->tasks()->sync([$task1->id, $task2->id => ['completed_at' => now(), 'completed_by' => $user->id]]);

        $response = $this->actingAs($user)->get(route('reports.tasks.outstanding'));

        $response->assertOk();
        $response->assertSee('Outstanding Tasks');
        $response->assertSee($person->full_name);
        $response->assertSee($task1->description);
        $response->assertDontSee($task2->description);
    }

    /** @test */
    public function users_can_see_the_outstanding_tasks_by_task_report_page()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $task1 = Task::factory()->create(['description' => 'This is task 1']);
        $task2 = Task::factory()->create(['description' => 'This is task 2']);
        $person->tasks()->sync([$task1->id, $task2->id => ['completed_at' => now(), 'completed_by' => $user->id]]);

        $response = $this->actingAs($user)->get(route('reports.tasks.outstanding_by_task'));

        $response->assertOk();
        $response->assertSee('Outstanding Tasks');
        $response->assertSee($person->full_name);
        $response->assertSee($task1->description);
        $response->assertDontSee($task2->description);
    }
}
