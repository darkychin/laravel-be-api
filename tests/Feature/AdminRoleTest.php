<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated requests to admin dashboard receive HTTP 401', function () {
    $response = $this->getJson(route('admin.dashboard'));

    $response->assertStatus(401);
});

test('authenticated non-admin users receive HTTP 403', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->getJson(route('admin.dashboard'), [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(403);
});

test('authenticated admin users receive HTTP 200', function () {
    $user = User::factory()->create([
        'is_admin' => true,
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->getJson(route('admin.dashboard'), [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Welcome to the admin dashboard.',
        ]);
});
