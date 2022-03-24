<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\People;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArrivalsAndDeparturesMail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AutomaticEmailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function there_is_a_scheduled_command_to_email_with_a_list_of_upcoming_arrivals_and_departures()
    {
        Mail::fake();
        config(['peopledb.recent_days_leaving' => 20]);
        config(['peopledb.recent_days_arriving' => 20]);
        config(['peopledb.arrivals_departures_recipients' => ['person1@example.com', 'person2@example.com']]);
        $currentPerson = People::factory()->create();
        $leftAgesAgoPerson = People::factory()->create(['end_at' => now()->subDays(100)]);
        $arrivesInAgesPerson = People::factory()->create(['start_at' => now()->addDays(100)]);
        $leavingSoonPerson1 = People::factory()->create(['end_at' => now()->addDays(10)]);
        $leavingSoonPerson2 = People::factory()->create(['end_at' => now()->addDays(15)]);
        $arrivingSoonPerson1 = People::factory()->create(['start_at' => now()->addDays(10)]);
        $arrivingSoonPerson2 = People::factory()->create(['start_at' => now()->addDays(15)]);

        $this->artisan('peopledb:email-arrivals-and-departures')
            ->assertExitCode(0);

        Mail::assertQueued(ArrivalsAndDeparturesMail::class, 2);
        foreach (['person1@example.com', 'person2@example.com'] as $recipient) {
            Mail::assertQueued(ArrivalsAndDeparturesMail::class, function ($mail) use ($recipient, $leavingSoonPerson1, $leavingSoonPerson2, $arrivingSoonPerson1, $arrivingSoonPerson2) {
                return $mail->hasTo($recipient) &&
                    $mail->arrivals->count() === 2 &&
                    $mail->arrivals->contains($arrivingSoonPerson1) &&
                    $mail->arrivals->contains($arrivingSoonPerson2) &&
                    $mail->departures->count() === 2 &&
                    $mail->departures->contains($leavingSoonPerson1) &&
                    $mail->departures->contains($leavingSoonPerson2);
            });
        }
    }
}
