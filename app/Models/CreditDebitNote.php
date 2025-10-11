<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditDebitNote extends Model {
    use HasFactory;

    protected $fillable = [
        'electronic_invoice_id', // FK a la factura electrónica asociada
        'reason',                // Motivo de la nota crédito/débito
        'note_type',             // Tipo de nota: 'debit' o 'credit'
        // 'description',         // Removido porque no existe en la base de datos
        'note_number',           // Número de la nota
        'status',                // Estado de la nota: 'accepted', 'rejected', 'pending'
        'issue_date',            // Fecha de emisión
        'total_amount',          // Valor total de la nota
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\CompanyScope);
    }

    // Listas blancas
    protected $allowIncluded = [
        'electronicInvoice',
        'electronicInvoice.user',
        'electronicDocuments',
    ];

    protected $allowFilter = ['reason', 'note_type', 'note_number', 'status'];
    protected $allowSort = ['reason', 'note_type', 'note_number', 'issue_date', 'total_amount', 'status'];

    // Cardinalidad: Una Nota Crédito/Débito (1) puede tener muchos Documentos Electrónicos (M)
    // Esto implica que la FK está en la tabla ElectronicDocument
    public function electronicDocuments()
    {
        return $this->hasMany(ElectronicDocument::class);
    }

    // Cardinalidad: Muchas Notas Crédito/Débito (M) pertenecen a una Factura Electrónica (1)
    // Esto implica que la FK (electronic_invoice_id) está en la tabla CreditDebitNote
    public function electronicInvoice()
    {
        return $this->belongsTo(ElectronicInvoice::class);
    }

    // scopes
  
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

   