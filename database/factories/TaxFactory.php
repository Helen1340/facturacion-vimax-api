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
            'nombre' => $this->faker->randomElement([
                'IVA 19%',
                'IVA 5%',
                'IVA 0%',
                'Retefuente 2.5%',
                'ICA 9.66 por mil',
                'ReteIVA 15%',
            ]),
            'descripcion'   => $this->faker->sentence(6),
            'tipo'          => $this->faker->randomElement(['IVA', 'ReteFuente', 'ICA']),
            'porcentaje_base'=> $this->faker->randomElement([19, 5, 0, 2.5, 0.966, 15]),
            'estado'        => $this->faker->randomElement(['Activo', 'Inactivo']),
        ];
    
            
    }
}

