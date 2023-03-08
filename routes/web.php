<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome_welcome');
});

Route::prefix('admin')->group(function () {
    Route::middleware(['isAdmin'])->group(function () {
        Route::get('/logout', [AdminController::class, 'logout']);
        Route::get('/dashboard', [AdminController::class, 'dashboardView']);
        Route::get('/event', [AdminController::class, 'manageEventView']);
        Route::get('/member', [AdminController::class, 'manageMemberView']);
    });

    Route::get('/login', [AdminController::class, 'loginView']);
    Route::post('/login', [AdminController::class, 'login']);
});
