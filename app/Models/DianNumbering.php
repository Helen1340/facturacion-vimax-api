<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DianNumbering extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',           // Id de la compañía
        'document_type',        // Tipo de documento: Factura, NotaCredito, NotaDebito
        'document_type_code',   // Código oficial del tipo de documento DIAN
        'prefix',               // Prefijo de numeración
        'start_number',         // Número inicial autorizado
        'end_number',           // Número final autorizado
        'resolution_date',      // Fecha de resolución DIAN
        'resolution_number',    // Número de resolución DIAN
        'validity_start_date',  // Fecha inicio de vigencia
        'validity_end_date',    // Fecha fin de vigencia
        'current_status',       // Estado actual de la numeración: Activo/Inactivo
        'environment',          // Ambiente de uso: Pruebas/Producción
        'description',          // Descripción opcional para referencia interna
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\CompanyScope);
    }

    // Listas blancas
    protected $allowIncluded = [
        'company',
        'company.users',
        'electronicDocuments',
        'electronicDocuments.electronicInvoice',
    ];
    // Permite incluir la relaciónes

    protected $allowFilter = [
        'document_type',
        'document_type_code',
        'prefix',
        'resolution_number',
        'current_status',
        'environment',
    ];
    protected $allowSort = [
        'document_type',
        'prefix',
        'start_number',
        'end_number',
        'resolution_date',
        'validity_start_date',
        'validity_end_date',
        'current_status',
        'environment',
    ];

    // Cardinalidad: Un numero de numeración DIAN (1) puede tener muchos Documentos Electrónicos (M)
    public function electronicDocuments()
    {
        return $this->hasMany(ElectronicDocument::class);
    }

    // Cardinalidad: Un numero de numeración DIAN (M) pertenece a una compañía (1)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // SCOPES //

    // Incluye relaciones según el parámetro included
    public function scopeIncluded(Builder $query)
    {
        if (empty($this->allowIncluded) || empty(request('included'))) {
            // validamos que la lista blanca y la variable included enviada a travez de HTTP no este en vacia
            return;
        }

        $relations = explode(',', request('included')); // ['electronicInvoice','paymentMethod']

        $allowIncluded = collect($this->allowIncluded);

        foreach ($relations as $key => $relationship) {
            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

        $query->with($relations); // se ejecuta el query con lo que tiene $relations
    }

    // Filtra resultados según el parámetro filter
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
                // nos retorna todos los registros que coincidan, así sea en una porción del texto
            }
        }
    }

    // Ordena resultados según el parámetro sort
    public function scopeSort(Builder $query)
    {
        if (empty($this->allowSort) || empty(request('sort'))) {
            return;
        }

        $sortFields = explode(',', request('sort'));
        $allowSort = collect($this->allowSort);

        foreach ($sortFields as $sortField) {
            $direction = 'asc'; // orden por defecto

            if (substr($sortField, 0, 1) == '-') {
                // cambiamos la consulta a 'desc' si el usuario antecede el menos (-)
                $direction = 'desc';
                $sortField = substr($sortField, 1);
            }

            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction);
                // ejecutamos la query con la direccion deseada sea 'asc' o 'desc'
            }
        }
        // ejemplo: http://api.blog.test/v1/payments?sort=-valor_pagado,fecha_pago
    }

    // Devuelve resultados paginados o todos
    public function scopeGetOrPaginate(Builder $query)
    {
        if (request('perPage')) {
            $perPage = intval(request('perPage'));
            if ($perPage) {
                // como la funcion intval retorna 0 si no puede hacer la conversion
                return $query->paginate($perPage);
                // retornamos la consulta de acuerdo al perPage
            }
        }

        return $query->get();
        // si no se pasa perPage, retorna todos los registros
        // ejemplo: http://api.codersfree1.test/v1/payments?perPage=2
    }
}
