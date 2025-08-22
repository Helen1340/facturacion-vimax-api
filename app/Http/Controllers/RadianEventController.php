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
            'codigo'                 => 'required|string|max:20',
            'fecha_evento'           => 'required|date',
            'tipo_evento'            => 'required|string|max:50',
            'xml_respuesta'          => 'required|string',
            'estado_dian'            => 'required|string|max:50',
        ]);

        $radian_event = RadianEvent::create($request->all());
        return response()->json($radian_event, 201);
    }

    public function show($id)
    {
        $radian_event = RadianEvent::findOrFail($id);
        return response()->json($radian_event);
    }

    public function update(Request $request, RadianEvent $radian_event)
    {
        $request->validate([
            'codigo'                 => 'sometimes|required|string|max:20',
            'fecha_evento'           => 'sometimes|required|date',
            'tipo_evento'            => 'sometimes|required|string|max:50',
            'xml_respuesta'          => 'sometimes|required|string',
            'estado_dian'            => 'sometimes|required|string|max:50',
        ]);

        $radian_event->update($request->only(array_keys($request->all())));

        return response()->json($radian_event);
    }

    public function destroy($id)
    {
        $radian_event = RadianEvent::findOrFail($id);
        $radian_event->delete();
        return response()->json(null, 204);
    }
}
