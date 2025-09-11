<?php

namespace Database\Seeders;

use App\Models\ElectronicDocument;
use App\Models\ElectronicInvoice;
use App\Models\InvoiceDetail;
use App\Models\Service;
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
        MeasurementUnitTableSeeder::class,
        CompanyTableSeeder::class,
        UsersSeeder::class,
        DianNumberingTableSeeder::class,
        PaymentMethodTableSeeder::class,
        ElectronicInvoiceseeder::class,
        CreditDebitNoteTableSeeder::class,
        DigitalCertificateTableSeeder::class,
        ElectronicDocumentsSeeder::class,
        TaxTableSeeder::class,
        ServiceSeeder::class,
        ProductsSeeder::class,
        InvoiceDetailSeeder::class,
        PaymentTableSeeder::class,
        RadianEventTableSeeder::class,
        RolesSeeder::class,
        
    
    ]);


    }
}
