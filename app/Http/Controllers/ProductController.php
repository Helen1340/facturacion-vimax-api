<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/products?status=Active&included=measurementUnit,taxes
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filtrar por status si se envía (por defecto solo activos si no se especifica)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Por defecto, solo productos activos para facilitar uso en facturas
            $query->where('status', 'Active');
        }

        // Aplicar includes, filtros y ordenamiento
        $products = $query->included()->filter()->sort()->getOrPaginate();

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
            'measurement_unit_id' => ['required', 'integer', 'exists:measurement_units,id'], // unidad de medida (FK)
            'standard_code'       => ['nullable', 'string', 'max:50'], // código estándar
            'product_code'        => ['required', 'string', 'max:50', 'unique:products,product_code'], // código interno
            'name'                => ['required', 'string', 'max:150'], // nombre
            'description'         => ['nullable', 'string', 'max:150'], // descripción
            'unit_price'          => ['required', 'numeric', 'min:0'], // precio unitario
            'status'              => ['required', 'in:Active,Inactive'], // estado
        ]);

        // Asignar automáticamente la empresa del usuario logueado
        $user = Auth::user();
        if (!$user || !$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado o sin empresa asociada'
            ], 401);
        }
        $validated['company_id'] = $user->company_id;
        
        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    /**
     * Obtener solo productos activos (útil para facturas)
     * GET /api/products/active
     */
    public function active(Request $request)
    {
        $products = Product::where('status', 'Active')
            ->with(['measurementUnit', 'taxes'])
            ->select('id', 'product_code', 'name', 'description', 'unit_price', 'measurement_unit_id', 'status')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'measurement_unit_id' => ['sometimes', 'integer', 'exists:measurement_units,id'],
            'standard_code'       => ['nullable', 'string', 'max:50'],
            'product_code'        => ['sometimes', 'string', 'max:50', 'unique:products,product_code,' . $product->id],
            'name'                => ['sometimes', 'string', 'max:150'],
            'description'         => ['nullable', 'string', 'max:150'],
            'unit_price'          => ['sometimes', 'numeric', 'min:0'],
            'status'              => ['sometimes', 'in:Active,Inactive'],
        ]);

        // Debug: Log los datos recibidos y validados
        Log::info('Product Update Request:', [
            'original_request' => $request->all(),
            'validated_data' => $validated,
            'product_id' => $product->id
        ]);

        $product->update($validated);
        
        // Debug: Log el producto actualizado
        Log::info('Product Updated:', $product->fresh()->toArray());
        
        return response()->json($product->fresh(), 200);
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
