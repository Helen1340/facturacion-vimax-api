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

    public function scopeFilter(Builder $query)
    {

        if (empty($this->allowFilter) || empty(request('filter'))) {
            return;
        }

        $filters = request('filter');

        $allowFilter = collect($this->allowFilter);

        foreach ($filters as $filter => $value) {

            if ($allowFilter->contains($filter)) {

                $query->where($filter, 'LIKE', '%' . $value . '%');//nos retorna todos los registros que conincidad, asi sea en una porcion del texto
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

            if(substr($sortField, 0,1)=='-'){ //cambiamos la consulta a 'desc'si el usuario antecede el menos (-) en el valor de la variable sort
                $direction = 'desc';
                $sortField = substr($sortField,1);//copiamos el valor de sort pero omitiendo, el primer caracter por eso inicia desde el indice 1
            }
            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction);//ejecutamos la query con la direccion deseada sea 'asc' o 'desc'
            }
        }
        //http://api.blog.test/v1/categories?sort=name
    }

    public function scopeGetOrPaginate(Builder $query)
    {
      if (request('perPage')) {
            $perPage = intval(request('perPage'));//transformamos la cadena que llega en un numero.

            if($perPage){//como la funcion intval retorna 0 si no puede hacer la conversion 0  es = false
                return $query->paginate($perPage);//retornamos la cuonsulta de acuerdo a la ingresado en la vaiable $perPage
            }


         }
           return $query->get();//sino se pasa el valor de $perPage en la URL se pasan todos los registros.
        //http://api.codersfree1.test/v1/categories?perPage=2
    }




}
