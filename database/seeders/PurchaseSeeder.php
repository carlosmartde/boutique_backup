<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PurchaseSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('rol', 'admin')->first();
        $gerente = User::where('rol', 'gerente')->first();
        $products = Product::all();
        $users = [$admin, $gerente];
        
        $suppliers = [
            'Distribuidora Nacional',
            'Comercial Mayorista',
            'Importadora Central',
            'Distribuidora del Sur',
            'Proveedora Express',
            'Mayorista Premium',
            'Distribuidora Rápida',
            'Comercializadora Global',
            'Suministros Totales',
            'Mayoreo Directo'
        ];

        // Generar 100 compras distribuidas en los últimos 3 meses
        for ($i = 0; $i < 100; $i++) {
            $randomDays = rand(0, 90);
            $randomUser = $users[array_rand($users)];
            
            $purchase = Purchase::create([
                'user_id' => $randomUser->id,
                'total' => 0,
                'supplier_name' => $suppliers[array_rand($suppliers)],
                'notes' => 'Compra #' . ($i + 1) . ' del ' . Carbon::now()->subDays($randomDays)->format('d/m/Y'),
                'created_at' => Carbon::now()->subDays($randomDays)->setTime(rand(8, 20), rand(0, 59), rand(0, 59)),
            ]);

            $total = 0;
            $numProducts = rand(3, 8); // Entre 3 y 8 productos por compra
            $selectedProducts = $products->random($numProducts);

            foreach ($selectedProducts as $product) {
                $quantity = rand(10, 50);
                $subtotal = $quantity * $product->purchase_price;
                $total += $subtotal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->purchase_price,
                    'subtotal' => $subtotal,
                ]);

                // Actualizar stock del producto
                $product->stock += $quantity;
                $product->save();
            }

            $purchase->total = $total;
            $purchase->save();
        }
    }
}