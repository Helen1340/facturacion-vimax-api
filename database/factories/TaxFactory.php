<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tax;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tax>
 */
class TaxFactory extends Factory
{
    protected $model = Tax::class;

    public function definition(): array
    {
        return [
            'nombre'        => $this->faker->unique()->word,        // Nombre del impuesto
            'descripcion'   => $this->faker->sentence(6),           // Descripción corta
            'tipo'          => $this->faker->randomElement(['IVA', 'ReteFuente', 'ICA']), // Tipo de impuesto
            'porcentaje_base'=> $this->faker->randomFloat(2, 0, 100), // Porcentaje base entre 0 y 100
            'estado'        => $this->faker->randomElement(['Activo', 'Inactivo']), // Estado del impuesto
        ];
    }
}

