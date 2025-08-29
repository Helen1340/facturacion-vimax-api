<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{

    use HasFactory;

    protected $table = 'invoice_details';

    protected $fillable = [
        'electronic_invoice_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'valor_total',
        'subtotal',
        'descuento',
        'impuestos_aplicados',
        'valor_impuesto',
        'item_id',
        'item_type',
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

}
