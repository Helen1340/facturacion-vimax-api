<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Tax extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\CompanyScope);
    }

    protected $fillable = [
        'company_id',      // empresa
        'tax_code',        // Código único del tributo según DIAN
        'name',            // Nombre del tributo
        'description',     // Descripción del tributo
        'type',            // Tipo de tributo: impuesto, retención, contribución, etc.
        'percentage',      // Porcentaje aplicado sobre la base
        'fixed_value',     // Valor fijo si aplica en lugar de porcentaje
        'application_type',// Tipo de aplicación: Porcentaje, ValorFijo, Retencion
        'min_value',       // Valor mínimo aplicable (opcional)
        'max_value',       // Valor máximo aplicable (opcional)
        'status',          // Estado del tributo: Activo o Inactivo
    ];

    // Las posibles relaciones (includes) que se pueden cargar
    // a través de query parameters en la API
    protected $allowIncluded = [
        'products',
        'products.measurementUnit',
        'services',
        'services.measurementUnit',
    ];

    // Los campos por los que se puede filtrar la consulta
    protected $allowFilter = ['tax_code',
        'name',
        'type',
        'status',];

    // Los campos por los que se puede ordenar la consulta
    protected $allowSort = ['tax_code',
        'name',
        'type',
        'percentage',
        'fixed_value',
        'status',];

    // CARDINALIDAD //

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_tax',
            'tax_id',      // FK en la pivote hacia Tax (snake_case)
            'product_id'   // FK en la pivote hacia Product (snake_case)
        )->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'service_tax',
            'tax_id',      // FK en la pivote hacia Tax (snake_case)
            'service_id'   // FK en la pivote hacia Service (snake_case)
        )->withTimestamps();
    }

    // SCOPES //

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
