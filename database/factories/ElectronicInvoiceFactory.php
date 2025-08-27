<?php

namespace Database\Factories;

use App\Models\ElectronicInvoice;

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

            'empresa_id' => 1, // se puede asociar luego dinámicamente
            'cliente_id' => 1,
            'numero' => $this->faker->unique()->numerify('FAC-#####'),
            'fecha_emision' => $this->faker->date(),
            'subtotal' => $this->faker->randomFloat(2, 10000, 500000),
            'impuestos' => $this->faker->randomFloat(2, 1000, 100000),
            'total' => $this->faker->randomFloat(2, 20000, 600000),
            'cufe' => $this->faker->uuid(),
            'estado' => $this->faker->randomElement(['Borrador','Emitida','Anulada']),
        
        ];
    }
}
