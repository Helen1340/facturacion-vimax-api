<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductService extends Model
{
    //
    protected $fillable = [
        'codigo_producto_servicio',
        'costo_unitario',
        'tipo',
        'nombre',
        'descripcion',
        'usuario_creacion',
        'porcentaje_iva',
        'aplica_impuesto',
        'estado'
    ];

    // listas blancas
    protected $allowIncluded = ['UnitOfMeasure', 'Taxes', 'InvoiceDetail']; ////relaciones que el cliente puede pedir vía included
    protected $allowFilter = [ 'nombre', 'tipo', 'estado', 'costo_unitario', 'descripcion', 'usuario_creación', 'porcentaje_iva', 'aplica_impuesto' ]; //columnas que se pueden filtrar vía ?filter[field]=value
    protected $allowSort = [ 'nombre', 'tipo', 'estado', 'costo_unitario', 'porcentaje_iva' ]; //columnas que se pueden ordenar vía ?sort=columna o ?sort=-columna



        // relaciones

    // relacion con unidad de medida de uno a muchos: muchos productos o servicios pueden tener una unidad de medida y una unuidad de medida puede tener muchos productos o servicios
    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class);
    }

    // relacion uno a muchos con detalles de factura: un producto o servicio puede tener muchos detalles de factura y un detalle de factura pertenece a un producto o servicio
    public function invoiceDetail()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    // scopes
    public function scopeIncluded(Builder $query)
    {
        if (empty($this->allowIncluded) || empty(request('included'))) { // validamos que la lista blanca y la variable included enviada a travez de HTTP no este en vacia.
            return;
        }


        // return request('included');

        $relations  = explode(',', request('included')); //['posts','relation2']//recuperamos el valor de la variable included y separa sus valores por una coma

         //return $relations;


        $allowIncluded = collect($this->allowIncluded); //colocamos en una colecion lo que tiene $allowIncluded en este caso = ['posts','posts.user']

        foreach ($relations as $key => $relationship) { //recorremos el array de relaciones

            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

       // return $relations;

        $query->with($relations); //se ejecuta el query con lo que tiene $relations en ultimas es el valor en la url de included

    }

    public function scopeFilter(Builder $query)
    {

        if (empty($this->allowFilter) || empty(request('filter'))) {
            return;
        }

        $filters = request('filter');

        $allowFilter = collect($this->allowFilter);

        foreach ($filters as $filter => $value) {

            if ($allowFilter->contains($filter)) {

                $query->where($filter, 'LIKE', '%' . $value . '%');//nos retorna todos los registros que conincidad, asi sea en una porcion del texto
            }
        }

    }

    public function scopeSort(Builder $query)
    {

    if (empty($this->allowSort) || empty(request('sort'))) {
            return;
        }

        $sortFields = explode(',', request('sort'));
        $allowSort = collect($this->allowSort);

    foreach ($sortFields as $sortField) {

            $direction = 'asc';

            if(substr($sortField, 0,1)=='-'){ //cambiamos la consulta a 'desc'si el usuario antecede el menos (-) en el valor de la variable sort
                $direction = 'desc';
                $sortField = substr($sortField,1);//copiamos el valor de sort pero omitiendo, el primer caracter por eso inicia desde el indice 1
            }
            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction);//ejecutamos la query con la direccion deseada sea 'asc' o 'desc'
            }
        }
        //http://api.blog.test/v1/categories?sort=name
    }

    public function scopeGetOrPaginate(Builder $query)
    {
        if (request('perPage')) {
            $perPage = intval(request('perPage'));//transformamos la cadena que llega en un numero.

            if($perPage){//como la funcion intval retorna 0 si no puede hacer la conversion 0  es = false
                return $query->paginate($perPage);//retornamos la cuonsulta de acuerdo a la ingresado en la vaiable $perPage
            }


        }
            return $query->get();//sino se pasa el valor de $perPage en la URL se pasan todos los registros.
        //http://api.codersfree1.test/v1/categories?perPage=2
    }
}
