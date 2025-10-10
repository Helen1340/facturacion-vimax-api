<?php

namespace App\Http\Controllers;
use App\Models\RadianEvent;

use Illuminate\Http\Request;

class RadianEventController extends Controller
{
    public function index()
    {
        $radian_events = RadianEvent::included()->filter()->sort()->getOrPaginate();
        return response()->json($radian_events);
    }

    public function store(Request $request)
    {
    $request->validate([
        'electronic_document_id' => 'required|exists:electronic_documents,id', // FK documento electrónico
            'event_code'             => 'required|string|max:20',                  // Código del evento RADIAN
            'event_name'             => 'required|string|max:100',                 // Nombre del evento
            'event_date'             => 'required|date',                            // Fecha del evento
            'event_uuid'             => 'required|string|max:64',                  // UUID del evento (CUFE)
            'response_xml'           => 'required|string',                          // XML de respuesta de la DIAN
            'dian_status'            => 'required|string|max:50',                  // Estado devuelto por la DIAN
    ]);

        $radian_event = RadianEvent::create($request->all());
        return response()->json($radian_event, 201);
    }

    public function show($id)
    {
        $radian_event = RadianEvent::findOrFail($id);
        return response()->json($radian_event);
    }

    public function update(Request $request, RadianEvent $radianEvent)
    {
        $request->validate([
            'event_code'   => 'sometimes|required|string|max:20',
            'event_name'   => 'sometimes|required|string|max:100',
            'event_date'   => 'sometimes|required|date',
            'event_uuid'   => 'sometimes|required|string|max:64',
            'response_xml' => 'sometimes|required|string',
            'dian_status'  => 'sometimes|required|string|max:50',
        ]);

        $radianEvent->update($request->only($radianEvent->getFillable()));

        return response()->json($radianEvent);
    }

    public function destroy($id)
    {
        $radian_event = RadianEvent::findOrFail($id);
        $radian_event->delete();
        return response()->json(null, 204);
    }
}
