<?php

namespace App\Http\Controllers;
use App\Models\RadianEvent;

use Illuminate\Http\Request;

class RadianEventController extends Controller
{
    public function index()
    {
        $radian_events = RadianEvent::included()->filter()->sort()->getOrPaginate();
        return response()->json($radian_events, 200);
    }

    public function store(Request $request)
    {
    $request->validate([
        'electronic_document_id' => 'required|exists:electronic_documents,id', // FK documento electrónico
            'event_code'             => 'required|string|max:10',                  // Código del evento RADIAN
            'event_name'             => 'required|string|max:100',                 // Nombre del evento
            'event_date'             => 'required|date',                            // Fecha del evento
            'event_uuid'             => 'required|string|max:64',                  // UUID del evento (CUFE)
            'response_xml'           => 'required|string',                          // XML de respuesta de la DIAN
            'dian_status'            => 'required|in:pending,accepted,rejected,error,cancelled',                  // Estado devuelto por la DIAN
    ]);

        $radian_event = RadianEvent::create($request->all());
        return response()->json($radian_event, 201);
    }

    public function show($id)
    {
        $radian_event = RadianEvent::findOrFail($id);
        return response()->json($radian_event, 200);
    }

    public function update(Request $request, RadianEvent $radianEvent)
    {
        $validated = $request->validate([
            'electronic_document_id' => 'sometimes|exists:electronic_documents,id',
            'event_code'             => 'sometimes|string|max:10',
            'event_name'             => 'sometimes|string|max:100',
            'event_date'             => 'sometimes|date',
            'event_uuid'             => 'sometimes|string|max:64',
            'response_xml'           => 'sometimes|string',
            'dian_status'            => 'sometimes|in:pending,accepted,rejected,error,cancelled',
        ]);

        // Debug: Log los datos recibidos y validados
        \Log::info('RadianEvent Update Request:', [
            'original_request' => $request->all(),
            'validated_data' => $validated,
            'radian_event_id' => $radianEvent->id
        ]);

        $radianEvent->update($validated);
        
        // Debug: Log el evento actualizado
        \Log::info('RadianEvent Updated:', $radianEvent->fresh()->toArray());

        return response()->json($radianEvent->fresh(), 200);
    }

    public function destroy($id)
    {
        $radian_event = RadianEvent::findOrFail($id);
        $radian_event->delete();
        return response()->json(null, 204);
    }
}
