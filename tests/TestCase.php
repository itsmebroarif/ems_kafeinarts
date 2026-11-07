<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function actingAsAdmin(): TestCase
    {
        $user = User::factory()->create([
            'email' => 'admin'.uniqid().'@kafeinarts.test',
        ]);

        return $this->actingAs($user);
    }
}
