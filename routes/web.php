<?php

use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\InvoiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
Route::get('/', function () {
    return view('index');
});
// Clients Routes
Route::get('/clients/data', [ClientController::class, 'getData'])->name('clients.data');
Route::resource('clients', ClientController::class);

// Invoices Routes
Route::get('/invoices/data', [InvoiceController::class, 'getData'])->name('invoices.data');
Route::resource('invoices', InvoiceController::class);
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
