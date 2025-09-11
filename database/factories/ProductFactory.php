<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\MeasurementUnit;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        // Buscar una unidad de medida existente o crear una nueva
        $measurementUnit = MeasurementUnit::query()->inRandomOrder()->first();

        if (!$measurementUnit) {
            $measurementUnit = MeasurementUnit::factory()->create();
        }

        // Generar precio
        $precio_unitario = $this->faker->randomFloat(2, 1000, 200000); // $1.000 - $200.000 COP

        // Generar nombre de producto
        $nombre_producto = ucfirst($this->faker->word) . ' ' .
                           $this->faker->randomElement(['Premium', 'Deluxe', 'Standard', 'Eco']);

        return [
            'measurement_unit_id' => $measurementUnit->id,
            'codigo_estandar'     => $this->faker->optional(0.7)->bothify('EST-####'),
            'codigo_producto'     => $this->faker->unique()->bothify('PRD-####'),
            'nombre'              => $nombre_producto,
            'descripcion'         => $this->faker->sentence(6),
            'precio_unitario'     => $precio_unitario,
            'estado'              => $this->faker->randomElement(['Activo', 'Inactivo']),
        ];
    }

    /**
     * Configuración post-creación para llenar la tabla pivote product_tax
     */
    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            // Seleccionar entre 1 y 2 impuestos existentes
            $taxes = Tax::inRandomOrder()->take(rand(1, 2))->pluck('id');

            if ($taxes->isNotEmpty()) {
                $product->taxes()->attach($taxes);
            }
        });
    }
}
