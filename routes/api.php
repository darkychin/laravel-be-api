<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedUserController;
use App\Http\Controllers\Auth\PasswordInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/user', AuthenticatedUserController::class)
    ->middleware('auth:sanctum')
    ->name('user');

Route::get('/admin/dashboard', DashboardController::class)
    ->middleware(['auth:sanctum', 'can:admin'])
    ->name('admin.dashboard');

Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware(['auth:sanctum', 'can:admin'])
    ->name('admin.users.index');

Route::post('/admin/users', [UserController::class, 'store'])
    ->middleware(['auth:sanctum', 'can:admin'])
    ->name('admin.users.store');

Route::put('/admin/users/{user}', [UserController::class, 'update'])
    ->middleware(['auth:sanctum', 'can:admin'])
    ->name('admin.users.update');

Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can:admin'])
    ->name('admin.users.destroy');

Route::post('/admin/users/{user}/resend-invitation', [UserController::class, 'resend'])
    ->middleware(['auth:sanctum', 'can:admin'])
    ->name('admin.users.resend');

Route::get('/invitations/verify', [PasswordInvitationController::class, 'verify'])
    ->name('invitations.verify');

Route::post('/invitations/set-password', [PasswordInvitationController::class, 'setPassword'])
    ->name('invitations.set-password');
