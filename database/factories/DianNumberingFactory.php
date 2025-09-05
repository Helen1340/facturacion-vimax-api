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
      
    // Generamos un rango coherente (inicio < fin)
    $numero_inicio = $this->faker->numberBetween(1, 5000);
    $numero_fin = $numero_inicio + $this->faker->numberBetween(500, 5000);

    // Fechas realistas de vigencia
    $fecha_resolucion = $this->faker->dateTimeBetween('-2 years', 'now');
    $fecha_inicio = (clone $fecha_resolucion)->modify('+1 day');
    $fecha_fin = (clone $fecha_inicio)->modify('+2 years');

    return [
        'company_id' =>Company::inRandomOrder()->first()?->id,
        'tipo_documento' => $this->faker->randomElement(['Factura', 'NotaCredito', 'NotaDebito']), // Documento DIAN
        'prefijo' => strtoupper($this->faker->bothify('??##')), // Ejemplo: FA01
        'numero_inicio' => $numero_inicio, 
        'numero_fin' => $numero_fin, 
        'fecha_resolucion' => $fecha_resolucion->format('Y-m-d'), 
        'numero_resolucion' => $this->faker->numerify('#########'), // Ejemplo: 187600123
        'fecha_inicio' => $fecha_inicio->format('Y-m-d'), 
        'fecha_fin' => $fecha_fin->format('Y-m-d'),
        'estado_actual' => $this->faker->randomElement(['Activo', 'Inactivo']),
    ];
}
}