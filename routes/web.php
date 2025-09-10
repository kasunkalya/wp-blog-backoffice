<?php

use App\Http\Controllers\Auth\WpAuthController;
use App\Http\Controllers\Api\BackOfficeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', [WpAuthController::class, 'redirect'])->name('login');

Route::get('/admin/{any?}', function () {
    return view('admin');
})->where('any', '.*')->middleware('auth');

Route::get('/login', [WpAuthController::class, 'redirect'])->name('login');
Route::get('/auth/wp/redirect', [WpAuthController::class, 'redirect'])->name('wp.redirect');
Route::get('/auth/wp/callback', [WpAuthController::class, 'callback'])->name('wp.callback');

Route::get('/back-office', function () {
    return Inertia::render('BackOffice');
});

Route::prefix('back-office/api')->middleware(['web', 'auth'])->group(function () {
    Route::get('/ping', [BackOfficeController::class, 'ping']);
    Route::get('/posts', [BackOfficeController::class, 'index']);
    Route::post('/posts', [BackOfficeController::class, 'store']);
    Route::put('/posts/{id}', [BackOfficeController::class, 'update']);
    Route::delete('/posts/{id}', [BackOfficeController::class, 'destroy']);
    Route::post('/posts/{id}/priority', [BackOfficeController::class, 'setPriority']);
    Route::post('/sync', [BackOfficeController::class, 'syncFromWordpress']);
    Route::put('/posts/{id}/priority', [BackOfficeController::class, 'updatePriority']);

});