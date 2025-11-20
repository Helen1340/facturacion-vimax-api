<?php

namespace App\Http\Controllers;

use App\Services\DigitalSignatureService;
use App\Models\Company;
use App\Models\DigitalCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Para verificar fechas

class DigitalCertificateController extends Controller
{
    private $signatureService;

    public function __construct(DigitalSignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    // =========================================================
    // MÉTODOS API RESOURCE (CRUD BÁSICO)
    // Usados con Route::apiResource('digitalCertificates', ...)
    // =========================================================

    /**
     * Listar todos los certificados.
     * GET /api/digitalCertificates
     */
    public function index(Request $request)
    {
        try {
            // Este es el 'index' que lista certificados de la empresa del usuario
            $loggedUser = Auth::user();
            $certificates = DigitalCertificate::where('company_id', $loggedUser->company_id)->orderBy('id', 'desc')->get();

            // Mapear los nombres de columnas al formato esperado por el frontend
            $mapped = $certificates->map(function ($cert) {
                return [
                    'id' => $cert->id,
                    'nombre_certificado' => $cert->certificate_name,
                    'numero_serial' => $cert->serial_number,
                    'entidad_emisora' => $cert->issuer,
                    'fecha_emision' => $cert->start_date,
                    'fecha_vencimiento' => $cert->end_date,
                    'estado_actual' => $this->mapStatus($cert->status),
                    'certificate_type' => $cert->certificate_type,
                    'signature_type' => $cert->signature_type,
                    'descripcion' => $cert->description,
                    'company_id' => $cert->company_id,
                ];
            });

            return response()->json($mapped, 200);
        } catch (\Exception $e) {
            Log::error('Error al listar certificados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener certificados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo certificado.
     * POST /api/digitalCertificates
     */
    public function store(Request $request)
    {
        try {
            Log::info('Datos recibidos para crear certificado:', $request->all());

            $loggedUser = Auth::user();
            if (!$loggedUser || !$loggedUser->company_id) {
                return response()->json(['success' => false, 'message' => 'Usuario no autenticado o sin empresa asociada'], 401);
            }

            $validated = $request->validate([
                'certificate_name' => 'required|string|max:225',
                'serial_number' => 'required|string|max:100',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'signature_type' => 'required|string|in:digital,electronica,electrónica',
                'certificate_type' => 'required|string|in:Producción,Produccion,Pruebas,ProducciÃ³n',
                'issuer' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'password' => 'nullable|string|max:255',
                'certificate_file' => 'nullable|file|mimes:pfx,p12|max:5120', // 5MB
                'status' => 'nullable|string|in:Vigente,Vencido,Revocado'
            ]);

            $certificatePath = null;
            if ($request->hasFile('certificate_file')) {
                $file = $request->file('certificate_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $certificatePath = $file->storeAs('certificates', $fileName, 'public');
                Log::info('Archivo guardado en: ' . $certificatePath);
            }

            $certificate = DigitalCertificate::create([
                'company_id' => $loggedUser->company_id,
                'certificate_name' => $validated['certificate_name'],
                'serial_number' => $validated['serial_number'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'signature_type' => $validated['signature_type'],
                'certificate_type' => $validated['certificate_type'],
                'issuer' => $validated['issuer'] ?? '',
                'description' => $validated['description'] ?? '',
                'certificate_path' => $certificatePath ?? '',
                'password' => $validated['password'] ?? '',
                'status' => $validated['status'] ?? 'Vigente',
                'signature_algorithm' => 'SHA256withRSA',
                'uuid' => null
            ]);

            Log::info('Certificado creado exitosamente:', ['id' => $certificate->id]);

            return response()->json([
                'success' => true,
                'message' => 'Certificado creado exitosamente',
                'data' => $certificate
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validaciÃ³n:', $e->errors());
            return response()->json(['success' => false, 'message' => 'Error de validaciÃ³n', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear certificado: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear el certificado: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un certificado especÃ­fico.
     * GET /api/digitalCertificates/{id}
     */
    public function show($id)
    {
        try {
            $certificate = DigitalCertificate::findOrFail($id);
            $loggedUser = Auth::user();
            if (!$loggedUser || $certificate->company_id !== $loggedUser->company_id) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
            }
            return response()->json(['success' => true, 'data' => $certificate], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Certificado no encontrado'], 404);
        }
    }

    /**
     * Actualizar un certificado.
     * PUT/PATCH /api/digitalCertificates/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Actualizando certificado ID: ' . $id);

            $certificate = DigitalCertificate::findOrFail($id);
            $loggedUser = Auth::user();
            if (!$loggedUser || $certificate->company_id !== $loggedUser->company_id) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
            }

            $validated = $request->validate([
                'certificate_name' => 'required|string|max:225',
                'serial_number' => 'required|string|max:100',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'signature_type' => 'required|string|in:digital,electronica,electrónica',
                'certificate_type' => 'required|string|in:Producción,Produccion,Pruebas',
                'issuer' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'password' => 'nullable|string|max:255',
                'certificate_file' => 'nullable|file|mimes:pfx,p12|max:5120', // 5MB
                'status' => 'nullable|string|in:Vigente,Vencido,Revocado'
            ]);

            // Manejo de archivo
            if ($request->hasFile('certificate_file')) {
                if ($certificate->certificate_path && Storage::disk('public')->exists($certificate->certificate_path)) {
                    Storage::disk('public')->delete($certificate->certificate_path);
                }
                $file = $request->file('certificate_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $validated['certificate_path'] = $file->storeAs('certificates', $fileName, 'public');
            }

            // Actualizar certificado
            $certificate->update($validated);

            Log::info('Certificado actualizado exitosamente');

            return response()->json(['success' => true, 'message' => 'Certificado actualizado exitosamente', 'data' => $certificate], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validaciÃ³n:', $e->errors());
            return response()->json(['success' => false, 'message' => 'Error de validaciÃ³n', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar certificado: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar el certificado: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un certificado.
     * DELETE /api/digitalCertificates/{id}
     */
    public function destroy($id)
    {
        try {
            $certificate = DigitalCertificate::findOrFail($id);
            $loggedUser = Auth::user();
            if (!$loggedUser || $certificate->company_id !== $loggedUser->company_id) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
            }

            // Eliminar el archivo del certificado si existe
            if ($certificate->certificate_path && Storage::disk('public')->exists($certificate->certificate_path)) {
                Storage::disk('public')->delete($certificate->certificate_path);
                Log::info('Archivo eliminado: ' . $certificate->certificate_path);
            }

            $certificate->delete();

            Log::info('Certificado eliminado exitosamente: ID ' . $id);

            return response()->json(['success' => true, 'message' => 'Certificado eliminado exitosamente'], 200);

        } catch (\Exception $e) {
            Log::error('Error al eliminar certificado: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el certificado: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================
    // MÉTODOS PERSONALIZADOS (Requieren rutas separadas)
    // =========================================================

    /**
     * Obtener informaciÃ³n del certificado de la empresa.
     * GET /api/certificates/info?company_id=1
     */
    public function getInfo(Request $request)
    {
        $companyId = $request->input('company_id');

        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Se requiere el ID de la empresa'], 400);
        }

        $company = Company::findOrFail($companyId);
        $result = $this->signatureService->getCertificateInfo($company);

        return response()->json($result);
    }

    /**
     * Crear certificado de prueba.
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
     * Listar certificados de una empresa (Mantenido el nombre 'index' del código original, pero renombrado).
     * GET /api/certificates?company_id=1
     */
    public function indexByCompany(Request $request)
    {
        $companyId = $request->input('company_id');

        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Se requiere el ID de la empresa'], 400);
        }

        $certificates = DigitalCertificate::where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $certificates]);
    }

    /**
     * Desactivar certificado.
     * POST /api/certificates/{id}/deactivate
     */
    public function deactivate($id)
    {
        $certificate = DigitalCertificate::findOrFail($id);

        $certificate->update([
            'status' => 'Revocado'
        ]);

        return response()->json(['success' => true, 'message' => 'Certificado desactivado exitosamente', 'data' => $certificate]);
    }

    /**
     * Verificar validez del certificado
     * POST /api/digitalCertificates/{id}/verify
     */
    public function verify($id)
    {
        try {
            $certificate = DigitalCertificate::findOrFail($id);

            $now = now();
            $endDate = Carbon::parse($certificate->end_date);

            $status = 'valido';
            if ($now->greaterThan($endDate)) {
                $status = 'vencido';
                $certificate->update(['status' => 'Vencido']);
            }

            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $status === 'valido' ? 'El certificado es vÃ¡lido' : 'El certificado ha vencido',
                'data' => $certificate
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al verificar el certificado: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mapear estado de BD al formato del frontend
     */
    private function mapStatus($status)
    {
        $statusMap = [
            'Vigente' => 'valido',
            'Vencido' => 'vencido',
            'Revocado' => 'bloqueado'
        ];

        return $statusMap[$status] ?? 'valido';
    }
}