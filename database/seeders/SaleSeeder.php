<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('rol', 'admin')->first();
        $gerente = User::where('rol', 'gerente')->first();
        $vendedor = User::where('rol', 'vendedor')->first();
        $products = Product::all();
        $users = [$admin, $gerente, $vendedor];

        // Generar 100 ventas distribuidas en los últimos 3 meses
        for ($i = 0; $i < 100; $i++) {
            $randomDays = rand(0, 90); // últimos 3 meses
            $randomUser = $users[array_rand($users)];
            
            $sale = Sale::create([
                'user_id' => $randomUser->id,
                'total' => 0,
                'created_at' => Carbon::now()->subDays($randomDays)->setTime(rand(8, 20), rand(0, 59), rand(0, 59)),
            ]);

            $total = 0;
            $numProducts = rand(1, 5); // Entre 1 y 5 productos por venta
            $selectedProducts = $products->random($numProducts);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $subtotal = $quantity * $product->sale_price;
                $cost_total = $quantity * $product->purchase_price;
                $total += $subtotal;

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->sale_price,
                    'subtotal' => $subtotal,
                    'cost_total' => $cost_total,
                ]);

                // Actualizar stock del producto
                $product->stock -= $quantity;
                $product->save();
            }

            $sale->total = $total;
            $sale->save();
        }
    }
}