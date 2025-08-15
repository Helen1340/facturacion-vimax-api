<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CreditDebitNote extends Model
{
    /* ==========================
       CAMPOS RELLENABLES
    ========================== */
    protected $fillable = [
        'IdUsuario',
        'Motivo',
        'TipoNota',
        'Descripcion',
        'ValorTotal',
        'CUFENota',
        'XMLFirmado',
        'EstadoDian',
        'FechaEmision',
        'Moneda'
    ];

    // /* ==========================
    //    RELACIONES (CARDINALIDADES)
    // ========================== */

    // // muchas nota de crédito/débito pertenece a un usuario
    // public function user()
    // {
    //     return $this->belongsTo(SystemUsers::class, 'IdUsuario');
    //     Nota: la FK en la tabla credit_debit_notes es IdUsuario
    // }

    /* ==========================
       LISTAS BLANCAS
    ========================== */
    protected $allowIncluded = [
        'user' // Solo se permite incluir la relación con usuario
    ];

    protected $allowFilter = [
        'IdUsuario',
        'Motivo',
        'TipoNota',
        'Descripcion',
        'ValorTotal',
        'CUFENota',
        'EstadoDian',
        'FechaEmision',
        'Moneda'
    ];

    protected $allowSort = [
        'Motivo',
        'TipoNota',
        'ValorTotal',
        'EstadoDian',
        'FechaEmision'
    ];

    /* ==========================
       SCOPES
    ========================== */

    // Permite incluir relaciones definidas en allowIncluded mediante query param 'included'
    public function scopeIncluded(Builder $query)
    {
        if (empty($this->allowIncluded) || empty(request('included'))) {
            return;
        }

        $relations  = explode(',', request('included'));
        $allowIncluded = collect($this->allowIncluded);

        foreach ($relations as $key => $relationship) {
            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]); // eliminamos relaciones no permitidas
            }
        }

        $query->with($relations);
        // Ejecuta la query con las relaciones permitidas
    }

    // Permite filtrar los registros según allowFilter y query param 'filter'
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

    // Permite ordenar los registros según allowSort y query param 'sort'
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

    // Retorna todos los registros o los paginados según query param 'perPage'
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

