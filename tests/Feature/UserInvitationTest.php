<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('guest cannot create users', function () {
    $response = $this->postJson('/api/admin/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response->assertStatus(401);
});

test('non-admin user cannot create users', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->postJson('/api/admin/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(403);
});

test('admin user can create users and receives a signed invitation URL', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->postJson('/api/admin/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'invitation_url',
            'user' => ['id', 'name', 'email'],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'name' => 'John Doe',
        'email_verified_at' => null,
    ]);

    $invitationUrl = $response->json('invitation_url');
    $this->assertStringContainsString('/api/invitations/verify', $invitationUrl);
    $this->assertStringContainsString('signature=', $invitationUrl);
    $this->assertStringContainsString('email=john%40example.com', $invitationUrl);
});

test('admin store user validation rules', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->postJson('/api/admin/users', [], [
        'Authorization' => 'Bearer '.$token,
    ]);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);

    User::factory()->create(['email' => 'john@example.com']);
    $response = $this->postJson('/api/admin/users', [
        'name' => 'John New',
        'email' => 'john@example.com',
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('verify invitation URL works with valid signature', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'email_verified_at' => null,
    ]);

    $url = URL::temporarySignedRoute(
        'invitations.verify',
        now()->addDays(7),
        ['email' => $user->email]
    );

    $response = $this->getJson($url);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Invitation is valid.',
            'email' => 'john@example.com',
        ]);
});

test('verify invitation URL fails with invalid signature', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'email_verified_at' => null,
    ]);

    $url = URL::temporarySignedRoute(
        'invitations.verify',
        now()->addDays(7),
        ['email' => $user->email]
    );

    $tamperedUrl = $url.'tampered';

    $response = $this->getJson($tamperedUrl);

    $response->assertStatus(403);
});

test('verify invitation URL fails when expired', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'email_verified_at' => null,
    ]);

    $url = URL::temporarySignedRoute(
        'invitations.verify',
        now()->addMinutes(5),
        ['email' => $user->email]
    );

    $this->travelTo(now()->addMinutes(6));

    $response = $this->getJson($url);

    $response->assertStatus(403);
});

test('verify invitation URL fails if user already verified', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'email_verified_at' => now(),
    ]);

    $url = URL::temporarySignedRoute(
        'invitations.verify',
        now()->addDays(7),
        ['email' => $user->email]
    );

    $response = $this->getJson($url);

    $response->assertStatus(403)
        ->assertJsonFragment(['message' => 'Invitation already used.']);
});

test('setting password works with valid invitation details', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'email_verified_at' => null,
    ]);

    $url = URL::temporarySignedRoute(
        'invitations.verify',
        now()->addDays(7),
        ['email' => $user->email]
    );

    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $params);

    $response = $this->postJson('/api/invitations/set-password', [
        'email' => 'john@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'signature' => $params['signature'],
        'expires' => $params['expires'],
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Password has been set successfully.',
        ]);

    $user->refresh();
    $this->assertNotNull($user->email_verified_at);
    $this->assertTrue(Hash::check('newpassword123', $user->password));
});

test('setting password fails with invalid signature', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'email_verified_at' => null,
    ]);

    $url = URL::temporarySignedRoute(
        'invitations.verify',
        now()->addDays(7),
        ['email' => $user->email]
    );

    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $params);

    $response = $this->postJson('/api/invitations/set-password', [
        'email' => 'john@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'signature' => $params['signature'].'invalid',
        'expires' => $params['expires'],
    ]);

    $response->assertStatus(403);

    $user->refresh();
    $this->assertNull($user->email_verified_at);
});

test('setting password fails if expired', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'email_verified_at' => null,
    ]);

    $url = URL::temporarySignedRoute(
        'invitations.verify',
        now()->addMinutes(5),
        ['email' => $user->email]
    );

    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $params);

    $this->travelTo(now()->addMinutes(6));

    $response = $this->postJson('/api/invitations/set-password', [
        'email' => 'john@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'signature' => $params['signature'],
        'expires' => $params['expires'],
    ]);

    $response->assertStatus(403);
});

test('setting password fails if already verified', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'email_verified_at' => now()->subDay(),
    ]);

    $url = URL::temporarySignedRoute(
        'invitations.verify',
        now()->addDays(7),
        ['email' => $user->email]
    );

    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $params);

    $response = $this->postJson('/api/invitations/set-password', [
        'email' => 'john@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'signature' => $params['signature'],
        'expires' => $params['expires'],
    ]);

    $response->assertStatus(403)
        ->assertJsonFragment(['message' => 'Invitation already used.']);
});

test('setting password validation rules', function () {
    $response = $this->postJson('/api/invitations/set-password', [
        'email' => 'invalid-email',
        'password' => 'short',
        'password_confirmation' => 'mismatch',
        'signature' => '',
        'expires' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password', 'signature', 'expires']);
});

test('guest cannot resend invitation link', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $response = $this->postJson("/api/admin/users/{$user->id}/resend-invitation");
    $response->assertStatus(401);
});

test('non-admin user cannot resend invitation link', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $token = $nonAdmin->createToken('test-token')->plainTextToken;

    $response = $this->postJson("/api/admin/users/{$user->id}/resend-invitation", [], [
        'Authorization' => 'Bearer '.$token,
    ]);
    $response->assertStatus(403);
});

test('admin can resend invitation link for unverified user', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $admin = User::factory()->create(['is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->postJson("/api/admin/users/{$user->id}/resend-invitation", [], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['invitation_url', 'message']);

    $invitationUrl = $response->json('invitation_url');
    $this->assertStringContainsString('/api/invitations/verify', $invitationUrl);
    $this->assertStringContainsString('signature=', $invitationUrl);
});

test('admin cannot resend invitation link for already verified user', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $admin = User::factory()->create(['is_admin' => true]);
    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->postJson("/api/admin/users/{$user->id}/resend-invitation", [], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertStatus(400)
        ->assertJsonFragment(['message' => 'User already active.']);
});
