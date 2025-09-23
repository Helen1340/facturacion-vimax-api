<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CreditDebitNoteController;
use App\Http\Controllers\DianNumberingController;
use App\Http\Controllers\DigitalCertificateController;
use App\Http\Controllers\ElectronicDocumentController;
use App\Http\Controllers\ElectronicInvoiceController;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\RadianEventController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrmController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// --- Auth ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Rutas protegidas ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::apiResource('users', UserController::class);

// routes/api.php
Route::apiResource('companies',CompanyController::class);
Route::apiResource('roles', RoleController::class);

Route::apiResource('measurementUnints', MeasurementUnitController::class); // unidad-medida
 // detalle-factura
Route::apiResource('payments', PaymentController::class);
Route::apiResource('paymentMethods', PaymentMethodController::class); // metodo-pago
Route::apiResource('creditDebitNotes', CreditDebitNoteController::class); // nota-credito-debito
Route::apiResource('electronicDocuments', ElectronicDocumentController::class); // documento-electronico
Route::apiResource('radianEvents', RadianEventController::class);
Route::apiResource('dianNumberings', DianNumberingController::class); // numeracion-dian
Route::apiResource('taxes', TaxController::class); // impuesto
//Route::apiResource('product-taxes', ProductTaxController::class);
//Route::apiResource('service-taxes', ServiceTaxController::class);
Route::apiResource('digitalCertificates', DigitalCertificateController::class); // certificado-digital


// ORM Testing
Route::get('test-all', [OrmController::class, 'testAllRelations']);



});

Route::apiResource('invoiceDetails', InvoiceDetailController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('electronicInvoices', ElectronicInvoiceController::class); // electronic-invoice-seeder