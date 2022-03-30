<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use App\Models\DailyStat;
use App\Models\PeopleType;
use App\Stats\DailyStatsRecorder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DailyStatsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_record_stats_about_the_data()
    {
        $phd1 = People::factory()->create(['type' => PeopleType::PHD_STUDENT]);
        $phd2 = People::factory()->create(['type' => PeopleType::PHD_STUDENT]);
        $leftPhd = People::factory()->create(['type' => PeopleType::PHD_STUDENT, 'end_at' => now()->subDays(1)]); // shouldnt be counted
        $startingSoonPhd = People::factory()->create(['type' => PeopleType::PHD_STUDENT, 'start_at' => now()->addDays(1)]); // shouldnt be counted
        $academic1 = People::factory()->create(['type' => PeopleType::ACADEMIC]);
        $academic2 = People::factory()->create(['type' => PeopleType::ACADEMIC]);
        $mpa1 = People::factory()->create(['type' => PeopleType::MPA]);
        $mpa2 = People::factory()->create(['type' => PeopleType::MPA]);

        (new DailyStatsRecorder())->record();

        $this->assertEquals(1, DailyStat::count());
        tap(DailyStat::first(), function ($stat) {
            $this->assertEquals(6, $stat->total_count);
            $this->assertEquals(2, $stat->phd_students_count);
            $this->assertEquals(2, $stat->academics_count);
            $this->assertEquals(2, $stat->mpas_count);
            $this->assertEquals(0, $stat->technicians_count);
            $this->assertEquals(now()->format('Y-m-d'), $stat->date->format('Y-m-d'));
        });
    }

    /** @test */
    public function recording_the_stats_twice_still_results_in_one_record()
    {
        $phd1 = People::factory()->create(['type' => PeopleType::PHD_STUDENT]);
        $phd2 = People::factory()->create(['type' => PeopleType::PHD_STUDENT]);
        $academic1 = People::factory()->create(['type' => PeopleType::ACADEMIC]);
        $academic2 = People::factory()->create(['type' => PeopleType::ACADEMIC]);
        $mpa1 = People::factory()->create(['type' => PeopleType::MPA]);
        $mpa2 = People::factory()->create(['type' => PeopleType::MPA]);

        (new DailyStatsRecorder())->record();

        $this->assertEquals(1, DailyStat::count());
        tap(DailyStat::first(), function ($stat) {
            $this->assertEquals(6, $stat->total_count);
            $this->assertEquals(2, $stat->phd_students_count);
            $this->assertEquals(2, $stat->academics_count);
            $this->assertEquals(2, $stat->mpas_count);
            $this->assertEquals(0, $stat->technicians_count);
            $this->assertEquals(now()->format('Y-m-d'), $stat->date->format('Y-m-d'));
        });

        $mpa1->update(['end_at' => now()->subDays(1)]);

        (new DailyStatsRecorder())->record();

        $this->assertEquals(1, DailyStat::count());
        tap(DailyStat::first(), function ($stat) {
            $this->assertEquals(5, $stat->total_count);
            $this->assertEquals(2, $stat->phd_students_count);
            $this->assertEquals(2, $stat->academics_count);
            $this->assertEquals(1, $stat->mpas_count);
            $this->assertEquals(0, $stat->technicians_count);
            $this->assertEquals(now()->format('Y-m-d'), $stat->date->format('Y-m-d'));
        });
    }

    /** @test */
    public function there_is_an_artisan_command_that_is_scheduled_to_record_the_stats()
    {
        $phd1 = People::factory()->create(['type' => PeopleType::PHD_STUDENT]);
        $phd2 = People::factory()->create(['type' => PeopleType::PHD_STUDENT]);
        $academic1 = People::factory()->create(['type' => PeopleType::ACADEMIC]);
        $academic2 = People::factory()->create(['type' => PeopleType::ACADEMIC]);
        $mpa1 = People::factory()->create(['type' => PeopleType::MPA]);
        $mpa2 = People::factory()->create(['type' => PeopleType::MPA]);

        $this->assertCommandIsScheduled('peopledb:record-stats');

        $this->artisan('peopledb:record-stats')->assertExitCode(0);

        $this->assertEquals(1, DailyStat::count());
        tap(DailyStat::first(), function ($stat) {
            $this->assertEquals(6, $stat->total_count);
            $this->assertEquals(2, $stat->phd_students_count);
            $this->assertEquals(2, $stat->academics_count);
            $this->assertEquals(2, $stat->mpas_count);
            $this->assertEquals(0, $stat->technicians_count);
            $this->assertEquals(now()->format('Y-m-d'), $stat->date->format('Y-m-d'));
        });
    }

    /** @test */
    public function there_is_a_page_where_users_can_view_the_stats()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.stats'));

        $response->assertOk();
        $response->assertSee('Stats and Trends');
    }
}
