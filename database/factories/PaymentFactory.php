<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'fecha_pago'      => $this->faker->date(),
            'valor_pagado'    => $this->faker->randomFloat(2, 1000, 100000),
            'moneda'          => $this->faker->randomElement(['COP', 'USD', 'EUR']),
            'referencia_pago' => $this->faker->unique()->bothify('REF-#####'), // aquí se genera un valor único
        ];
    }
}


