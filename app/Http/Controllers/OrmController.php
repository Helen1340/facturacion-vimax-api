<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Company;
use App\Models\User;
use App\Models\ElectronicInvoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\Service;
use App\Models\Tax;
use App\Models\MeasurementUnit;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\CreditDebitNote;
use App\Models\RadianEvent;
use App\Models\DianNumbering;
use App\Models\ElectronicDocument;
use App\Models\DigitalCertificate;
use App\Models\Role;

class OrmController extends Controller
{
    public function showCompany(Company $company): JsonResponse
    {
        $company->load(['users', 'digitalCertificates']);

        return response()->json([
            'company' => $company
        ]);
    }

    public function showUser(User $user): JsonResponse
    {
        $user->load(['company', 'electronicInvoices', 'role']);

        return response()->json([
            'user' => $user
        ]);
    }

    public function showElectronicInvoice(ElectronicInvoice $invoice): JsonResponse
    {
        $invoice->load([
            'user',
            'invoiceDetails',
            'payment',
            'creditDebitNotes',
            'radianEvents',
            'ElectronicDocuments'
        ]);

        return response()->json([
            'electronicInvoice' => $invoice
        ]);
    }

    public function showInvoiceDetail(InvoiceDetail $detail): JsonResponse
    {
        try {
            $detail->load(['electronicInvoice', 'item']);
            return response()->json([
                'invoiceDetail' => $detail
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function showProduct(Product $product): JsonResponse
    {
        $product->load(['measurementUnit', 'invoiceDetails', 'taxes']);

        return response()->json([
            'product' => $product
        ]);
    }

    public function showService(Service $service): JsonResponse
    {
        $service->load(['measurementUnit', 'invoiceDetails', 'taxes']);

        return response()->json([
            'service' => $service
        ]);
    }

    public function showTax(Tax $tax): JsonResponse
    {
        $tax->load(['products', 'services']);

        return response()->json([
            'tax' => $tax
        ]);
    }

    public function showMeasurementUnit(MeasurementUnit $unit): JsonResponse
    {
        $unit->load(['products', 'services']);

        return response()->json([
            'measurementUnit' => $unit
        ]);
    }

    public function showPayment(Payment $payment): JsonResponse
    {
        $payment->load(['electronicInvoice', 'paymentMethod']);

        return response()->json([
            'payment' => $payment
        ]);
    }

    public function showPaymentMethod(PaymentMethod $method): JsonResponse
    {
        $method->load(['payments']);

        return response()->json([
            'paymentMethod' => $method
        ]);
    }

    public function showCreditDebitNote(CreditDebitNote $note): JsonResponse
    {
        $note->load(['electronicInvoice']);

        return response()->json([
            'creditDebitNote' => $note
        ]);
    }

    public function showRadianEvent(RadianEvent $event): JsonResponse
    {
        $event->load(['electronicDocument']);

        return response()->json([
            'radianEvent' => $event
        ]);
    }

    public function showDianNumbering(DianNumbering $numbering): JsonResponse
    {
        $numbering->load(['company', 'electronicDocuments']);

        return response()->json([
            'dianNumbering' => $numbering
        ]);
    }

    public function showElectronicDocument(ElectronicDocument $document): JsonResponse
    {
        $document->load(['dianNumbering', 'electronicInvoice', 'creditDebitNote', 'radianEvents']);

        return response()->json([
            'electronicDocument' => $document
        ]);
    }

    public function showDigitalCertificate(DigitalCertificate $certificate): JsonResponse
    {
        $certificate->load(['company']);

        return response()->json([
            'digitalCertificate' => $certificate
        ]);
    }

    public function showRole(Role $role): JsonResponse
    {
        $role->load(['users']);

        return response()->json([
            'role' => $role
        ]);
    }


    public function testAllRelations(Request $request): JsonResponse
    {
        $results = [];

        // Role
        $role = Role::first();
        if ($role) {
            $role->load(['users']);
            $results['role'] = $role;
        }

        // Company
        $company = Company::first();
        if ($company) {
            $company->load(['users', 'digitalCertificates', 'dianNumberings']);
            $results['company'] = $company;
        }

        // User
        $user = User::first();
        if ($user) {
            $user->load(['company', 'electronicInvoices', 'role']);
            $results['user'] = $user;
        }

        // ElectronicInvoice
        $invoice = ElectronicInvoice::first();
        if ($invoice) {
            $invoice->load([
                'user',
                'invoiceDetails',
                'payment',
                'creditDebitNotes'
            ]);
            $results['electronicInvoice'] = $invoice;
        }

        // InvoiceDetail
        $detail = InvoiceDetail::first();
        if ($detail) {
            $detail->load(['electronicInvoice', 'item']);
            $results['invoiceDetail'] = $detail;
        }

        // Product
        $product = Product::first();
        if ($product) {
            $product->load(['measurementUnit', 'invoiceDetails', 'taxes']);
            $results['product'] = $product;
        }

        // Service
        $service = Service::first();
        if ($service) {
            $service->load(['measurementUnit', 'invoiceDetails', 'taxes']);
            $results['service'] = $service;
        }

        // Tax
        $tax = Tax::first();
        if ($tax) {
            $tax->load(['products', 'services']);
            $results['tax'] = $tax;
        }

        // MeasurementUnit
        $unit = MeasurementUnit::first();
        if ($unit) {
            $unit->load(['products', 'services']);
            $results['measurementUnit'] = $unit;
        }

        // Payment
        $payment = Payment::first();
        if ($payment) {
            $payment->load(['electronicInvoice', 'paymentMethod']);
            $results['payment'] = $payment;
        }

        // PaymentMethod
        $paymentMethod = PaymentMethod::first();
        if ($paymentMethod) {
            $paymentMethod->load(['payments']);
            $results['paymentMethod'] = $paymentMethod;
        }

        // CreditDebitNote
        $note = CreditDebitNote::first();
        if ($note) {
            $note->load(['electronicInvoice', 'electronicDocuments']);
            $results['creditDebitNote'] = $note;
        }

        // RadianEvent
        $event = RadianEvent::first();
        if ($event) {
            $event->load(['electronicDocument']);
            $results['radianEvent'] = $event;
        }

        // DocumentNumbering
        $numbering = DianNumbering::first();
        if ($numbering) {
            $numbering->load(['company', 'electronicDocuments']);
            $results['dianNumbering'] = $numbering;
        }

        // ElectronicDocument
        $document = ElectronicDocument::first();
        if ($document) {
            $document->load(['dianNumbering', 'creditDebitNote', 'radianEvents', 'electronicInvoice']);
            $results['electronicDocument'] = $document;
        }

        // DigitalCertificate
        $certificate = DigitalCertificate::first();
        if ($certificate) {
            $certificate->load(['company']);
            $results['digitalCertificate'] = $certificate;
        }

        return response()->json([
            'message' => 'Todas las relaciones probadas exitosamente',
            'total_models_tested' => count($results),
            'data' => $results
        ]);
    }
}
