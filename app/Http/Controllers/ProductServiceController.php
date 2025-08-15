<?php

namespace App\Http\Controllers;

use App\Models\ProductService;
use Illuminate\Http\Request;

class ProductServiceController extends Controller
{
    // lista con filtros, relaciones y paginacion
    public function index()
    {

        $product_services = ProductService::included()->filter()->sort()->getOrPaginate();
        return response()->json($product_services);

    }

    // crear un nuevo producto / servicio
    public function store(Request $request)
    {

        $request->validate([
                'codigo_producto_servicio' => 'required|integer|unique:product_services,CodigoProductoServicio',
                'costo_unitario' => 'required|numeric',
                'tipo' => 'required|in:Producto,Servicio',
                'nombre' => 'required|string|max:150',
                'descripcion' => 'nullable|string|max:150',
                'usuario_creacion' => 'required|string|max:20',
                'porcentaje_iva' => 'required|numeric|between:0,999.99',
                'aplica_impuesto' => 'required|boolean',
                'estado' => 'required|boolean',
        ]);


        $product_service = ProductService::create($request->all());
        return response()->json($product_service);
    }

    // mostrar un producto / servicio por id
    public function show($id) //si se pasa $id se utiliza la comentada
    {
        $product_service = ProductService::findOrFail($id);

        return response()->json($product_service);
    }


    public function update(Request $request, ProductService $product_service)
    {
    $request->validate([
        'codigo_producto_servicio' => 'sometimes|integer|unique:product_services,CodigoProductoServicio,' . $product_service->CodigoProductoServicio ,
        'costo_unitario' => 'sometimes|numeric',
        'tipo' => 'sometimes|in:Producto,Servicio',
        'nombre' => 'sometimes|string|max:150',
        'descripcion' => 'sometimes|nullable|string|max:150',
        'usuario_creacion' => 'sometimes|string|max:20',
        'porcentaje_iva' => 'sometimes|numeric|between:0,999.99',
        'aplica_impuesto' => 'sometimes|boolean',
        'estado' => 'sometimes|boolean',
    ]);

    // Actualiza solo los campos que vienen en el request
    $product_service->update($request->only(array_keys($request->all())));

    //Actualiza el campo pero siempretenemos que poner o validar nit, razon_social y tipo_documento
    //$company->update($request->all()); // Linea del Repositorio del Instrucor

    return response()->json($product_service);
    }

    // eliminar un producto / servicio
    public function destroy(ProductService $product_service)
    {
    $product_service->delete();

    return response()->json(null, 204);
    }


}
