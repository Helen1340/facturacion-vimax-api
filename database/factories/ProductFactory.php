<?php

namespace Database\Factories;

use App\Models\MeasurementUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
    
    // Tomar una unidad de medida existente, o crear una si no hay
    $measurementUnit = MeasurementUnit::inRandomOrder()->first() ?? MeasurementUnit::factory()->create();

    // Generar un precio más realista según tipo de producto
    $precio_unitario = $this->faker->randomFloat(2, 1000, 200000); // entre $1.000 y $200.000 COP

    // Generar nombre de producto coherente
    $nombre_producto = ucfirst($this->faker->word) . ' ' . $this->faker->randomElement(['Premium', 'Deluxe', 'Standard', 'Eco']);

    return [
        'measurement_unit_id' => $measurementUnit->id,
        'codigo_estandar' => $this->faker->optional(0.7)->bothify('EST-####'), // 70% de probabilidad de generar código
        'codigo_producto' => $this->faker->unique()->bothify('PRD-####'),
        'nombre' => $nombre_producto,
        'descripcion' => $this->faker->sentence(6),
        'precio_unitario' => $precio_unitario,
        'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
    ];
}
    }