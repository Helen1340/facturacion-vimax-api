<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{

    use HasFactory;

    protected $fillable = [
        'measurement_unit_id',
        'name',                // nombre del servicio
        'description',         // descripción
        'service_code',        // código del servicio
        'unit_price',          // precio unitario
        'status',              // estado (Activo/Inactivo)
    ];

    protected $allowIncluded = [ 'measurementUnit', 'invoiceDetails', 'taxes'];
    protected $allowFilter = ['id', 'name', 'description', 'service_code', 'unit_price', 'status'];
    protected $allowSort = ['id', 'name', 'description', 'service_code', 'unit_price', 'status'];

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function invoiceDetails()
    {
        return $this->morphMany(InvoiceDetail::class, 'item');
    }

    public function taxes()
    {
        return $this->belongsToMany(
            Tax::class,        // Modelo relacionado
            'service_tax',     // Tabla pivote
            'service_id',      // FK en la pivote hacia Service
            'tax_id'           // FK en la pivote hacia Tax
        )->withTimestamps();
    }

    public function scopeIncluded(Builder $query)
    {
        if (empty($this->allowIncluded) || empty(request('included'))) {
            return;
        }

        $relations  = explode(',', request('included'));

        $allowIncluded = collect($this->allowIncluded);

        foreach ($relations as $key => $relationship) {

            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

        $query->with($relations);
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

                $query->where($filter, 'LIKE', '%' . $value . '%');
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

            if (substr($sortField, 0, 1) == '-') {
                $direction = 'desc';
                $sortField = substr($sortField, 1);
            }
            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction);
            }
        }
    }

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
