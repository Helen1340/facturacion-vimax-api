<?php

namespace App\Http\Controllers;

use App\Services\DigitalSignatureService;
use App\Models\Company;
use App\Models\DigitalCertificate;
use Illuminate\Http\Request;

class DigitalCertificateController extends Controller
{
    private $signatureService;

    public function __construct(DigitalSignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    /**
     * Obtener información del certificado de la empresa
     * GET /api/certificates/info?company_id=1
     */
    public function getInfo(Request $request)
    {
        $companyId = $request->input('company_id');
        
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere el ID de la empresa'
            ], 400);
        }

        $company = Company::findOrFail($companyId);
        $result = $this->signatureService->getCertificateInfo($company);

        return response()->json($result);
    }

    /**
     * Crear certificado de prueba
     * POST /api/certificates/create-test
     * { "company_id": 1 }
     */
    public function createTest(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id'
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $result = $this->signatureService->createTestCertificate($company);

        return response()->json($result, $result['success'] ? 201 : 400);
    }

    /**
     * Listar certificados de una empresa
     * GET /api/certificates?company_id=1
     */
    public function index(Request $request)
    {
        $companyId = $request->input('company_id');
        
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere el ID de la empresa'
            ], 400);
        }

        $certificates = DigitalCertificate::where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $certificates
        ]);
    }

    /**
     * Desactivar certificado
     * POST /api/certificates/{id}/deactivate
     */
    public function deactivate($id)
    {
        $certificate = DigitalCertificate::findOrFail($id);
        
        $certificate->update([
            'status' => 'Revocado'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificado desactivado exitosamente',
            'data' => $certificate
        ]);
    }
}