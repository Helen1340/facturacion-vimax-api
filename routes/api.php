<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DigitalCertificateController;
use App\Http\Controllers\InvoiceNumberController;
use App\Http\Controllers\TaxController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// rutas customers
Route::resource('customers', CustomerController::class);

