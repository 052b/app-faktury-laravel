<?php

use App\Actions\InvoiceSetPaid;
use App\Actions\InvoiceSetUnpaid;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientGusController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::resource('roles', \App\Http\Controllers\RoleController::class)
    ->middleware(['auth', 'verified']);

Route::resource('permissions', \App\Http\Controllers\PermissionController::class)
    ->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');
    Route::resource('clients', ClientController::class)
        ->except(['show']);
    Route::post('clients/gus', ClientGusController::class)->name('client.gus');

    Route::resource('invoices', InvoiceController::class)
        ->except(['show']);
    Route::post('invoices/set-paid', InvoiceSetPaid::class)->name('invoices.set-paid');
    Route::post('invoices/set-unpaid', InvoiceSetUnpaid::class)->name('invoices.set-unpaid');
});

require __DIR__ . '/auth.php';
