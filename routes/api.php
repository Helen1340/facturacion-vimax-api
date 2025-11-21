<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CreditDebitNoteController;
use App\Http\Controllers\DianCredentialController;
use App\Http\Controllers\DianNumberingController;
use App\Http\Controllers\DianStatusResponseController;
use App\Http\Controllers\DigitalCertificateController;
use App\Http\Controllers\ElectronicDocumentController;
use App\Http\Controllers\ElectronicInvoiceController;
use App\Http\Controllers\FCMTokenController;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\NotificationController;
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
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BackupController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// --- Auth ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/fcm-token', [FCMTokenController::class, 'store']);
Route::delete('/fcm-token', [FCMTokenController::class, 'destroy']);

// --- Rutas protegidas ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/completeRegistration', [AuthController::class, 'completeRegistration']);
    
    // CRUD de recursos (todos protegidos por autenticación)
    Route::apiResource('users', UserController::class);

    // routes/api.php - Actualiza el grupo de invoices
    Route::prefix('invoices')->group(function () {
        // CRUD básico
        Route::get('/', [ElectronicInvoiceController::class, 'index']);
        Route::post('/', [ElectronicInvoiceController::class, 'store']);
        Route::get('/{id}', [ElectronicInvoiceController::class, 'show']);
        Route::put('/{id}', [ElectronicInvoiceController::class, 'update']);
        Route::delete('/{id}', [ElectronicInvoiceController::class, 'destroy']);
        
        // Datos para creación de facturas
        Route::get('/create/data', [ElectronicInvoiceController::class, 'createData']);
        
        // Obtener clientes (usuarios con role 'client')
        Route::get('/clients', [ElectronicInvoiceController::class, 'getClients']);
        
        // Acciones DIAN
        Route::post('/{id}/send-dian', [ElectronicInvoiceController::class, 'sendToDian']);
        Route::get('/{id}/status', [ElectronicInvoiceController::class, 'checkStatus']);
        Route::post('/{id}/cancel', [ElectronicInvoiceController::class, 'cancel']);
        Route::get('/{id}/qr', [ElectronicInvoiceController::class, 'generateQR']);
        
        // Descargas y documentos
        // GET /api/invoices/{id}/download/pdf -> Descarga PDF de la factura con diseño
        Route::get('/{id}/download/pdf', [ElectronicInvoiceController::class, 'downloadPDF']);
        Route::get('/{id}/download/xml', [ElectronicInvoiceController::class, 'downloadXML']);
        Route::get('/{id}/preview', [ElectronicInvoiceController::class, 'preview']);
        
        // Crear notas crédito/débito para una factura (POST)
        // Uso: POST /api/invoices/{id}/notes
        // Crea una nota para la factura indicada. Campos: reason, note_type (credit|debit), total_amount.
        Route::post('/{id}/notes', [ElectronicInvoiceController::class, 'createNote']);

        // Listar notas de una factura (GET)
        // Uso: GET /api/invoices/{id}/notes
        // Devuelve todas las notas crédito/débito asociadas a la factura.
        Route::get('/{id}/notes', [ElectronicInvoiceController::class, 'listNotes']);

        // Anular factura con nota crédito total (POST)
        // Uso: POST /api/invoices/{id}/notes/annul
        // Crea una nota crédito por el total de la factura. Campo requerido: reason.
        Route::post('/{id}/notes/annul', [ElectronicInvoiceController::class, 'annulWithCreditNote']);
        
        // Estadísticas y reportes
        Route::get('/stats/summary', [ElectronicInvoiceController::class, 'stats']);
        Route::get('/dashboard/metrics', [ElectronicInvoiceController::class, 'dashboardMetrics']);
    });
    

    // routes/api.php
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('dianStatusResponse', DianStatusResponseController::class);
    Route::apiResource('measurementUnits', MeasurementUnitController::class);

    // detalle-factura
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('paymentMethods', PaymentMethodController::class); // metodo-pago
    Route::apiResource('creditDebitNotes', CreditDebitNoteController::class); // nota-credito-debito
    Route::get('/notes/{id}/download/pdf', [CreditDebitNoteController::class, 'downloadPDF']);
    Route::get('/notes/{id}/download/xml', [CreditDebitNoteController::class, 'downloadXML']);
    Route::apiResource('electronicDocuments', ElectronicDocumentController::class); // documento-electronico
    Route::apiResource('radianEvents', RadianEventController::class);
    Route::apiResource('dianNumberings', DianNumberingController::class); // numeracion-dian
    Route::apiResource('taxes', TaxController::class); // impuesto
    //Route::apiResource('product-taxes', ProductTaxController::class);
    //Route::apiResource('service-taxes', ServiceTaxController::class);
    Route::apiResource('digitalCertificates', DigitalCertificateController::class); // certificado-digital
    Route::apiResource('dianCredential', DianCredentialController::class);
    // ORM Testing
    Route::get('test-all', [OrmController::class, 'testAllRelations']);
    
Route::apiResource('notifications', NotificationController::class);


    Route::apiResource('invoiceDetails', InvoiceDetailController::class);
    
    // Rutas específicas de productos (deben ir antes de apiResource)
    Route::get('products/active', [ProductController::class, 'active']); // Productos activos para facturas
    Route::post('products/{id}/sync-taxes', [ProductController::class, 'syncTaxes']); // Sincronizar impuestos de producto
    Route::apiResource('products', ProductController::class);
    
    // Rutas específicas de servicios (deben ir antes de apiResource)
    Route::get('services/active', [ServiceController::class, 'active']); // Servicios activos para facturas
    Route::post('services/{id}/sync-taxes', [ServiceController::class, 'syncTaxes']); // Sincronizar impuestos de servicio
    Route::apiResource('services', ServiceController::class);
    
    Route::apiResource('electronicInvoices', ElectronicInvoiceController::class); // electronic-invoice-seeder

    Route::prefix('reportes')->group(function () {
        Route::get('/facturas', [ReportController::class, 'reporteFacturas']);
        Route::get('/pagos', [ReportController::class, 'reportePagos']);
        Route::get('/usuarios', [ReportController::class, 'reporteUsuarios']);
        Route::get('/resumen/facturas', [ReportController::class, 'resumenFacturas']);
        Route::get('/resumen/pagos', [ReportController::class, 'resumenPagos']);
        Route::get('/productos', [ReportController::class, 'reporteProductos']);
        Route::get('/servicios', [ReportController::class, 'reporteServicios']);
        Route::get('/resumen/productos', [ReportController::class, 'resumenProductos']);
        Route::get('/resumen/servicios', [ReportController::class, 'resumenServicios']);
    });

    Route::post('/backups/run', [BackupController::class, 'run']);
    Route::get('/backups/download', [BackupController::class, 'download']);
    Route::get('/backups/list', [BackupController::class, 'list']);


    // RUTAS DE CERTIFICADOS DIGITALES
    Route::prefix('certificates')->group(function () {
        Route::get('/info', [DigitalCertificateController::class, 'getInfo']);
        Route::post('/create-test', [DigitalCertificateController::class, 'createTest']);
        Route::get('/', [DigitalCertificateController::class, 'index']);
        Route::post('/{id}/deactivate', [DigitalCertificateController::class, 'deactivate']);
    });

});




