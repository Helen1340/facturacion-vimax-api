<?php

namespace App\Services;

use App\Models\ElectronicDocument;
use App\Models\ElectronicInvoice;
use App\Models\DianStatusResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DianSimulatorService
{

    private $signatureService;

    public function __construct(DigitalSignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    /**
     * Genera un CUFE (Código Único de Factura Electrónica) simulado
     * Formato realista: SHA-384 hash de datos de la factura
     * En producción real sería un hash SHA-384 según especificación DIAN
     */
    public function generateCUFE($invoice)
    {
        $company = $invoice->user->company;

        // Asegurar que issue_date sea un objeto Carbon
        $issueDate = $invoice->issue_date;
        if (is_string($issueDate)) {
            $issueDate = \Carbon\Carbon::parse($issueDate);
        }

        // Datos para generar CUFE (según especificación DIAN)
        $cufeData = [
            $company->nit,
            $invoice->invoice_number,
            $issueDate->format('Y-m-d'),
            number_format($invoice->payable_amount, 2, '.', ''),
            number_format($invoice->tax_inclusive_amount - $invoice->tax_exclusive_amount, 2, '.', ''), // IVA
            $invoice->invoice_type_code,
            $invoice->document_currency_code,
            $company->nit // NIT del adquirente (mismo que emisor en este caso)
        ];

        // Concatenar datos
        $cufeString = implode('', $cufeData);

        // Generar hash SHA-384 (simulado, en producción sería más complejo)
        $hash = hash('sha256', $cufeString . config('app.key'));

        // Formato CUFE: primeros 64 caracteres del hash en mayúsculas
        $cufe = strtoupper(substr($hash, 0, 64));

        // Agregar prefijo para simulación
        return 'CUFE-' . $cufe;
    }

    /**
     * Genera un CUDE (Código Único de Documento Electrónico) simulado
     */
    public function generateCUDE()
    {
        return Str::uuid()->toString();
    }

    /**
     * Simula el envío de factura a la DIAN
     * Retorna: ['success' => true/false, 'message' => '...', 'data' => [...]]
     */
    public function sendInvoiceToDian(ElectronicInvoice $invoice)
    {
        // Validaciones previas antes de enviar
        $validation = $this->validateBeforeSending($invoice);
        if (!$validation['valid']) {
            $invoice->update([
                'dian_status' => 'rejected',
                'sent_at' => now()
            ]);

            return [
                'success' => false,
                'message' => 'La factura no cumple con los requisitos para ser enviada a la DIAN',
                'data' => [
                    'errors' => $validation['errors'],
                    'invoice_number' => $invoice->invoice_number,
                    'status' => 'rejected'
                ]
            ];
        }

        // Simulamos un delay de procesamiento (1-3 segundos) como en la DIAN real
        sleep(rand(1, 3));

        // En desarrollo: siempre aceptar. En producción: 90% de éxito, 10% de rechazo
        $isDevelopment = config('app.env') === 'local' || config('app.debug');
        $success = $isDevelopment ? true : (rand(1, 100) <= 90);

        if ($success) {
            // Generar CUFE único
            $cufe = $this->generateCUFE($invoice);

            // Crear documento electrónico asociado
            $document = $this->createElectronicDocument($invoice, $cufe);

            // Crear respuesta de estado DIAN
            $this->createDianResponse($document, 'accepted');

            // Actualizar estado de la factura
            $invoice->update([
                'dian_status' => 'accepted',
                'uuid' => $cufe,
                'sent_at' => now(),
                'received_at' => now()->addSeconds(rand(5, 15))
            ]);

            return [
                'success' => true,
                'message' => 'Factura procesada exitosamente por la DIAN',
                'data' => [
                    'cufe' => $cufe,
                    'invoice_number' => $invoice->invoice_number,
                    'status' => 'accepted',
                    'protocol_number' => 'PRT-' . str_pad(rand(100000, 999999), 9, '0', STR_PAD_LEFT),
                    'validation_date' => now()->toDateTimeString(),
                    'qr_url' => "https://catalogo-vpfe.dian.gov.co/document/{$cufe}",
                    'invoice' => $invoice->load('invoiceDetails.item', 'user.company', 'electronicDocuments')
                ]
            ];
        } else {
            // Simular rechazo con errores comunes de la DIAN
            $errors = [
                'El NIT del emisor no está autorizado para facturación electrónica',
                'El rango de numeración ha sido agotado',
                'Error en la estructura del XML - Campo obligatorio faltante: cbc:InvoiceTypeCode',
                'El certificado digital ha expirado o no es válido',
                'Inconsistencia en los totales de la factura - El tax_inclusive_amount no coincide',
                'La numeración no corresponde a la resolución DIAN autorizada',
                'El CUFE generado no coincide con el cálculo esperado',
                'Error en la firma digital del documento XML'
            ];

            $errorMessage = $errors[array_rand($errors)];

            $invoice->update([
                'dian_status' => 'rejected',
                'sent_at' => now()
            ]);

            return [
                'success' => false,
                'message' => 'Factura rechazada por la DIAN',
                'data' => [
                    'error' => $errorMessage,
                    'error_code' => 'ERR-' . rand(1000, 9999),
                    'invoice_number' => $invoice->invoice_number,
                    'status' => 'rejected',
                    'is_simulated' => true, // Indicador de que es un error simulado
                    'recommendations' => [
                        'Verifique que todos los datos de la empresa estén correctos',
                        'Valide que la numeración DIAN esté activa',
                        'Revise los totales de la factura',
                        'Contacte a soporte técnico si el error persiste',
                        'NOTA: Este es un error simulado del sistema de pruebas'
                    ]
                ]
            ];
        }
    }

    /**
     * Crea el documento electrónico asociado a la factura
     */
    private function createElectronicDocument(ElectronicInvoice $invoice, $cufe)
    {
        $cude = $this->generateCUDE();

        // Obtener numeración DIAN de la empresa
        $numbering = $invoice->user->company->dianNumberings()
            ->where('document_type', 'Factura')
            ->where('current_status', 'Activo')
            ->first();

        // Generar XML simulado (más completo que antes)
        $xml = $this->generateXmlUBL($invoice, $cufe);

        return ElectronicDocument::create([
            'electronic_invoice_id' => $invoice->id,
            'dian_numbering_id' => $numbering ? $numbering->id : null,
            'cufe' => $cufe,
            'cude' => $cude,
            'xml_document' => $xml,
            'dian_status' => 'Aprobado',
            'validation_date' => now(),
            'digital_signature' => Str::random(50),
            'document_hash' => hash('sha256', $xml),
            'description' => 'Documento electrónico generado automáticamente para Factura Electrónica',
            'environment' => $invoice->user->company->dian_environment ?? 'HABILITACION',
            'document_type' => 'Factura Electrónica',
            'qr_code' => $this->generateQRUrl($invoice, $cufe),
            'cdr' => $this->generateCDR($cufe),
            'emission_mode' => 'normal'
        ]);
    }

    /**
     * Genera XML UBL 2.1 CON FIRMA DIGITAL
     * Incluye todos los campos requeridos según especificación DIAN
     */
    private function generateXmlUBL($invoice, $cufe)
    {
        $company = $invoice->user->company;
        $user = $invoice->user;

        // Cargar detalles con relaciones y buyer (cliente/comprador)
        $invoice->load('invoiceDetails.item.taxes', 'invoiceDetails.item.measurementUnit', 'buyer');

        // Validar que existe un buyer (cliente)
        if (!$invoice->buyer) {
            throw new \Exception('La factura debe tener un cliente (buyer) asignado');
        }

        // Asegurar que issue_date sea un objeto Carbon
        $issueDateObj = $invoice->issue_date;
        if (is_string($issueDateObj)) {
            $issueDateObj = \Carbon\Carbon::parse($issueDateObj);
        }

        // Formatear fecha según UBL (YYYY-MM-DD)
        $issueDate = $issueDateObj->format('Y-m-d');
        $issueTime = $issueDateObj->format('H:i:s');

        // Generar líneas de factura (InvoiceLine)
        $invoiceLines = $this->generateInvoiceLines($invoice);

        // Generar totales de impuestos
        $taxTotal = $this->generateTaxTotal($invoice);

        // Generar información del cliente (usar buyer en lugar de user)
        $customerParty = $this->generateCustomerParty($invoice->buyer);

        // Generar información de pago
        $paymentMeans = $this->generatePaymentMeans($invoice);

        // 1. Generar XML sin firma
        $xmlWithoutSignature = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
         xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <cbc:UBLVersionID>UBL 2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>DIAN 2.1: Factura Electrónica de Venta</cbc:CustomizationID>
    <cbc:ProfileID>DIAN 2.1</cbc:ProfileID>
    <cbc:ID>{$invoice->invoice_number}</cbc:ID>
    <cbc:UUID schemeID="CUFE-SHA384">{$cufe}</cbc:UUID>
    <cbc:IssueDate>{$issueDate}</cbc:IssueDate>
    <cbc:IssueTime>{$issueTime}</cbc:IssueTime>
    <cbc:InvoiceTypeCode listID="01" listAgencyName="PE" listName="Tipo de Documento">{$invoice->invoice_type_code}</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency" listAgencyName="United Nations Economic Commission for Europe">{$invoice->document_currency_code}</cbc:DocumentCurrencyCode>
    <cbc:LineCountNumeric>{$invoice->invoiceDetails->count()}</cbc:LineCountNumeric>
    
    <!-- Emisor (Supplier) -->
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="4" schemeName="31" schemeAgencyName="CO">{$company->nit}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name>{$this->escapeXml($company->business_name)}</cbc:Name>
            </cac:PartyName>
            <cac:PostalAddress>
                <cbc:StreetName>{$this->escapeXml($company->address ?? '')}</cbc:StreetName>
                <cbc:CityName>{$this->escapeXml($company->city ?? '')}</cbc:CityName>
                <cac:Country>
                    <cbc:IdentificationCode listID="ISO 3166-1" listName="Country" listAgencyName="United Nations Economic Commission for Europe" listURI="urn:oasis:names:specification:ubl:codelist:gc:CountryIdentificationCode-2.1">CO</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cac:TaxScheme>
                    <cbc:ID>01</cbc:ID>
                    <cbc:Name>IVA</cbc:Name>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
        </cac:Party>
    </cac:AccountingSupplierParty>
    
    <!-- Cliente (Customer) -->
    {$customerParty}
    
    <!-- Líneas de factura -->
    {$invoiceLines}
    
    <!-- Totales de impuestos -->
    {$taxTotal}
    
    <!-- Información de pago -->
    {$paymentMeans}
    
    <!-- Totales legales -->
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($invoice->line_extension_amount)}</cbc:LineExtensionAmount>
        <cbc:TaxExclusiveAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($invoice->tax_exclusive_amount)}</cbc:TaxExclusiveAmount>
        <cbc:TaxInclusiveAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($invoice->tax_inclusive_amount)}</cbc:TaxInclusiveAmount>
        <cbc:PayableAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($invoice->payable_amount)}</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    
    <!-- FIRMA DIGITAL SE INSERTARÁ AQUÍ -->
    {{SIGNATURE}}
