<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::any('whatsapp/webhook', [\App\Http\Controllers\WhatsappWebHookController::class, 'handle'])->name('whatsapp.webhook');
