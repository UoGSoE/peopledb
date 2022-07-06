<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\People;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OutstandingTasksExport;
use Illuminate\Foundation\Testing\WithFaker;
use App\Exports\OutstandingTasksByTaskExport;
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
    public function people_with_no_outstanding_tasks_dont_appear_on_the_page()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $person2 = People::factory()->create();
        $task1 = Task::factory()->create(['description' => 'This is task 1']);
        $task2 = Task::factory()->create(['description' => 'This is task 2']);
        $person->tasks()->sync([$task1->id, $task2->id => ['completed_at' => now(), 'completed_by' => $user->id]]);

        $response = $this->actingAs($user)->get(route('reports.tasks.outstanding'));

        $response->assertOk();
        $response->assertSee('Outstanding Tasks');
        $response->assertSee($person->full_name);
        $response->assertDontSee($person2->full_name);
        $response->assertSee($task1->description);
        $response->assertDontSee($task2->description);
    }

    /** @test */
    public function if_there_are_no_outstanding_tasks_we_get_a_friendly_message()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $person2 = People::factory()->create();
        $task1 = Task::factory()->create(['description' => 'This is task 1']);
        $task2 = Task::factory()->create(['description' => 'This is task 2']);

        $response = $this->actingAs($user)->get(route('reports.tasks.outstanding'));

        $response->assertOk();
        $response->assertSee('Outstanding Tasks');
        $response->assertSee('No outstanding tasks found');
        $response->assertDontSee($person->full_name);
        $response->assertDontSee($person2->full_name);
        $response->assertDontSee($task1->description);
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

    /** @test */
    public function if_there_are_no_outstanding_tasks_on_the_by_task_report_we_get_a_friendly_message()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $person2 = People::factory()->create();
        $task1 = Task::factory()->create(['description' => 'This is task 1']);
        $task2 = Task::factory()->create(['description' => 'This is task 2']);

        $response = $this->actingAs($user)->get(route('reports.tasks.outstanding_by_task'));

        $response->assertOk();
        $response->assertSee('Outstanding Tasks');
        $response->assertSee('No outstanding tasks found');
        $response->assertDontSee($person->full_name);
        $response->assertDontSee($person2->full_name);
        $response->assertDontSee($task1->description);
        $response->assertDontSee($task2->description);
    }

    /** @test */
    public function we_can_export_the_list_tasks_by_person_as_an_excel_file()
    {
        Excel::fake();
        $user = User::factory()->create();
        $person1 = People::factory()->create();
        $person2 = People::factory()->create();
        $person3 = People::factory()->create();
        $task1 = Task::factory()->create(['description' => 'This is task 1']);
        $task2 = Task::factory()->create(['description' => 'This is task 2']);
        $person1->tasks()->sync([$task1->id, $task2->id => ['completed_at' => now(), 'completed_by' => $user->id]]);
        $person2->tasks()->sync([$task1->id, $task2->id]);

        $response = $this->actingAs($user)->get(route('reports.tasks.outstanding.export'));

        $response->assertOk();
        Excel::assertDownloaded('Outstanding Tasks By Person ' . now()->format('d-m-Y') . '.xlsx', function (OutstandingTasksExport $export) use ($person1, $person2, $person3, $task1, $task2) {
            return $export->collection()->contains('person', $person1->full_name) &&
                $export->collection()->contains('person', $person2->full_name) &&
                $export->collection()->doesntContain('person', $person3->full_name) &&
                $export->collection()->contains('task', $task1->description) &&
                $export->collection()->contains('task', $task2->description);
        });
    }

    /** @test */
    public function we_can_export_the_list_tasks_by_task_as_an_excel_file()
    {
        Excel::fake();
        $user = User::factory()->create();
        $person1 = People::factory()->create();
        $person2 = People::factory()->create();
        $person3 = People::factory()->create();
        $task1 = Task::factory()->create(['description' => 'This is task 1']);
        $task2 = Task::factory()->create(['description' => 'This is task 2']);
        $person1->tasks()->sync([$task1->id, $task2->id => ['completed_at' => now(), 'completed_by' => $user->id]]);
        $person2->tasks()->sync([$task1->id, $task2->id]);

        $response = $this->actingAs($user)->get(route('reports.tasks.outstanding_by_task.export'));

        $response->assertOk();
        Excel::assertDownloaded('Outstanding Tasks By Task ' . now()->format('d-m-Y') . '.xlsx', function (OutstandingTasksByTaskExport $export) use ($person1, $person2, $person3, $task1, $task2) {
            return $export->collection()->contains('person', $person1->full_name) &&
                $export->collection()->contains('person', $person2->full_name) &&
                $export->collection()->doesntContain('person', $person3->full_name) &&
                $export->collection()->contains('task', $task1->description) &&
                $export->collection()->contains('task', $task2->description);
        });
    }
}
