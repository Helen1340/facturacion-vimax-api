<?php

namespace Database\Factories;
use App\Models\Company;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DianNumbering>
 */
class DianNumberingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
             'company_id' => Company::factory(),
            'tipo_documento' => $this->faker->randomElement(['Factura', 'NotaCredito', 'NotaDebito']), // Tipo de documento
            'prefijo' => $this->faker->bothify('??##'), // Prefijo con letras y números (ej: FA01)
            'numero_inicio' => $this->faker->numberBetween(1, 1000), // Inicio del rango
            'numero_fin' => $this->faker->numberBetween(1001, 9999), // Fin del rango mayor al inicio
            'fecha_resolucion' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'), // Fecha de resolución DIAN
            'numero_resolucion' => $this->faker->numerify('#########'), // Número de resolución de la DIAN
            'fecha_inicio' => $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'), // Vigencia inicio
            'fecha_fin' => $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d'), // Vigencia fin
            'estado_actual' => $this->faker->randomElement(['Activo', 'Inactivo']), // Estado actual
        ];
    }
}
