<?php

namespace App\Http\Controllers;

use App\Models\DianStatusResponse;
use Illuminate\Http\Request;

class DianStatusResponseController extends Controller
{
    // Listar respuestas (puede incluir electronicDocument)
    public function index()
    {
        $responses = DianStatusResponse::included()->filter()->sort()->getOrPaginate();
        return response()->json($responses, 200);
    }

    // Guardar nueva respuesta DIAN
    public function store(Request $request)
    {
        $validated = $request->validate([
            'electronic_document_id' => ['required','exists:electronic_documents,id'],
            'status_code'            => ['required','string','max:20'],
            'status_description'     => ['required','string','max:150'],
            'status_message'         => ['nullable','string'],
            'response_xml'           => ['nullable','string'],
            'protocol_number'        => ['nullable','string','max:100'],
            'received_at'            => ['nullable','date'],
        ]);

    

        $response = DianStatusResponse::create($validated);
        return response()->json($response, 201);
    }

    // Mostrar una respuesta
    public function show($id)
    {
        $response = DianStatusResponse::with('electronicDocument')->findOrFail($id);
        return response()->json($response, 200);
    }

    // Actualizar una respuesta (si aplica)
    public function update(Request $request, DianStatusResponse $dianStatusResponse)
    {
        $validated = $request->validate([
            'status_code'        => ['sometimes','string','max:20'],
            'status_description' => ['sometimes','string','max:150'],
            'status_message'     => ['sometimes','string'],
            'response_xml'       => ['sometimes','string'],
            'protocol_number'    => ['sometimes','string','max:100'],
            'received_at'        => ['sometimes','date'],
        ]);

        

        $dianStatusResponse->update($validated);
        return response()->json($dianStatusResponse, 200);
    }

    // Eliminar (solo si aplica en tu política de retención)
    public function destroy(DianStatusResponse $dianStatusResponse)
    {
        $dianStatusResponse->delete();
        return response()->json(null, 204);
    }
}
