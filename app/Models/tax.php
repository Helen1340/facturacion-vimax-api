<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Tax extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'porcentaje_base',
        'estado',
    ];



    // LISTAS BLANCAS
    protected $allowIncluded = ['products', 'services'];
    protected $allowFilter = ['nombre', 'tipo', 'estado'];
    protected $allowSort = ['nombre', 'tipo', 'porcentaje_base', 'estado'];

    // CARDINALIDADES

    // Muchos a muchos con productos
    // public function productsTax()
    // {
    //     return $this->belongsToMany(ProductTax::class)
    //
    // }

    // // Muchos a muchos con servicios
    // public function servicesTax()
    // {
    //     return $this->belongsToMany(ServiceTax::class)
    //
    // }

     // scopes
    public function scopeIncluded(Builder $query)
    {
        if (empty($this->allowIncluded) || empty(request('included'))) { // validamos que la lista blanca y la variable included enviada a través de HTTP no esté vacía.
            return;
        }

        // return request('included');

        $relations  = explode(',', request('included')); // ['products','services'] // recuperamos el valor de la variable included y separamos sus valores por una coma

        // return $relations;

        $allowIncluded = collect($this->allowIncluded); // colocamos en una colección lo que tiene $allowIncluded en este caso = ['products','services']

        foreach ($relations as $key => $relationship) { // recorremos el array de relaciones
            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

        // return $relations;

        $query->with($relations); // se ejecuta el query con lo que tiene $relations, en últimas es el valor en la url de included
    }

    public function scopeFilter(Builder $query)
    {
        if (empty($this->allowFilter) || empty(request('filter'))) { // validamos que la lista blanca y la variable filter enviada a través de HTTP no estén vacías.
            return;
        }

        $filters = request('filter');

        $allowFilter = collect($this->allowFilter);

        foreach ($filters as $filter => $value) { // recorremos los filtros que llegan por la petición
            if ($allowFilter->contains($filter)) {
                $query->where($filter, 'LIKE', '%' . $value . '%'); // nos retorna todos los registros que coincidan, así sea en una porción del texto
            }
        }
    }

    public function scopeSort(Builder $query)
    {
        if (empty($this->allowSort) || empty(request('sort'))) { // validamos que la lista blanca y la variable sort enviada a través de HTTP no estén vacías.
            return;
        }

        $sortFields = explode(',', request('sort')); // recuperamos los campos enviados en sort y los separamos por coma
        $allowSort = collect($this->allowSort);

        foreach ($sortFields as $sortField) {

            $direction = 'asc'; // orden por defecto

            if (substr($sortField, 0, 1) == '-') { // cambiamos la consulta a 'desc' si el usuario antecede con el signo menos (-)
                $direction = 'desc';
                $sortField = substr($sortField, 1); // copiamos el valor de sort pero omitiendo el primer caracter
            }

            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction); // ejecutamos la query con la dirección deseada sea 'asc' o 'desc'
            }
        }
        // http://api.blog.test/v1/taxes?sort=-porcentaje_base,nombre
    }

    public function scopeGetOrPaginate(Builder $query)
    {
        if (request('perPage')) {
            $perPage = intval(request('perPage')); // transformamos la cadena que llega en un número.

            if ($perPage) { // como la función intval retorna 0 si no puede hacer la conversión (0 es = false)
                return $query->paginate($perPage); // retornamos la consulta de acuerdo a lo ingresado en la variable $perPage
            }
        }

        return $query->get(); // si no se pasa el valor de $perPage en la URL se retornan todos los registros.
        // http://api.codersfree1.test/v1/taxes?perPage=2
    }
}

