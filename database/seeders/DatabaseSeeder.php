<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Primero los usuarios porque son necesarios para ventas y compras
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,      // Productos primero
            PurchaseSeeder::class,     // Luego compras para tener stock
            SaleSeeder::class,         // Después ventas
            InvoiceSeeder::class,      // Finalmente facturas
        ]);
    }
}
