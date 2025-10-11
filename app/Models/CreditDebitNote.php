<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditDebitNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'electronic_invoice_id', // FK a la factura electrónica asociada
        'reason',                // Motivo de la nota crédito/débito
        'note_type',             // Tipo de nota: 'debit' o 'credit'
        'description',           // Descripción adicional
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
    protected $allowFilter = ['reason', 'note_type', 'description', 'note_number', 'status'];
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
        if (empty($this->allowIncluded) || empty(request('included'))) { // validamos que la lista blanca y la variable included enviada a travez de HTTP no este en vacia.
            return;
        }


        // return request('included');

        $relations  = explode(',', request('included')); //['posts','relation2']//recuperamos el valor de la variable included y separa sus valores por una coma

        //return $relations;


        $allowIncluded = collect($this->allowIncluded); //colocamos en una colecion lo que tiene $allowIncluded en este caso = ['posts','posts.user']

        foreach ($relations as $key => $relationship) { //recorremos el array de relaciones

            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

        // return $relations;

        $query->with($relations); //se ejecuta el query con lo que tiene $relations en ultimas es el valor en la url de included

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

                $query->where($filter, 'LIKE', '%' . $value . '%'); //nos retorna todos los registros que conincidad, asi sea en una porcion del texto
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

            if (substr($sortField, 0, 1) == '-') { //cambiamos la consulta a 'desc'si el usuario antecede el menos (-) en el valor de la variable sort
                $direction = 'desc';
                $sortField = substr($sortField, 1); //copiamos el valor de sort pero omitiendo, el primer caracter por eso inicia desde el indice 1
            }
            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction); //ejecutamos la query con la direccion deseada sea 'asc' o 'desc'
            }
        }
        //http://api.blog.test/v1/categories?sort=name
    }

    public function scopeGetOrPaginate(Builder $query)
    {
        if (request('perPage')) {
            $perPage = intval(request('perPage')); //transformamos la cadena que llega en un numero.

            if ($perPage) { //como la funcion intval retorna 0 si no puede hacer la conversion 0  es = false
                return $query->paginate($perPage); //retornamos la cuonsulta de acuerdo a la ingresado en la vaiable $perPage
            }
        }
        return $query->get(); //sino se pasa el valor de $perPage en la URL se pasan todos los registros.
        //http://api.codersfree1.test/v1/categories?perPage=2
    }
}
