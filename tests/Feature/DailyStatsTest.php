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
        $this->setUpPeopleTypes();

        $phd1 = People::factory()->phd()->create();
        $phd2 = People::factory()->phd()->create();
        $leftPhd = People::factory()->phd()->create(['end_at' => now()->subDays(1)]); // shouldnt be counted
        $startingSoonPhd = People::factory()->phd()->create(['start_at' => now()->addDays(1)]); // shouldnt be counted
        $academic1 = People::factory()->academic()->create();
        $academic2 = People::factory()->academic()->create();
        $mpa1 = People::factory()->mpa()->create();
        $mpa2 = People::factory()->mpa()->create();

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
        $phd1 = People::factory()->phd()->create();
        $phd2 = People::factory()->phd()->create();
        $academic1 = People::factory()->academic()->create();
        $academic2 = People::factory()->academic()->create();
        $mpa1 = People::factory()->mpa()->create();
        $mpa2 = People::factory()->mpa()->create();

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
        $this->assertCommandIsScheduled('peopledb:record-stats');
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
