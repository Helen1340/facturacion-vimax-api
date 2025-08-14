<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer = Customer::included()->filter()->sort()->paginate();

        return response()->json($customer);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customer = Customer::create($request->all());

        return response()->json($customer);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $customer = Customer::findOr($id);

        return response()->json($customer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validatedData = $request->validate([
            'Nombre_Completo'    => 'sometimes|string|max:100',
            'Correo_Electronico' => 'sometimes|string|email|max:100|unique:customers,Correo_Electronico,' . $customer->id,
            'Telefono'           => 'sometimes|string|max:15',
            'Razon_Social'       => 'sometimes|string|max:150',
            'Tipo_Persona'       => 'sometimes|in:Natural,Juridica',
            'Tipo_Documento'     => 'sometimes|string|max:20',
            'Observacion'        => 'nullable|string|max:255',
            'Estado'             => 'boolean',
            'Direccion'          => 'sometimes|string|max:150',
            'Pais'               => 'sometimes|string|max:50',
            'Departamento'       => 'sometimes|string|max:50',
            'Fecha'              => 'sometimes|date',
        ]);

        $customer->update($validatedData);

        return $customer;
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        
        return $customer;
    }
}
