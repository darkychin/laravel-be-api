<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class PasswordInvitationController extends Controller
{
    /**
     * Verify the invitation signature.
     */
    public function verify(Request $request): JsonResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired signature.');
        }

        $email = $request->query('email');
        if (! $email) {
            abort(400, 'Email parameter is missing.');
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            abort(404, 'User not found.');
        }

        if ($user->email_verified_at !== null) {
            abort(403, 'Invitation already used.');
        }

        return response()->json([
            'message' => 'Invitation is valid.',
            'email' => $user->email,
        ]);
    }

    /**
     * Set password and mark email as verified.
     */
    public function setPassword(SetPasswordRequest $request): JsonResponse
    {
        // Reconstruct verify URL to check signature
        $verifyUrl = URL::route('invitations.verify', [
            'email' => $request->validated('email'),
            'expires' => $request->validated('expires'),
            'signature' => $request->validated('signature'),
        ]);

        if (! URL::hasValidSignature(Request::create($verifyUrl))) {
            abort(403, 'Invalid or expired signature.');
        }

        $user = User::where('email', $request->validated('email'))->firstOrFail();

        if ($user->email_verified_at !== null) {
            abort(403, 'Invitation already used.');
        }

        $user->password = Hash::make($request->validated('password'));
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'message' => 'Password has been set successfully.',
        ]);
    }
}
