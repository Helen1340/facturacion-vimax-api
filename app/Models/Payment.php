<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
protected $fillable = [
        'electronic_invoice_id', // Factura electrónica
        'payment_method_id',     // Método de pago
        'payment_date',          // Fecha de pago
        'amount_paid',           // Valor pagado
        'currency',              // Moneda
        'payment_reference',     // Referencia de pago
];


    // Las posibles relaciones (includes) que se pueden cargar a través de query parameters en la API
    protected $allowIncluded = [
        'electronicInvoice',
        'electronicInvoice.user',
        'paymentMethod',
    ];
    // Campos por los que se puede filtrar la consulta
    protected $allowFilter = ['payment_date', 'amount_paid', 'currency'];
    // Campos por los que se puede ordenar la consulta
    protected $allowSort = ['payment_date', 'amount_paid'];

    // CARDINALIDAD //

    // Muchos pagos pertenecen a una factura electrónica (muchos a uno)
    public function electronicInvoice()
    {
        return $this->belongsTo(ElectronicInvoice::class, 'electronic_invoice_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
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
