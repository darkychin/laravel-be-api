<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthenticatedUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): User
    {
        return $request->user();
    }
}
