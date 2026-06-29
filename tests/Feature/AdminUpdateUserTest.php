<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot update users', function () {
    $user = User::factory()->create();

    $response = $this->putJson("/api/admin/users/{$user->id}", [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $response->assertStatus(401);
});

test('non-admin user cannot update users', function () {
    $user = User::factory()->create();
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $token = $nonAdmin->createToken('test-token')->plainTextToken;

    $response = $this->putJson("/api/admin/users/{$user->id}", [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(403);
});

test('admin user can update a user\'s name and email', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
    $admin = User::factory()->create(['is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->putJson("/api/admin/users/{$user->id}", [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'User updated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('admin update user validation rules', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
    $admin = User::factory()->create(['is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    // Test required fields
    $response = $this->putJson("/api/admin/users/{$user->id}", [], [
        'Authorization' => 'Bearer '.$token,
    ]);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);

    // Test unique email rule ignores the current user being updated
    $response = $this->putJson("/api/admin/users/{$user->id}", [
        'name' => 'New Name',
        'email' => 'original@example.com', // same email
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);
    $response->assertStatus(200);

    // Test unique email rule checks other users
    $otherUser = User::factory()->create(['email' => 'taken@example.com']);
    $response = $this->putJson("/api/admin/users/{$user->id}", [
        'name' => 'New Name',
        'email' => 'taken@example.com',
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
