<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Permission extends Model
{
    protected $fillable = [
        'id_Permiso',
        'Nombre',
        'Descripcion',
    ];

    // relaciones
    // ninguna relación definida por el momento

    // listas blancas de relaciones que se pueden incluir vía ?included
    protected $allowIncluded = [];

    // columnas que se pueden filtrar vía ?filter[field]=value
    protected $allowFilter = [
        'Nombre',
        'Descripcion',
    ];

    // columnas que se pueden ordenar vía ?sort=columna o ?sort=-columna
    protected $allowSort = [
        'Nombre',
        'Descripcion',
    ];

    // scopes

    // included: permite incluir relaciones definidas en allowIncluded
    public function scopeIncluded(Builder $query)
    {
        if (empty($this->allowIncluded) || empty(request('included'))) {
            return;
        }

        $relations = explode(',', request('included'));
        $allowIncluded = collect($this->allowIncluded);

        foreach ($relations as $key => $relationship) {
            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

        $query->with($relations);
    }

    // filter: filtra columnas según los parámetros enviados en la URL
    public function scopeFilter(Builder $query)
    {
        if (empty($this->allowFilter) || empty(request('filter'))) {
            return;
        }

        $filters = request('filter');
        $allowFilter = collect($this->allowFilter);

        foreach ($filters as $filter => $value) {
            if ($allowFilter->contains($filter)) {
                $query->where($filter, 'LIKE', '%' . $value . '%');
            }
        }
    }

    // sort: ordena columnas según los parámetros enviados en la URL
    public function scopeSort(Builder $query)
    {
        if (empty($this->allowSort) || empty(request('sort'))) {
            return;
        }

        $sortFields = explode(',', request('sort'));
        $allowSort = collect($this->allowSort);

        foreach ($sortFields as $sortField) {
            $direction = 'asc';
            if (substr($sortField, 0, 1) == '-') {
                $direction = 'desc';
                $sortField = substr($sortField, 1);
            }

            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction);
            }
        }
    }

    // getOrPaginate: devuelve todos los registros o paginados según ?perPage
    public function scopeGetOrPaginate(Builder $query)
    {
        if (request('perPage')) {
            $perPage = intval(request('perPage'));
            if ($perPage) {
                return $query->paginate($perPage);
            }
        }

        return $query->get();
    }
}

