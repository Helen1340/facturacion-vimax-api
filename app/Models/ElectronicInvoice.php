<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ElectronicInvoice extends Model
{
    use HasFactory;

    

    protected $fillable = [
        'user_id',
        'buyer_id',  // Cliente (comprador) - usuario con role 'client'
        'invoice_number',
        'issue_date',
        'internal_status',
        'observation',

        // --- Campos DIAN / UBL ---
        'ubl_version',
        'customization_id',
        'profile_id',
        'uuid',
        'document_currency_code',
        'invoice_type_code',

        // --- Totales principales ---
        'line_extension_amount',
        'tax_exclusive_amount',
        'tax_inclusive_amount',
        'payable_amount',
        'total_discount',

        // --- Control de estado DIAN ---
        'dian_status',
        'sent_at',
        'received_at',

        // --- Información de pago ---
        'payment_means_code',
        'payment_terms',
        'payment_means_name',
    ];


    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\CompanyScope);
    }

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'issue_date' => 'datetime',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    //LISTAS BLANCAS

    //Las posibles relaciones (includes) que se pueden cargar.
    protected $allowIncluded = [
        'user',
        'user.company',
        'user.role',
        'buyer',
        'buyer.company',
        'buyer.role',
        'invoiceDetails',
        'invoiceDetails.item',
        'payment',
        'payment.paymentMethod',
        'creditDebitNotes',
        'creditDebitNotes.electronicDocuments',
        'electronicDocuments',
        'electronicDocuments.dianNumbering',
    ];
    //Los campos por los que se puede filtrar la consulta.
    protected $allowFilter = [
    // Identificación
    'user_id',
    'buyer_id',
    'invoice_number',
    'issue_date',

    // Estados
    'internal_status',
    'dian_status',

    // DIAN / UBL básicos
    'uuid',
    'document_currency_code',
    'invoice_type_code',

    // Auditoría
    'sent_at',
    'received_at',

    // Totales (opcional si usarás reportes)
    // 'payable_amount',
    // 'tax_inclusive_amount',

    // Información de pago (solo si aplica)
    // 'payment_means_code',
];

    //Los campos por los que se puede ordenar la consulta.
    protected $allowSort = ['id',
    'invoice_number',
    'issue_date',
    'internal_status',
    'dian_status',
    'payable_amount',
    'sent_at',
    'received_at',
    // opcionales
    // 'document_currency_code',
    // 'invoice_type_code',
    ];


    //RELACIONES CON OTRAS TABLAS

    //Relación muchos a uno: Una factura pertenece a un usuario (facturador que crea la factura).

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relación muchos a uno: Una factura tiene un comprador (cliente - usuario con role 'client').

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    //Relación uno a uno: Una factura puede tener un pago.

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    //Relación uno a muchos: Una factura se compone de muchos detalles de factura.

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    //Relación uno a muchos: Una factura puede tener muchas notas de crédito o débito.

    public function creditDebitNotes()
    {
        return $this->hasMany(CreditDebitNote::class);
    }

    // Relación uno a muchos: Una factura puede tener muchos documentos electrónicos.

    public function electronicDocuments()
    {
        return $this->hasMany(ElectronicDocument::class);
    }

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


    // (cálculos)


    /**
     * Subtotal = SUM(cantidad * precio_unitario)
     * Usamos suma en DB si la relación no está cargada (mejor performance en listados).
     */
        public function getSubTotalAttribute()
{
     // si la relación ya está cargada (eager loaded), usar la colección
    if ($this->relationLoaded('invoiceDetails')) {
        $sum = $this->invoiceDetails->sum(function ($detail) {
            return (float) $detail->quantity * (float) $detail->unit_price;
        });

        return round($sum, 2);
    }

        // si no está cargada, calcular en DB (evita traer todos los detalles)
        $value = $this->invoiceDetails()
        ->selectRaw('COALESCE(SUM(quantity * unit_price),0) as total')
        ->value('total');

    return round((float) $value, 2);
}

    /**
     * Total impuesto = SUM(valor_impuesto) (tomamos el campo 'valor_impuesto' del detalle)
     */
    public function getTotalTaxAttribute()
{
    if ($this->relationLoaded('invoiceDetails')) {
        $sum = $this->invoiceDetails->sum(function ($detail) {
            return (float) ($detail->tax_amount ?? 0);
        });
        return round($sum, 2);
    }

    $value = $this->invoiceDetails()->sum('tax_amount');
    return round((float) $value, 2);
}

    /**
     * Total factura = subtotal - descuento_total + total_impuesto
     * (descuento_total puede estar almacenado o ser 0)
     */
    public function getTotalInvoiceAttribute()
{
    $subtotal = (float) $this->sub_total;
    $taxes = (float) $this->total_tax;
    $discount = (float) ($this->total_discount ?? 0);

    return round($subtotal - $discount + $taxes, 2);
}
}


