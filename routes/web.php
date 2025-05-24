<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PipedriveController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome'); // or your own view/controller
});

Route::get('/pipedrive/stripe-data', [PipedriveController::class, 'getStripeData']);
Route::get('/pipedrive/panel', [PipedriveController::class, 'showPanel']);

// OAuth callback route
Route::get('/oauth/callback', [PipedriveController::class, 'handleCallback'])->name('oauth.callback');

