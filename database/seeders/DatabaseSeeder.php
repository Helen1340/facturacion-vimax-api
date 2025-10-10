<?php

namespace Database\Seeders;

use App\Models\DianNumbering;
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
        RolesSeeder::class,
        CompanyTableSeeder::class,
        MeasurementUnitTableSeeder::class,
        UsersSeeder::class,
        DianNumberingTableSeeder::class,
        DigitalCertificateTableSeeder::class,
        PaymentMethodTableSeeder::class,
        TaxTableSeeder::class,
        ElectronicInvoiceseeder::class,
        ServiceSeeder::class,
        ProductsSeeder::class,
        ProductTaxSeeder::class,
        ServiceTaxSeeder::class,
        InvoiceDetailSeeder::class,
        CreditDebitNoteTableSeeder::class,
        PaymentTableSeeder::class,
        ElectronicDocumentsSeeder::class,
        RadianEventTableSeeder::class,
        
    
    ]);


    }
}
