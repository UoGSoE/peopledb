<?php

namespace Tests\Feature;

use App\Models\SiteOption;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OptionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_get_to_the_site_options_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('options.edit'))
            ->assertOk()
            ->assertSee('Site options');
    }

    /** @test */
    public function users_can_change_the_options()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        SiteOption::create([
            'key' => 'recent_days_arriving',
            'value' => '10',
        ]);
        SiteOption::create([
            'key' => 'recent_days_leaving',
            'value' => '10',
        ]);
        SiteOption::create([
            'key' => 'arrivals_departures_recipients',
            'value' => 'fred@example.com',
        ]);

        $this->assertEquals(3, SiteOption::count());

        $response = $this->actingAs($user)->post(route('options.update'), [
                'recent_days_arriving' => '99',
                'recent_days_leaving' => '88',
                'arrivals_departures_recipients' => 'fred@example.com, ginger@example.com',
        ]);

        $response->assertRedirect(route('options.edit'));
        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('success', 'Options updated');
        $this->assertEquals(3, SiteOption::count());
        $this->assertDatabaseHas('site_options', [
            'key' => 'recent_days_arriving',
            'value' => '99',
        ]);
        $this->assertDatabaseHas('site_options', [
            'key' => 'recent_days_leaving',
            'value' => '88',
        ]);
        $this->assertDatabaseHas('site_options', [
            'key' => 'arrivals_departures_recipients',
            'value' => 'fred@example.com, ginger@example.com',
        ]);
    }
}
