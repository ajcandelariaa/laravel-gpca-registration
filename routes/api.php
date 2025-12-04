<?php

use App\Http\Controllers\DelegateController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FastTrackController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OnsiteRegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/event/{eventCategory}/{year}', [EventController::class, 'getRegistrationTypes']);
Route::get('/event/{eventCategory}/{year}/attendees', [DelegateController::class, 'apiGetConfirmedDelegatesList']);
Route::get('/members', [MemberController::class, 'getListOfMembers']);

Route::get('/fast-track/{eventCategory}/{eventYear}', [FastTrackController::class, 'getFastTrackDetails']);
Route::post('/fast-track/{eventCategory}/{eventYear}/print-badge', [FastTrackController::class, 'printBadge']);
Route::post('/fast-track/{eventCategory}/{eventYear}/toggle-badge-collected', [FastTrackController::class, 'toggleBadgeCollected']);
Route::post('/fast-track/{eventCategory}/{eventYear}/update-details', [FastTrackController::class, 'updateDetails']);

Route::post('/fast-track/{eventCategory}/{eventYear}/badge-scan', [FastTrackController::class, 'badgeScan']);


// Onsite registration APIs
Route::group(['middleware' => 'api.check.secret.code'], function () {
    Route::prefix('{api_code}')->group(function () {
        Route::group(['middleware' => 'api.check.event.exists'], function () {
            Route::prefix('event/{eventCategory}/{eventId}/onsite-registration')->group(function () {
                Route::get('/fetch-metadata', [OnsiteRegistrationController::class, 'fetchMetadata']);
                Route::post('/validate', [OnsiteRegistrationController::class, 'validateOnsiteRegistration']);
                Route::post('/confirm', [OnsiteRegistrationController::class, 'confirmOnsiteRegistration']);
            });
        });
    });
});
