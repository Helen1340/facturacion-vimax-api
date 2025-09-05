<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'role_id',
        'nombre',
        'tipo_documento',
        'numero_documento',
        'direccion',
        'pais',
        'descripcion',
        'contrasena',
        'correo_electronico',
        'telefono',
        'estado',
        'ultimo_acceso',
    ];

    // 👇 todo en minúsculas
    protected $allowIncluded = [
        'company',
        'company.digitalCertificates',
        'company.dianNumberings',
        'electronicInvoices',
        'electronicInvoices.invoiceDetails',
        'electronicInvoices.payment',
        'electronicInvoices.creditDebitNotes',
        'electronicInvoices.electronicDocuments',
        'role',

    ];

    protected $allowFilter = [
        'company_id',
        'nombre',
        'tipo_documento',
        'numero_documento',
        'direccion',
        'pais',
        'descripcion',
        'correo_electronico',
        'telefono',
        'estado',
        'ultimo_acceso'
    ];

    protected $allowSort = [
        'company_id',
        'nombre',
        'tipo_documento',
        'numero_documento',
        'direccion',
        'pais',
        'descripcion',
        'correo_electronico',
        'telefono',
        'estado',
        'ultimo_acceso'
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function electronicInvoices()
    {
        return $this->hasMany(ElectronicInvoice::class);
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */

    //esto oculta la contraseña "contrasena" cuando devuelve el json
    /*
    protected $hidden = [
        'contrasena',
        'remember_token',
    ];
    */

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */


    // esto encripta la contraseña "contrasena"
    /*
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'contraseña' => 'hashed',
        ];
    }
    */
}