</Invoice>
XML;

        // 2. Firmar el XML
        try {
            $signatureData = $this->signatureService->signXML($xmlWithoutSignature, $company);

            // 3. Insertar la firma en el XML
            $xmlWithSignature = str_replace(
                '{{SIGNATURE}}',
                $signatureData['signature'],
                $xmlWithoutSignature
            );

            return $xmlWithSignature;
        } catch (\Exception $e) {
            // Si no hay certificado, devolver XML sin firma
            Log::warning("No se pudo firmar el XML: " . $e->getMessage());
            return str_replace('{{SIGNATURE}}', '<!-- Sin firma digital -->', $xmlWithoutSignature);
        }
    }


    /**
     * Genera CDR (Acuse de Recibo) simulado
     */
    private function generateCDR($cufe)
    {
        $protocol = 'PRT-' . str_pad(rand(100000, 999999), 9, '0', STR_PAD_LEFT);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<ApplicationResponse>
    <cbc:ResponseCode>00</cbc:ResponseCode>
    <cbc:Description>Procesado Correctamente</cbc:Description>
    <cac:DocumentResponse>
        <cac:Response>
            <cbc:ResponseCode>00</cbc:ResponseCode>
            <cbc:Description>Documento procesado correctamente</cbc:Description>
        </cac:Response>
        <cac:DocumentReference>
            <cbc:ID>{$cufe}</cbc:ID>
        </cac:DocumentReference>
    </cac:DocumentResponse>
    <cbc:ProtocolNumber>{$protocol}</cbc:ProtocolNumber>
</ApplicationResponse>
XML;
    }

    /**
     * Genera URL del código QR en formato DIAN
     */
    // En DianSimulatorService.php
    public function generateQRUrl($invoice, $cufe)
    {
        $company = $invoice->user->company;

        $qrData = [
            'NumFac' => $invoice->invoice_number,
            'FecFac' => is_string($invoice->issue_date)
                ? date('Y-m-d', strtotime($invoice->issue_date))
                : $invoice->issue_date->format('Y-m-d'),
            'NitFac' => $company->nit,
            'DocAdq' => optional($invoice->buyer)->document_number,
            'ValFac' => number_format($invoice->payable_amount, 2, '.', ''),
            'ValIva' => number_format($invoice->tax_inclusive_amount - $invoice->tax_exclusive_amount, 2, '.', ''),
            'ValOtroIm' => '0.00',
            'ValTotal' => number_format($invoice->payable_amount, 2, '.', ''),
            'CUFE' => $cufe
        ];

        return "https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?" . http_build_query($qrData);
    }

    /**
     * Crea la respuesta de estado DIAN
     */
    private function createDianResponse(ElectronicDocument $document, $status)
    {
        $messages = [
            'accepted' => 'La factura fue validada exitosamente y está disponible para consulta en el sistema de la DIAN. Proceso completado sin observaciones.',
            'rejected' => 'El documento no cumple con el esquema XML exigido por la DIAN. Se requiere corrección antes de reenvío.'
        ];

        $descriptions = [
            'accepted' => 'Documento recibido correctamente por la DIAN',
            'rejected' => 'Error en validación del XML'
        ];

        return DianStatusResponse::create([
            'electronic_document_id' => $document->id,
            'status_code' => $status === 'accepted' ? '200' : '400',
            'status_description' => $descriptions[$status],
            'status_message' => $messages[$status],
            'response_xml' => '<ApplicationResponse>Validación DIAN completada</ApplicationResponse>',
            'protocol_number' => 'PRT-' . str_pad(rand(100000, 999999), 9, '0', STR_PAD_LEFT),
            'received_at' => now()
        ]);
    }

    /**
     * Consulta el estado de una factura en la DIAN (simulado)
     */
    public function checkInvoiceStatus($cufe)
    {
        // Simular consulta con delay
        sleep(1);

        $document = ElectronicDocument::where('cufe', $cufe)->first();

        if (!$document) {
            return [
                'success' => false,
                'message' => 'Documento no encontrado en los registros de la DIAN',
                'data' => [
                    'cufe' => $cufe,
                    'status' => 'not_found'
                ]
            ];
        }

        return [
            'success' => true,
            'message' => 'Consulta exitosa',
            'data' => [
                'cufe' => $document->cufe,
                'status' => $document->dian_status,
                'validation_date' => $document->validation_date,
                'qr_url' => $document->qr_code,
                'environment' => $document->environment,
                'document_type' => $document->document_type,
                'protocol_number' => $document->dianStatusResponses->first()->protocol_number ?? null
            ]
        ];
    }

    /**
     * Genera un código QR como imagen base64 (opcional para PDF)
     */
    public function generateQRCodeImage($invoice)
    {
        // Aquí podrías usar una librería como SimpleSoftwareIO/simple-qrcode
        // Por ahora retornamos la URL
        return $this->generateQRUrl($invoice, $invoice->uuid);
    }

    /**
     * Valida que la factura cumpla con los requisitos antes de enviar a DIAN
     */
    private function validateBeforeSending(ElectronicInvoice $invoice)
    {
        $errors = [];
        $company = $invoice->user->company;

        // 1. Validar certificado digital
        $certificateInfo = $this->signatureService->getCertificateInfo($company);
        if (!$certificateInfo['success']) {
            $errors[] = 'La empresa no tiene un certificado digital activo configurado';
        } else {
            $certificate = $certificateInfo['certificate'];
            if ($certificate['days_until_expiry'] < 0) {
                $errors[] = 'El certificado digital ha expirado';
            } elseif ($certificate['days_until_expiry'] < 30) {
                Log::warning("Certificado digital próximo a expirar para empresa {$company->id}");
            }
        }

        // 2. Validar numeración DIAN
        $numbering = $company->dianNumberings()
            ->where('document_type', 'Factura')
            ->where('current_status', 'Activo')
            ->first();

        if (!$numbering) {
            $errors[] = 'No hay numeración DIAN activa para facturas';
        } else {
            $today = now()->toDateString();
            if ($today < $numbering->validity_start_date || $today > $numbering->validity_end_date) {
                $errors[] = 'La resolución DIAN no está vigente';
            }
        }

        // 3. Validar que tenga detalles
        if ($invoice->invoiceDetails->count() === 0) {
            $errors[] = 'La factura debe tener al menos un detalle (producto o servicio)';
        }

        // 4. Validar totales
        if ($invoice->payable_amount <= 0) {
            $errors[] = 'El total a pagar debe ser mayor a cero';
        }

        if ($invoice->tax_inclusive_amount < $invoice->tax_exclusive_amount) {
            $errors[] = 'El total con impuestos no puede ser menor al total sin impuestos';
        }

        // 5. Validar datos de la empresa
        if (!$company->nit || !$company->business_name) {
            $errors[] = 'La empresa debe tener NIT y razón social configurados';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Genera las líneas de factura (InvoiceLine) en formato UBL
     */
    private function generateInvoiceLines($invoice)
    {
        $lines = '';
        $lineNumber = 1;

        foreach ($invoice->invoiceDetails as $detail) {
            $item = $detail->item;
            $unitCode = 'C62'; // C62 = Unidad (por defecto)
            if ($item && $item->relationLoaded('measurementUnit') && $item->measurementUnit) {
                $unitCode = $item->measurementUnit->code ?? 'C62';
            }

            $lines .= <<<XML
    <cac:InvoiceLine>
        <cbc:ID>{$lineNumber}</cbc:ID>
        <cbc:InvoicedQuantity unitCode="{$unitCode}">{$this->formatAmount($detail->quantity)}</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($detail->line_extension_amount)}</cbc:LineExtensionAmount>
        
        <cac:Item>
            <cbc:Description>{$this->escapeXml($detail->description)}</cbc:Description>
            <cac:StandardItemIdentification>
                <cbc:ID>{$item->id}</cbc:ID>
            </cac:StandardItemIdentification>
        </cac:Item>
        
        <cac:Price>
            <cbc:PriceAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($detail->unit_price)}</cbc:PriceAmount>
        </cac:Price>
        
        {$this->generateLineTaxes($detail)}
    </cac:InvoiceLine>
XML;
            $lineNumber++;
        }

        return $lines;
    }

    /**
     * Genera los impuestos de una línea de factura usando los impuestos reales del item
     */
    private function generateLineTaxes($detail)
    {
        $item = $detail->item;

        if (!$item || !$item->relationLoaded('taxes')) {
            // Si no hay item o impuestos cargados, retornar vacío
            return '';
        }

        $taxes = $item->taxes->where('status', 'Activo');

        if ($taxes->isEmpty() || $detail->tax_amount <= 0) {
            return '';
        }

        $taxableAmount = $detail->line_extension_amount;
        $taxSubtotals = '';
        $totalTaxAmount = 0;

        // Generar un TaxSubtotal por cada impuesto activo
        foreach ($taxes as $tax) {
            $taxValue = 0;

            // Calcular el valor del impuesto según su tipo de aplicación
            switch ($tax->application_type) {
                case 'Porcentaje':
                    $taxValue = ($taxableAmount * $tax->percentage) / 100;
                    break;

                case 'ValorFijo':
                    $taxValue = $tax->fixed_value ?? 0;
                    break;

                case 'Retencion':
                    // Las retenciones se calculan pero se muestran como negativas en el XML
                    $taxValue = ($taxableAmount * $tax->percentage) / 100;
                    break;

                default:
                    $taxValue = 0;
            }

            if ($taxValue <= 0 && $tax->application_type !== 'Retencion') {
                continue; // Saltar impuestos con valor 0 (excepto retenciones)
            }

            // Mapear tipo de impuesto a código DIAN
            $taxSchemeId = $this->getTaxSchemeId($tax->type);
            $taxSchemeName = $tax->name;
            $percent = $tax->application_type === 'Porcentaje' ? $tax->percentage : 0;

            // Para retenciones, el valor es negativo
            if ($tax->application_type === 'Retencion') {
                $taxValue = -abs($taxValue);
            }

            $totalTaxAmount += $taxValue;

            $taxSubtotals .= <<<XML
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="COP">{$this->formatAmount($taxableAmount)}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="COP">{$this->formatAmount(abs($taxValue))}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:Percent>{$this->formatAmount($percent)}</cbc:Percent>
                    <cac:TaxScheme>
                        <cbc:ID>{$taxSchemeId}</cbc:ID>
                        <cbc:Name>{$this->escapeXml($taxSchemeName)}</cbc:Name>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
XML;
        }

        if (empty($taxSubtotals)) {
            return '';
        }

        // El TaxAmount total debe ser la suma de todos los impuestos (puede ser negativo si hay retenciones)
        return <<<XML
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="COP">{$this->formatAmount($detail->tax_amount)}</cbc:TaxAmount>
            {$taxSubtotals}
        </cac:TaxTotal>
XML;
    }

    /**
     * Mapea el tipo de impuesto al código DIAN según especificación UBL 2.1
     */
    private function getTaxSchemeId($taxType)
    {
        // Mapeo de tipos de impuesto a códigos DIAN
        $mapping = [
            'IVA' => '01',           // Impuesto sobre las Ventas
            'INC' => '02',           // Impuesto Nacional al Consumo
            'ICA' => '03',           // Impuesto de Industria y Comercio
            'RETEFUENTE' => '03',    // Retención en la Fuente
            'RETEIVA' => '03',       // Retención de IVA
            'RETEICA' => '03',       // Retención de ICA
        ];

        // Buscar coincidencia exacta o parcial
        foreach ($mapping as $key => $code) {
            if (stripos($taxType, $key) !== false) {
                return $code;
            }
        }

        // Por defecto, usar código 01 (IVA) si no se encuentra
        return '01';
    }

    /**
     * Genera el total de impuestos de la factura agrupados por tipo de impuesto
     */
    private function generateTaxTotal($invoice)
    {
        $totalTax = $invoice->tax_inclusive_amount - $invoice->tax_exclusive_amount;

        if ($totalTax <= 0) {
            return '';
        }

        // Agrupar impuestos por tipo desde todos los detalles
        $taxGroups = [];
        $taxableAmount = $invoice->tax_exclusive_amount;

        foreach ($invoice->invoiceDetails as $detail) {
            $item = $detail->item;

            if (!$item || !$item->relationLoaded('taxes')) {
                continue;
            }

            $taxes = $item->taxes->where('status', 'Activo');

            foreach ($taxes as $tax) {
                $taxKey = $tax->type . '_' . ($tax->percentage ?? $tax->fixed_value ?? '0');

                if (!isset($taxGroups[$taxKey])) {
                    $taxGroups[$taxKey] = [
                        'tax' => $tax,
                        'total_amount' => 0,
                        'taxable_amount' => 0
                    ];
                }

                // Calcular el valor del impuesto para esta línea
                $lineTaxableAmount = $detail->line_extension_amount;
                $taxValue = 0;

                switch ($tax->application_type) {
                    case 'Porcentaje':
                        $taxValue = ($lineTaxableAmount * $tax->percentage) / 100;
                        break;

                    case 'ValorFijo':
                        $taxValue = $tax->fixed_value ?? 0;
                        break;

                    case 'Retencion':
                        $taxValue = - ($lineTaxableAmount * $tax->percentage) / 100;
                        break;
                }

                $taxGroups[$taxKey]['total_amount'] += $taxValue;
                $taxGroups[$taxKey]['taxable_amount'] += $lineTaxableAmount;
            }
        }

        if (empty($taxGroups)) {
            // Si no hay impuestos agrupados, usar el total calculado
            return <<<XML
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($totalTax)}</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($taxableAmount)}</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($totalTax)}</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:Percent>0.00</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>01</cbc:ID>
                    <cbc:Name>IVA</cbc:Name>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
