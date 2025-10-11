<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class ElectronicDocument extends Model
{
    use HasFactory;

    protected   $model = 'electronic_documents';

    protected $fillable = [
        'electronic_invoice_id', // FK a la factura electrónica
        'dian_numbering_id',     // FK a numeración DIAN
        'credit_debit_note_id',  // FK a nota crédito/débito
        'cufe',                  // Código Único de Factura Electrónica
        'cude',                  // Código Único de Documento Electrónico
        'xml_document',          // XML del documento electrónico
        'dian_status',           // Estado ante la DIAN
        'validation_date',       // Fecha de validación del documento
        'digital_signature',     // Firma digital del documento
        'document_hash',         // Hash del documento electrónico
        'description',           // Descripción del documento
        'environment',           // Ambiente de emisión: Pruebas o Producción
        'document_type',         // Tipo de documento (Factura, Nota Crédito, etc.)
        'qr_code',               // Código QR del documento
        'cdr',                   // Código de Respuesta de la DIAN
        'emission_mode',         // Modo de emisión: normal o en contingencia
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\CompanyScope);
    }

    protected $allowIncluded = [
        'dianNumbering',
        'dianNumbering.company',
        'electronicInvoice',
        'creditDebitNote',
        'radianEvents',
    ];
    //relaciones con otras tablas

    protected $allowFilter = [
        'electronic_invoice_id',
        'dian_numbering_id',
        'credit_debit_note_id',
        'cufe',
        'cude',
        'xml_document',
        'dian_status',
        'validation_date',
        'digital_signature',
        'document_hash',
        'description',
        'environment',
        'document_type',
        'qr_code',
        'cdr',
        'emission_mode',
    ];

    protected $allowSort = [
        'electronic_invoice_id',
        'dian_numbering_id',
        'credit_debit_note_id',
        'cufe',
        'cude',
        'xml_document',
        'dian_status',
        'validation_date',
        'digital_signature',
        'document_hash',
        'description',
        'environment',
        'document_type',
        'qr_code',
        'cdr',
        'emission_mode',
    ];


    public function electronicInvoice()
    {
        return $this->belongsTo(ElectronicInvoice::class);
    }

    public function dianNumbering()
    {

        return $this->belongsTo(DianNumbering::class,);

        
    }

    public function creditDebitNote()
    {
        return $this->belongsTo(CreditDebitNote::class);
    }

    public function radianEvents()
    {
        return $this->hasMany(RadianEvent::class, 'electronic_document_id');
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
