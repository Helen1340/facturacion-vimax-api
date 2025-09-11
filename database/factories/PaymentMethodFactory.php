<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PaymentMethod;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        // Excluir los códigos oficiales para no duplicar
        $excluidos = ['10','20','30','31','41','42','ZZZ'];

        // Generar un código que no esté entre los oficiales
        do {
            $codigo = strtoupper($this->faker->unique()->bothify('PMT###'));
        } while (in_array($codigo, $excluidos));

        return [
            'nombre'      => ucfirst($this->faker->words(2, true)),
            'codigo_dian' => $codigo,
            'descripcion' => $this->faker->sentence(6),
        ];
    }
}
