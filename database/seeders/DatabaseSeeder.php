<?php

namespace Database\Seeders;

use App\Models\ElectronicInvoice;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
    $this->call([
        // Agrega aquí el seeder de la empresa
        CompanyTableSeeder::class,
        DigitalCertificateTableSeeder::class,
        DianNumberingTableSeeder::class,
        CreditDebitNoteTableSeeder::class,
        ElectronicInvoiceseeder::class,
        paymentTableSeeder::class,
        PaymentMethodTableSeeder::class,
        MeasurementUnitTableSeeder::class,
        
    ]);



    }
}