XML;
        }

        // Generar TaxSubtotal por cada grupo de impuestos
        $taxSubtotals = '';
        $calculatedTotal = 0;

        foreach ($taxGroups as $group) {
            $tax = $group['tax'];
            $groupTaxAmount = $group['total_amount'];
            $groupTaxableAmount = $group['taxable_amount'];

            if (abs($groupTaxAmount) < 0.01) {
                continue; // Saltar grupos con valor muy pequeño
            }

            $taxSchemeId = $this->getTaxSchemeId($tax->type);
            $taxSchemeName = $tax->name;
            $percent = $tax->application_type === 'Porcentaje' ? $tax->percentage : 0;

            $calculatedTotal += $groupTaxAmount;

            $taxSubtotals .= <<<XML
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($groupTaxableAmount)}</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount(abs($groupTaxAmount))}</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:Percent>{$this->formatAmount($percent)}</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>{$taxSchemeId}</cbc:ID>
                    <cbc:Name>{$this->escapeXml($taxSchemeName)}</cbc:Name>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
XML;
        }

        if (empty($taxSubtotals)) {
            return '';
        }

        return <<<XML
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="{$invoice->document_currency_code}">{$this->formatAmount($totalTax)}</cbc:TaxAmount>
        {$taxSubtotals}
    </cac:TaxTotal>
