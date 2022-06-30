<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\People;
use App\Events\PersonIsLeaving;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use App\Mail\NotifySupervisorOfLeavingPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

class SupervisorCanChangeLeavingDateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_someone_is_leaving_their_supervisor_is_emailed_to_let_them_know()
    {
        Mail::fake();
        $supervisor = People::factory()->academic()->create();
        $phd = People::factory()->phd()->create(['reports_to' => $supervisor->id]);

        event(new PersonIsLeaving($phd));

        Mail::assertQueued(NotifySupervisorOfLeavingPerson::class, 1);
        Mail::assertQueued(NotifySupervisorOfLeavingPerson::class, function ($mail) use ($phd) {
            return $mail->hasTo($phd->reportsTo->email) &&
                $mail->supervisee->is($phd);
        });
    }

    /** @test */
    public function the_email_the_supervisor_is_sent_has_a_secure_url_in_it_to_let_them_alter_the_date()
    {
        $supervisor = People::factory()->academic()->create();
        $phd = People::factory()->phd()->create(['reports_to' => $supervisor->id]);

        $mail = new NotifySupervisorOfLeavingPerson($supervisor, $phd);

        $mail->assertSeeInText($phd->end_at->format('d/m/Y'));
        $mail->assertSeeInText($phd->full_name);
        $mail->assertSeeInText($phd->email);
        $mail->assertSeeInText(route('supervisor.edit_leaving_date_supervisee', [
            'supervisee' => $phd->id,
            'supervisor' => $supervisor->id,
        ]));
    }

    /** @test */
    public function the_supervisor_can_visit_the_link_and_see_the_form_to_edit_the_leaving_date()
    {
        $supervisor = People::factory()->academic()->create();
        $phd = People::factory()->phd()->create(['reports_to' => $supervisor->id]);

        $response = $this->get(
            URL::temporarySignedRoute(
                'supervisor.edit_leaving_date_supervisee',
                now()->addWeeks(4),
                [
                    'supervisee' => $phd->id,
                    'supervisor' => $supervisor->id,
                ]
            )
        );

        $response->assertOk();
        $response->assertSee($phd->full_name);
        $response->assertSee($phd->end_at->format('Y-m-d')); // html date input format is always 'Y-m-d'
    }

    /** @test */
    public function the_supervisor_can_update_the_leaving_date_of_the_supervisee()
    {
        $supervisor = People::factory()->academic()->create();
        $phd = People::factory()->phd()->create(['reports_to' => $supervisor->id, 'end_at' => now()]);

        $response = $this->post(
            URL::temporarySignedRoute(
                'supervisor.update_leaving_date_supervisee',
                now()->addWeeks(4),
                [
                    'supervisee' => $phd->id,
                    'supervisor' => $supervisor->id,
                ]
            ),
            [
                'end_at' => now()->addWeeks(30)->format('Y-m-d'),
            ]
        );

        $response->assertRedirect(
            URL::temporarySignedRoute(
                'supervisor.edit_leaving_date_supervisee',
                now()->addWeeks(4),
                [
                    'supervisee' => $phd->id,
                    'supervisor' => $supervisor->id,
                ]
            )
        );
        $response->assertSessionHas('success');
        $this->assertEquals(now()->addWeeks(30)->format('Y-m-d'), $phd->fresh()->end_at->format('Y-m-d'));
    }

    /** @test */
    public function invalid_or_expired_links_dont_work()
    {
        $supervisor = People::factory()->academic()->create();
        $phd = People::factory()->phd()->create(['reports_to' => $supervisor->id, 'end_at' => now()]);
        $otherPhd = People::factory()->phd()->create(['reports_to' => null, 'end_at' => now()]);

        // get to an expired link
        $response = $this->get(
            URL::temporarySignedRoute(
                'supervisor.edit_leaving_date_supervisee',
                now()->subWeeks(4),
                [
                    'supervisee' => $phd->id,
                    'supervisor' => $supervisor->id,
                ]
            )
        );

        $response->assertUnauthorized();
        $this->assertEquals(now()->format('Y-m-d'), $phd->fresh()->end_at->format('Y-m-d'));

        // post to an expired link
        $response = $this->post(
            URL::temporarySignedRoute(
                'supervisor.update_leaving_date_supervisee',
                now()->subWeeks(4),
                [
                    'supervisee' => $phd->id,
                    'supervisor' => $supervisor->id,
                ]
            ),
            [
                'end_at' => now()->addWeeks(30)->format('Y-m-d'),
            ]
        );

        $response->assertUnauthorized();
        $this->assertEquals(now()->format('Y-m-d'), $phd->fresh()->end_at->format('Y-m-d'));

        // get to link for a supervisee not supervised by the supervisor
        $response = $this->get(
            URL::temporarySignedRoute(
                'supervisor.edit_leaving_date_supervisee',
                now()->addWeeks(4),
                [
                    'supervisee' => $otherPhd->id,
                    'supervisor' => $supervisor->id,
                ]
            )
        );

        $response->assertUnauthorized();

        // post to a link for a supervisee not supervised by the supervisor
        $response = $this->post(
            URL::temporarySignedRoute(
                'supervisor.update_leaving_date_supervisee',
                now()->addWeeks(4),
                [
                    'supervisee' => $otherPhd->id,
                    'supervisor' => $supervisor->id,
                ]
            ),
            [
                'end_at' => now()->addWeeks(30)->format('Y-m-d'),
            ]
        );

        $response->assertUnauthorized();
        $this->assertEquals(now()->format('Y-m-d'), $otherPhd->fresh()->end_at->format('Y-m-d'));
    }
}
