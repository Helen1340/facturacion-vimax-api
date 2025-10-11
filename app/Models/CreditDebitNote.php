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
        // 'description',         // 🔴 Removido porque no existe en la base de datos
        'note_number',           // Número de la nota
        'status',                // Estado de la nota: 'accepted', 'rejected', 'pending'
        'issue_date',            // Fecha de emisión
        'total_amount',          // Valor total de la nota
    ];

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
            // validamos que la lista blanca y la variable included enviada a través de HTTP no esté vacía.
            return;
        }

        $relations  = explode(',', request('included')); // ['posts','relation2']
        $allowIncluded = collect($this->allowIncluded); // colocamos en una colección lo que tiene $allowIncluded

        foreach ($relations as $key => $relationship) {
            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

        $query->with($relations); // se ejecuta el query con lo que tiene $relations
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
                // nos retorna todos los registros que coincidan, así sea en una porción del texto
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
            if(substr($sortField, 0,1)=='-'){
                // cambiamos la consulta a 'desc' si el usuario antecede el menos (-) en el valor de la variable sort
                $direction = 'desc';
                $sortField = substr($sortField,1); // copiamos el valor de sort pero omitiendo el primer caracter
            }
            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction); // ejecutamos la query con la dirección deseada
            }
        }
    }

    public function scopeGetOrPaginate(Builder $query)
    {
        if (request('perPage')) {
            $perPage = intval(request('perPage')); // transformamos la cadena que llega en un número
            if($perPage){
                return $query->paginate($perPage); // retornamos la consulta de acuerdo al valor ingresado
            }
        }
        return $query->get(); // si no se pasa el valor de $perPage se devuelven todos los registros
    }
}

