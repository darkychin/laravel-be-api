<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreUserRequest;
use App\Http\Requests\AdminUpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'users' => User::orderBy('id')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminStoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make(Str::random(32)),
        ]);

        return response()->json([
            'invitation_url' => $user->generateInvitationUrl(),
            'user' => $user,
        ], 201);
    }

    /**
     * Resend user invitation.
     */
    public function resend(User $user): JsonResponse
    {
        if ($user->email_verified_at !== null) {
            return response()->json([
                'message' => 'User already active.',
            ], 400);
        }

        return response()->json([
            'invitation_url' => $user->generateInvitationUrl(),
            'message' => 'New invitation URL generated successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateUserRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\Illuminate\Http\Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }
}
