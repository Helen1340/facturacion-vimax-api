<?php

namespace Database\Factories;

use App\Models\ElectronicInvoice;
use App\Models\User; 

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ElectronicInvoice>
 */
class ElectronicInvoiceFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

    return [
        'user_id' => User::factory(),
        'numero_factura' => $this->faker->unique()->numerify('FAC-#####'),
        'fecha_emision' => $this->faker->dateTimeBetween('-5 years', 'now'),
        'sub_total' => $this->faker->randomFloat(2, 10000, 500000),
        'total_impuesto' => $this->faker->randomFloat(2, 1000, 100000),
        'total_factura' => $this->faker->randomFloat(2, 20000, 600000),
        'estado_interno' => $this->faker->randomElement(['borrador', 'Emitida']),
        'descuento_total' => $this->faker->optional()->randomFloat(2, 500, 5000),
        'observacion' => $this->faker->optional()->sentence(),
    ];
}

}
