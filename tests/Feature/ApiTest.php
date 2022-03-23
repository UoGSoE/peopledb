<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected $apiSystemUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->validApiToken = ApiToken::factory()->create();
    }

    /** @test */
    public function we_need_to_supply_an_api_key_to_access_the_api_endpoints()
    {
    }
}
