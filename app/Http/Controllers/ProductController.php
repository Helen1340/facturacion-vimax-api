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
            'measurement_unit_id' => ['required', 'integer'], // unidad de medida (FK)
            'standard_code'       => ['nullable', 'string', 'max:50'], // código estándar
            'product_code'        => ['required', 'string', 'max:50', 'unique:products,product_code'], // código interno
            'name'                => ['required', 'string', 'max:150'], // nombre
            'description'         => ['nullable', 'string', 'max:150'], // descripción
            'unit_price'          => ['required', 'numeric', 'min:0'], // precio unitario
            'status'              => ['required', 'in:Activo,Inactivo'], // estado
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::findorfail($id);

        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'measurement_unit_id' => ['sometimes', 'integer'],
            'standard_code'       => ['sometimes', 'string', 'max:50'],
            'product_code'        => ['sometimes', 'string', 'max:50', 'unique:products,product_code,' . $product->id],
            'name'                => ['sometimes', 'string', 'max:150'],
            'description'         => ['sometimes', 'string', 'max:150'],
            'unit_price'          => ['sometimes', 'numeric', 'min:0'],
            'status'              => ['sometimes', 'in:Activo,Inactivo'],
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
