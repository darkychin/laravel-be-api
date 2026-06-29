<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot list users', function () {
    $response = $this->getJson('/api/admin/users');

    $response->assertStatus(401);
});

test('non-admin user cannot list users', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->getJson('/api/admin/users', [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(403);
});

test('admin user can view all users list including himself', function () {
    $user1 = User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
    $user2 = User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);
    $admin = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@example.com', 'is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->getJson('/api/admin/users', [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(200)
        ->assertJsonCount(3, 'users')
        ->assertJson([
            'users' => [
                ['id' => $user1->id, 'name' => 'Alice', 'email' => 'alice@example.com'],
                ['id' => $user2->id, 'name' => 'Bob', 'email' => 'bob@example.com'],
                ['id' => $admin->id, 'name' => 'Admin User', 'email' => 'admin@example.com', 'is_admin' => true],
            ],
        ]);
});
