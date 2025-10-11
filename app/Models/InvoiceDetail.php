<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class InvoiceDetail extends Model
{

    use HasFactory;

    protected $table = 'invoice_details';

    protected $fillable = [
        'electronic_invoice_id',
        'item_id',
        'item_type',

        // --- Campos específicos del detalle UBL ---
        'description',           // Descripción del ítem
        'quantity',              // Cantidad
        'unit_price',            // Precio unitario
        'line_extension_amount', // Subtotal sin impuestos
        'discount_amount',       // Descuento aplicado
        'tax_amount',            // Valor de impuestos
        'total_line_amount',     // Total línea (subtotal + impuestos)
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\CompanyScope);
    }


    // listas blancas para la consulta
    protected $allowIncluded =[
        'electronicInvoice',
        'electronicInvoice.user',
        'item',
        'item.measurementUnit',
        'item.taxes',
    ];// Las relaciones que se pueden "incluir"

    //Campos permitidos para filtrar resultados
     protected $allowFilter = [
        'id',
        'description',
        'quantity',
        'unit_price',
        'tax_amount',
    ];

    //Campos permitidos para ordenar resultados

    protected $allowSort = [
        'id',
        'description',
        'quantity',
        'unit_price',
        'line_extension_amount',
        'tax_amount',
        'total_line_amount',
    ];

             //Relación polimórfica: devuelve el item que puede ser Product o Service.
    public function item()
    {
        return $this->morphTo(); // Laravel resuelve item_type + item_id
    }

    // Relación hacia la factura (ElectronicInvoice).

    public function electronicInvoice()
    {
        return $this->belongsTo(ElectronicInvoice::class);
    }

    
    // Scopes para filtros, relaciones y ordenamiento dinámicos
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
