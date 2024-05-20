<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        User::query()->forceDelete();
    }

    public function testCreate(): void
    {
        $user = User::factory()->create();
        self::assertNotNull(User::find($user->id));
    }
}
