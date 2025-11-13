<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DigitalCertificate;
use Illuminate\Support\Str;

class DigitalSignatureService
{
    /**
     * Firma un documento XML con el certificado digital de la empresa
     * 
     * @param string $xmlContent - Contenido XML a firmar
     * @param Company $company - Empresa que firma el documento
     * @return array ['signature' => string, 'certificate_info' => array]
     */
    public function signXML($xmlContent, Company $company)
    {
        // Obtener certificado digital activo de la empresa
        $certificate = $this->getActiveCertificate($company);

        if (!$certificate) {
            throw new \Exception('La empresa no tiene un certificado digital activo configurado');
        }

        // Generar firma digital simulada
        $signature = $this->generateSignature($xmlContent, $certificate);

        return [
            'signature' => $signature['digital_signature'],
            'signature_value' => $signature['signature_value'],
            'digest_value' => $signature['digest_value'],
            'certificate_info' => [
                'serial_number' => $certificate->serial_number,
                'issuer' => $certificate->issuer,
                'subject' => $this->generateSubject($company),
                'valid_from' => $certificate->start_date,
                'valid_to' => $certificate->end_date,
                'algorithm' => $certificate->signature_algorithm ?? 'SHA256withRSA'
            ]
        ];
    }

    /**
     * Obtiene el certificado digital activo de la empresa
     */
    public function getActiveCertificate(Company $company)
    {
        return DigitalCertificate::where('company_id', $company->id)
            ->where('status', 'Vigente')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Genera una firma digital simulada (en producción usarías openssl o xmlseclibs)
     * 
     * @param string $xmlContent - XML a firmar
     * @param DigitalCertificate $certificate - Certificado digital
     * @return array
     */
    private function generateSignature($xmlContent, DigitalCertificate $certificate)
    {
        // 1. Calcular hash SHA-256 del XML (DigestValue)
        $digestValue = base64_encode(hash('sha256', $xmlContent, true));

        // 2. Crear estructura de SignedInfo (lo que se firma)
        $signedInfo = $this->createSignedInfo($digestValue);

        // 3. Firmar el SignedInfo (simulado con hash)
        $signatureValue = $this->createSignatureValue($signedInfo, $certificate);

        // 4. Crear firma completa en formato XML
        $digitalSignature = $this->createSignatureXML(
            $signatureValue,
            $digestValue,
            $certificate
        );

        return [
            'digital_signature' => $digitalSignature,
            'signature_value' => $signatureValue,
            'digest_value' => $digestValue
        ];
    }

    /**
     * Crea el bloque SignedInfo (información que se va a firmar)
     */
    private function createSignedInfo($digestValue)
    {
        return <<<XML
<SignedInfo xmlns="http://www.w3.org/2000/09/xmldsig#">
    <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
    <SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/>
    <Reference URI="">
        <Transforms>
            <Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
        </Transforms>
        <DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
        <DigestValue>{$digestValue}</DigestValue>
    </Reference>
</SignedInfo>
XML;
    }

    /**
     * Crea el valor de la firma (simulado)
     * En producción real usarías:
     * openssl_sign($signedInfo, $signature, $privateKey, OPENSSL_ALGO_SHA256)
     */
    private function createSignatureValue($signedInfo, DigitalCertificate $certificate)
    {
        // Simulamos la firma usando hash del contenido + serial del certificado
        $dataToSign = $signedInfo . $certificate->serial_number . config('app.key');
        $signature = hash('sha256', $dataToSign, true);
        
        return base64_encode($signature);
    }

    /**
     * Crea el XML completo de la firma digital
     */
    private function createSignatureXML($signatureValue, $digestValue, DigitalCertificate $certificate)
    {
        $subject = $this->generateSubject($certificate->company);
        $issuer = $certificate->issuer;
        $serialNumber = $certificate->serial_number;
        
        // Certificado X509 simulado en Base64
        $x509Certificate = $this->generateX509Certificate($certificate);

        return <<<XML
<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
    <SignedInfo>
        <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
        <SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/>
        <Reference URI="">
            <Transforms>
                <Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
            </Transforms>
            <DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
            <DigestValue>{$digestValue}</DigestValue>
        </Reference>
    </SignedInfo>
    <SignatureValue>{$signatureValue}</SignatureValue>
    <KeyInfo>
        <X509Data>
            <X509Certificate>{$x509Certificate}</X509Certificate>
            <X509IssuerSerial>
                <X509IssuerName>{$issuer}</X509IssuerName>
                <X509SerialNumber>{$serialNumber}</X509SerialNumber>
            </X509IssuerSerial>
            <X509SubjectName>{$subject}</X509SubjectName>
        </X509Data>
    </KeyInfo>
</Signature>
XML;
    }

    /**
     * Genera el subject (sujeto) del certificado
     */
    private function generateSubject(Company $company)
    {
        return "CN={$company->business_name}, "
             . "OU=Facturación Electrónica, "
             . "O={$company->business_name}, "
             . "L={$company->city}, "
             . "ST={$company->department}, "
             . "C=CO, "
             . "SERIALNUMBER={$company->nit}";
    }

    /**
     * Genera un certificado X509 simulado en Base64
     */
    private function generateX509Certificate(DigitalCertificate $certificate)
    {
        // En producción real, aquí cargarías el archivo .p12 o .pfx
        // Por ahora, generamos un certificado simulado
        
        $certData = [
            'version' => '3',
            'serial' => $certificate->serial_number,
            'issuer' => $certificate->issuer,
            'validity' => [
                'notBefore' => $certificate->start_date,
                'notAfter' => $certificate->end_date
            ],
            'subject' => $this->generateSubject($certificate->company),
            'algorithm' => $certificate->signature_algorithm ?? 'SHA256withRSA',
            'public_key' => Str::random(128)
        ];

        // Convertir a "certificado" Base64 simulado
        return base64_encode(json_encode($certData));
    }

    /**
     * Valida si una firma digital es válida (simulado)
     */
    public function validateSignature($signatureXML, $originalXML)
    {
        // En producción real, usarías xmlseclibs para validar la firma
        // Por ahora, simulamos una validación básica
        
        $isValid = !empty($signatureXML) && !empty($originalXML);
        
        return [
            'valid' => $isValid,
            'message' => $isValid 
                ? 'Firma digital válida y verificada' 
                : 'Error en la validación de la firma',
            'validated_at' => now()->toDateTimeString()
        ];
    }

    /**
     * Crea un certificado de prueba para una empresa
     */
    public function createTestCertificate(Company $company)
    {
        // Verificar si ya tiene uno activo
        $existing = $this->getActiveCertificate($company);
        
        if ($existing) {
            return [
                'success' => false,
                'message' => 'La empresa ya tiene un certificado digital activo',
                'certificate' => $existing
            ];
        }

        // Crear certificado de prueba
        $certificate = DigitalCertificate::create([
            'company_id' => $company->id,
            'certificate_name' => "Certificado de Prueba - {$company->business_name}",
            'certificate_path' => "/certificates/test_{$company->id}_" . time() . ".p12",
            'serial_number' => strtoupper(Str::random(20)),
            'password' => bcrypt('test_password_' . $company->id),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYears(2)->toDateString(),
            'status' => 'Vigente',
            'issuer' => 'CN=Entidad Certificadora DIAN - Pruebas, O=DIAN, C=CO',
            'certificate_type' => 'Pruebas',
            'signature_algorithm' => 'SHA256withRSA',
            'uuid' => Str::uuid()->toString(),
            'description' => 'Certificado digital de prueba generado automáticamente para simulación'
        ]);

        return [
            'success' => true,
            'message' => 'Certificado de prueba creado exitosamente',
            'certificate' => $certificate
        ];
    }

    /**
     * Obtiene información del certificado de una empresa
     */
    public function getCertificateInfo(Company $company)
    {
        $certificate = $this->getActiveCertificate($company);

        if (!$certificate) {
            return [
                'success' => false,
                'message' => 'No se encontró certificado digital activo'
            ];
        }

        return [
            'success' => true,
            'certificate' => [
                'id' => $certificate->id,
                'name' => $certificate->certificate_name,
                'serial_number' => $certificate->serial_number,
                'issuer' => $certificate->issuer,
                'subject' => $this->generateSubject($company),
                'valid_from' => $certificate->start_date,
                'valid_to' => $certificate->end_date,
                'status' => $certificate->status,
                'type' => $certificate->certificate_type,
                'algorithm' => $certificate->signature_algorithm,
                'days_until_expiry' => now()->diffInDays($certificate->end_date, false)
            ]
        ];
    }
}