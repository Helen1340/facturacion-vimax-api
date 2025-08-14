<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Rutas de productos y servicios
Route::apiResource('product_services', ProductServiceController::class)->names('api.v1.product_services');

