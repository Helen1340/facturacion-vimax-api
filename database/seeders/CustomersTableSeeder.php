<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;


class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'Nombre_Completo'    => 'Juan Pérez',
            'Correo_Electronico' => 'juan.perez@example.com',
            'Telefono'           => '3001234567',
            'Razon_Social'       => 'Comercial JP S.A.S.',
            'Tipo_Persona'       => 'Natural',
            'Tipo_Documento'     => 'CC',
            'Observacion'        => 'Cliente frecuente',
            'Estado'             => true,
            'Direccion'          => 'Calle 123 #45-67',
            'Pais'               => 'Colombia',
            'Departamento'       => 'Antioquia',
            'Fecha'              => '2025-08-13',
        ]);

        Customer::create([
            'Nombre_Completo'    => 'María López',
            'Correo_Electronico' => 'maria.lopez@example.com',
            'Telefono'           => '3017654321',
            'Razon_Social'       => 'Distribuidora ML E.U.',
            'Tipo_Persona'       => 'Juridica',
            'Tipo_Documento'     => 'NIT',
            'Observacion'        => 'Pendiente de pago',
            'Estado'             => false,
            'Direccion'          => 'Carrera 50 #12-34',
            'Pais'               => 'Colombia',
            'Departamento'       => 'Cundinamarca',
            'Fecha'              => '2025-08-10',
        ]);

        Customer::create([
            'Nombre_Completo'    => 'Carlos Gómez',
            'Correo_Electronico' => 'carlos.gomez@example.com',
            'Telefono'           => '3009876543',
            'Razon_Social'       => 'Comercializadora CG Ltda.',
            'Tipo_Persona'       => 'Juridica',
            'Tipo_Documento'     => 'NIT',
            'Observacion'        => 'Cliente frecuente',
            'Estado'             => true,
            'Direccion'          => 'Avenida 30 #45-67',
            'Pais'               => 'Colombia',
            'Departamento'       => 'Antioquia',
            'Fecha'              => '2025-08-11',
        ]);

        Customer::create([
            'Nombre_Completo'    => 'Ana Martínez',
            'Correo_Electronico' => 'ana.martinez@example.com',
            'Telefono'           => '3124567890',
            'Razon_Social'       => 'Servicios AM S.A.S.',
            'Tipo_Persona'       => 'Natural',
            'Tipo_Documento'     => 'CC',
            'Observacion'        => 'Solicita cotización',
            'Estado'             => true,
            'Direccion'          => 'Calle 20 #15-90',
            'Pais'               => 'Colombia',
            'Departamento'       => 'Valle del Cauca',
            'Fecha'              => '2025-08-12',
        ]);

        Customer::create([
            'Nombre_Completo'    => 'Jorge Ramírez',
            'Correo_Electronico' => 'jorge.ramirez@example.com',
            'Telefono'           => '3151239876',
            'Razon_Social'       => 'Constructora JR E.U.',
            'Tipo_Persona'       => 'Juridica',
            'Tipo_Documento'     => 'NIT',
            'Observacion'        => 'En proceso de verificación',
            'Estado'             => false,
            'Direccion'          => 'Diagonal 5 #8-60',
            'Pais'               => 'Colombia',
            'Departamento'       => 'Santander',
            'Fecha'              => '2025-08-13',
        ]);
    }
}
