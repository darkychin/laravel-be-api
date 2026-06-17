<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum')->name('user');

Route::get('/admin/dashboard', function () {
    return response()->json([
        'message' => 'Welcome to the admin dashboard.',
    ]);
})->middleware(['auth:sanctum', 'can:admin'])->name('admin.dashboard');
