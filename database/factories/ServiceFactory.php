<?php

namespace Database\Factories;

use App\Models\MeasurementUnit;
use App\Models\Service;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        // Tomar una unidad de medida existente o crear una si no hay
        $measurementUnit = MeasurementUnit::inRandomOrder()->first()
            ?? MeasurementUnit::factory()->create();

        // Generar un precio más realista para servicios
        $precio_unitario = $this->faker->randomFloat(2, 20000, 500000); // entre $20.000 y $500.000 COP

        // Generar nombre de servicio coherente
        $nombre_servicio = ucfirst($this->faker->word) . ' ' . $this->faker->randomElement([
            'Consultoría', 'Asesoría', 'Soporte', 'Instalación', 'Mantenimiento'
        ]);

        return [
            'measurement_unit_id' => $measurementUnit->id,
            'nombre' => $nombre_servicio,
            'descripcion' => $this->faker->sentence(8),
            'codigo_servicio' => 'SVC-' . $this->faker->unique()->numberBetween(10000, 99999),
            'precio_unitario' => $precio_unitario,
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
        ];
    }

    /**
     * Estado afterCreating para asociar impuestos automáticamente
     */
    public function configure()
    {
        return $this->afterCreating(function (Service $service) {
            $taxes = Tax::inRandomOrder()->take(rand(1, 3))->pluck('id');

            if ($taxes->isNotEmpty()) {
                // llena la pivot service_tax
                $service->taxes()->syncWithoutDetaching($taxes);
            }
        });
    }
}
