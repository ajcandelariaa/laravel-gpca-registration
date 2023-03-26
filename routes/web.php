<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DelegateController;
use App\Http\Controllers\RegistrationController;
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
        Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [AdminController::class, 'dashboardView'])->name('admin.dashboard.view');

        Route::prefix('event')->group(function () {
            Route::get('/', [EventController::class, 'manageEventView'])->name('admin.event.view');
            Route::prefix('{eventCategory}/{eventId}')->group(function (){
                Route::get('/detail', [EventController::class, 'eventDetailView'])->name('admin.event.detail.view');
                Route::get('/promo-code', [EventController::class, 'eventPromoCodeView'])->name('admin.event.promo-codes.view');
                Route::prefix('registrant')->group(function (){
                    Route::get('/', [RegistrationController::class, 'eventRegistrantsView'])->name('admin.event.registrants.view');
                    Route::get('/{registrantId}', [RegistrationController::class, 'registrantDetailView'])->name('admin.event.registrants.detail.view');
                    Route::get('/{registrantId}/download-invoice', [RegistrationController::class, 'registrantDownloadInvoice'])->name('admin.event.registrants.download.invoice');
                    Route::get('/{registrantId}/view-invoice', [RegistrationController::class, 'registrantViewInvoice'])->name('admin.event.registrants.view.invoice');
                });
                Route::prefix('delegate')->group(function () {
                    Route::get('/', [DelegateController::class, 'eventDelegateView'])->name('admin.event.delegates.view');
                    Route::get('/{delegateType}/{delegateId}', [DelegateController::class, 'delegateDetailView'])->name('admin.event.delegates.detail.view');
                    Route::get('/{delegateType}/{delegateId}/print-badge', [DelegateController::class, 'delegateDetailPrintBadge'])->name('admin.event.delegates.detail.printBadge');
                });
            });
            Route::get('/edit/{eventCategory}/{eventId}', [EventController::class, 'eventEditView'])->name('admin.event.edit.view');
            Route::get('/add', [EventController::class, 'addEventView'])->name('admin.event.add.view');
            Route::post('/add', [EventController::class, 'addEvent'])->name('admin.event.add.post');
            Route::post('/edit/{eventCategory}/{eventId}', [EventController::class, 'updateEvent'])->name('admin.event.edit.post');
        });

        Route::get('/member', [MemberController::class, 'manageMemberView'])->name('admin.member.view');
        Route::get('/delegate', [DelegateController::class, 'manageDelegateView'])->name('admin.delegate.view');

    });

    Route::get('/login', [AdminController::class, 'loginView'])->name('admin.login.view');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');
});

Route::get('/register/{eventYear}/{eventCategory}/{eventId}', [RegistrationController::class, 'registrationView'])->name('register.view');