<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot delete users', function () {
    $user = User::factory()->create();

    $response = $this->deleteJson("/api/admin/users/{$user->id}");

    $response->assertStatus(401);
});

test('non-admin user cannot delete users', function () {
    $user = User::factory()->create();
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $token = $nonAdmin->createToken('test-token')->plainTextToken;

    $response = $this->deleteJson("/api/admin/users/{$user->id}", [], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(403);
});

test('admin user can delete another user', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->deleteJson("/api/admin/users/{$user->id}", [], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'User deleted successfully.',
        ]);

    $this->assertSoftDeleted('users', [
        'id' => $user->id,
    ]);
});

test('admin can create user with same email as a soft-deleted user', function () {
    $user = User::factory()->create([
        'email' => 'duplicate@example.com',
    ]);
    $user->delete(); // Soft delete it

    $admin = User::factory()->create(['is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    // Try creating a new user with the same email
    $response = $this->postJson('/api/admin/users', [
        'name' => 'New User',
        'email' => 'duplicate@example.com',
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', [
        'email' => 'duplicate@example.com',
        'name' => 'New User',
        'deleted_at' => null,
    ]);
});

test('admin user cannot delete themselves', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->deleteJson("/api/admin/users/{$admin->id}", [], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'You cannot delete your own account.',
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
    ]);
});
