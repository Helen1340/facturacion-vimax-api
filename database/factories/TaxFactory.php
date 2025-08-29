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
            'nombre' => $this->faker->word . ' ' . $this->faker->numerify('###'), 
            'descripcion'   => $this->faker->sentence(6),
            'tipo'          => $this->faker->randomElement(['IVA', 'ReteFuente', 'ICA']),
            'porcentaje_base'=> $this->faker->randomFloat(2, 0, 100),
            'estado'        => $this->faker->randomElement(['Activo', 'Inactivo']),
        ];
    
            
    }
}

