<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MeasurementUnit extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\CompanyScope);
    }

    protected $fillable = [
        'company_id',        // empresa
        'name',              // Nombre
        'status',            // Estado
        'dian_code',         // Código DIAN
        'description',       // Descripción
        'application_type',  // Tipo de aplicación
    ];

    // Las posibles relaciones que se pueden cargar vía query parameters en la API
    protected $allowIncluded = [
        'products',
        'products.invoiceDetails',
        'services',
        'services.invoiceDetails',
    ];
    // Campos por los que se puede filtrar la consulta
    protected $allowFilter = ['id', 'name', 'status', 'dian_code', 'application_type'];
    // Campos por los que se puede ordenar la consulta
    protected $allowSort = ['id', 'name', 'status', 'dian_code'];


    // Cardinalidad //

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Una unidad de medida puede tener muchos productos
    public function products()
    {
        return $this->hasMany(Product::class, 'measurement_unit_id');
    }
    // Una unidad de medida puede tener muchos servicios
    public function services()
    {
        return $this->hasMany(Service::class, 'measurement_unit_id');
    }


    // SCOPES PARA FILTROS, INCLUSIONES Y ORDENAMIENTO //

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

