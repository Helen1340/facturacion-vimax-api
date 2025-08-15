<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SystemUsers extends Model
{

    /* ==========================
    CAMPOS RELLENABLES
    ========================== */
    protected $fillable = [
        'nombre_completo',
        'rol',
        'contrasena',
        'correo_electronico',
        'telefono',
        'estado',
        'ultimo_Acceso',
        'numero_identificacion'
    ];

    /*
    LISTAS BLANCAS
    */
    protected $allowIncluded = [ 'CreditDebitNote', 'Role', 'ElectronicInvoice', 'Company'];
    protected $allowFilter = ['nombre_completo', 'rol', 'correo_electronico', 'telefono', 'estado', 'numero_identificacion'];
    protected $allowSort = ['nombre_completo', 'rol', 'correo_electronico', 'estado', 'numero_identificacion'];



    // (CARDINALIDADES)


    //Un usuario tiene muchas notas de crédito/debito
    public function CreditDebitNote()
    {
        return $this->hasMany(CreditDebitNote::class);
    }

    //Muchos usuarios pertenecen a un rol
    public function Role()
    {
        return $this->belongsTo(Role::class);
    }

    //Un usuario crea muchas facturas electrónicas
    public function ElectronicInvoice()
    {
        return $this->hasMany(ElectronicInvoice::class);
    }

    //Muchos usuarios trabajan en una empresa
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }



    /*
    SCOPES
    */
    // scopes
public function scopeIncluded(Builder $query)
{
    if (empty($this->allowIncluded) || empty(request('included'))) {
        // validamos que la lista blanca y la variable included enviada a través de HTTP no estén vacías
        return;
    }

    $relations  = explode(',', request('included'));
    // ['posts','relation2'] → recuperamos el valor de la variable included y separamos por coma

    $allowIncluded = collect($this->allowIncluded);
    // colocamos en una colección lo que tiene $allowIncluded (ej: ['posts','posts.user'])

    foreach ($relations as $key => $relationship) {
        // recorremos el array de relaciones
        if (!$allowIncluded->contains($relationship)) {
            unset($relations[$key]); // eliminamos las relaciones que no están permitidas
        }
    }

    $query->with($relations);
    // se ejecuta el query con lo que tiene $relations
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
            // retornamos todos los registros que coincidan, así sea parcialmente
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
            // cambiamos la consulta a 'desc' si el usuario antecede un "-" en el valor de sort
            $direction = 'desc';
            $sortField = substr($sortField, 1);
            // copiamos el valor de sort pero omitiendo el primer carácter
        }

        if ($allowSort->contains($sortField)) {
            // ejecutamos la query con la dirección deseada (asc o desc)
            $query->orderBy($sortField, $direction);
        }
    }
    // ejemplo: http://api.blog.test/v1/categories?sort=name
}

public function scopeGetOrPaginate(Builder $query)
{
    if (request('perPage')) {
        $perPage = intval(request('perPage'));
        // transformamos la cadena que llega en un número entero

        if ($perPage) {
            // como intval retorna 0 si no puede convertir, 0 = false
            return $query->paginate($perPage);
            // retornamos la consulta paginada
        }
    }

    return $query->get();
    // si no se pasa perPage en la URL, se devuelven todos los registros
    // ejemplo: http://api.codersfree1.test/v1/categories?perPage=2
}

}
