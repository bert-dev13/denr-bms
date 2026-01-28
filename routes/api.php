<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
        'remember' => 'boolean'
    ]);

    // For demonstration purposes, we'll return a mock response
    // In a real application, you would use Laravel's authentication system
    if ($credentials['email'] === 'admin@denr.gov.ph' && $credentials['password'] === 'password') {
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => 1,
                'name' => 'DENR Administrator',
                'email' => 'admin@denr.gov.ph',
                'role' => 'admin'
            ],
            'token' => 'mock-jwt-token-' . time(),
            'redirect' => '/dashboard'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials',
        'errors' => [
            'email' => 'Invalid email or password',
            'password' => 'Invalid email or password'
        ]
    ], 401);
});
