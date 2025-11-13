<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Evita interferir en consola, seeders, o sin usuario autenticado
        if (app()->runningInConsole()) {
            return;
        }

        $user = Auth::user();

        if (!$user || !$user->company_id) {
            return;
        }

        // 🔹 1️⃣ Si el modelo tiene relación directa con empresa
        if (in_array('company_id', $model->getFillable())) {
            // Filtrar por empresa y excluir registros con company_id NULL
            $builder->where('company_id', $user->company_id)
                    ->whereNotNull('company_id');
            return;
        }

        // 🔹 2️⃣ Si el modelo tiene relación directa con usuario
        if (method_exists($model, 'user')) {
            $builder->whereHas('user', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            });
            return;
        }

        // 🔹 3️⃣ Si pertenece a una factura electrónica (caso Payment, CreditNote, etc.)
        if (method_exists($model, 'electronicInvoice')) {
            $builder->whereHas('electronicInvoice.user', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            });
            return;
        }

        // 🔹 4️⃣ Si pertenece a un documento o numeración de la DIAN
        if (method_exists($model, 'company')) {
            $builder->whereHas('company', function ($query) use ($user) {
                $query->where('id', $user->company_id);
            });
            return;
        }

        // 🔹 5️⃣ Si pertenece a un detalle de factura (InvoiceDetail → Invoice → User)
        if (method_exists($model, 'electronicInvoice')) {
            $builder->whereHas('electronicInvoice.user', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            });
        }
    }
}
