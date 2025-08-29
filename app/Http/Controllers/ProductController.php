<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::included()->filter()->sort()->getOrPaginate();

        return response()->json($products, 200);
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
        $validated = $request->validate([
            'measurement_unit_id' => ['required', 'integer'], // FK no habilitada
            'codigo_estandar' => ['nullable', 'string', 'max:50'],
            'codigo_producto' => ['required', 'string', 'max:50', 'unique:products,codigo_producto'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:150'],
            'precio_unitario' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:Activo,Inactivo'],
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'measurement_unit_id' => ['sometimes', 'integer'],
            'codigo_estandar' => ['sometimes', 'string', 'max:50'],
            'codigo_producto' => ['sometimes', 'string', 'max:50', 'unique:products,codigo_producto,' . $product->id],
            'nombre' => ['sometimes', 'string', 'max:150'],
            'descripcion' => ['sometimes', 'string', 'max:150'],
            'precio_unitario' => ['sometimes', 'numeric', 'min:0'],
            'estado' => ['sometimes', 'in:Activo,Inactivo'],
        ]);

        $product->update($validated);
        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json($product, 204);
    }
}
