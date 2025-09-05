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
        'ElectronicInvoice_id',
        'DianNumbering_id',
        'CreditDebitNote_id',
        'cufe',
        'cude',
        'xml_documento',
        'estado_dian',
        'fecha_validacion',
        'firma_digital',
        'hash_documento',
        'descripcion',
        'ambiente',
        'tipo_documento',
        'qr_codigo',
        'cdr',
        'modo_emision',
    ];

    protected $allowIncluded = [
        'dianNumbering',
        'dianNumbering.company',
        'electronicInvoice',
        'creditDebitNote',
        'radianEvents',
    ];
     //relaciones con otras tablas
    
    protected $allowFilter = [ 'ElectronicInvoice_id', 'DianNumbering_id', 'CreditDebitNote_id', 'cufe', 'cude', 'xml_documento', 'estado_dian', 'fecha_validacion', 'firma_digital', 'hash_documento', 'descripcion', 'ambiente', 'tipo_documento', 'qr_codigo', 'cdr', 'modo_emision',];
    protected $allowSort = [ 'ElectronicInvoice_id', 'DianNumbering_id', 'CreditDebitNote_id', 'cufe', 'cude', 'xml_documento', 'estado_dian', 'fecha_validacion', 'firma_digital', 'hash_documento', 'descripcion', 'ambiente', 'tipo_documento', 'qr_codigo', 'cdr', 'modo_emision',];

    public function electronicInvoice()
    {
        return $this->belongsTo(ElectronicInvoice::class);
    }

    public function dianNumbering()
    {
        return $this->belongsTo(DianNumbering::class, );
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