XML;
    }

    /**
     * Genera información del cliente (AccountingCustomerParty)
     */
    private function generateCustomerParty($user)
    {
        $documentType = $user->document_type ?? 'CC';
        $documentNumber = $user->document_number ?? '';

        return <<<XML
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="{$documentType}">{$this->escapeXml($documentNumber)}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name>{$this->escapeXml($user->first_name ?? 'Cliente')}</cbc:Name>
            </cac:PartyName>
            <cac:PostalAddress>
                <cbc:StreetName>{$this->escapeXml($user->address ?? '')}</cbc:StreetName>
                <cac:Country>
                    <cbc:IdentificationCode listID="ISO 3166-1" listName="Country" listAgencyName="United Nations Economic Commission for Europe" listURI="urn:oasis:names:specification:ubl:codelist:gc:CountryIdentificationCode-2.1">CO</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
        </cac:Party>
    </cac:AccountingCustomerParty>
XML;
    }

    /**
     * Genera información de pago (PaymentMeans)
     */
    private function generatePaymentMeans($invoice)
    {
        $paymentCode = $invoice->payment_means_code ?? '10';
        $paymentName = $invoice->payment_means_name ?? 'Contado';

        return <<<XML
    <cac:PaymentMeans>
        <cbc:PaymentMeansCode listID="UN/ECE 4461" listName="Payment Means" listAgencyName="United Nations Economic Commission for Europe">{$paymentCode}</cbc:PaymentMeansCode>
        <cbc:PaymentID>{$invoice->invoice_number}</cbc:PaymentID>
    </cac:PaymentMeans>
XML;
    }

    /**
     * Formatea un monto para XML (2 decimales)
     */
    private function formatAmount($amount)
    {
        return number_format((float)$amount, 2, '.', '');
    }

    /**
     * Escapa caracteres especiales XML
     */
    private function escapeXml($string)
    {
        return htmlspecialchars($string ?? '', ENT_XML1, 'UTF-8');
    }
}
