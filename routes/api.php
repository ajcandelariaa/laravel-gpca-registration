<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\FastTrackController;
use App\Http\Controllers\MemberController;
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
Route::get('/members', [MemberController::class, 'getListOfMembers']);

Route::get('/fast-track/{code}/{eventCategory}/{eventYear}', [FastTrackController::class, 'getFastTrackDetails']);
Route::get('/fast-track/{code}/{eventCategory}/{eventYear}/print-badge/{delegateId}/{delegateType}', [FastTrackController::class, 'printBadge']);
Route::post('/fast-track/{code}/update-details', [FastTrackController::class, 'updateDetails']);