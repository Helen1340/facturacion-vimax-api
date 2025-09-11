<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;
use App\Models\ElectronicInvoice; 
use App\Models\PaymentMethod;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            // Relacionar con factura ya existente
            'electronic_invoice_id' => ElectronicInvoice::inRandomOrder()->first()->id 
                                        ?? ElectronicInvoice::factory(),

            // Relacionar con método de pago oficial ya existente
            'payment_method_id'     => PaymentMethod::inRandomOrder()->first()->id,

            'fecha_pago'      => $this->faker->dateTimeBetween('-2 months', 'now'),
            'valor_pagado'    => $this->faker->randomFloat(2, 100000, 5000000),
            'moneda'          => $this->faker->randomElement(['COP', 'USD', 'EUR']),
            'referencia_pago' => strtoupper($this->faker->bothify('PSE-########')),
        ];
    }
}

