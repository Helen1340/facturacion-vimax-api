<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PaymentMethod;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'nombre'      => $this->faker->unique()->word, // Nombre del método de pago
            'codigo_dian' => strtoupper($this->faker->unique()->bothify('??###')), // Código tipo DIAN
            'descripcion' => $this->faker->sentence(6), // Descripción corta
        ];
    }
}
