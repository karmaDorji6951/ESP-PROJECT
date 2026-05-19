<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Add this to routes/web.php temporarily for debugging

Route::get('/debug/session', function (Request $request) {
    return [
        'session_id' => $request->getSession()->getId(),
        'session_driver' => config('session.driver'),
        'session_lifetime' => config('session.lifetime'),
        'session_exists' => session()->exists('_token'),
        'csrf_token' => csrf_token(),
        'app_key_set' => !empty(config('app.key')),
        'debug_mode' => config('app.debug'),
    ];
});

Route::post('/debug/csrf-test', function (Request $request) {
    return [
        'csrf_passed' => true,
        'message' => 'CSRF token validation passed!',
    ];
})->middleware('web');
