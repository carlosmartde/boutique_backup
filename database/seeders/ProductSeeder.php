<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Bebidas' => ['Coca-Cola', 'Pepsi', 'Fanta', 'Sprite', '7Up', 'RedBull', 'Monster', 'Gatorade', 'Powerade', 'Aquarius'],
            'Snacks' => ['Doritos', 'Cheetos', 'Lays', 'Pringles', 'Ruffles', 'Takis', 'Sabritas', 'Tostitos', 'Fritos', 'Churrumais'],
            'Lácteos' => ['Leche', 'Yogurt', 'Queso', 'Mantequilla', 'Crema', 'Helado', 'Natilla', 'Requesón', 'Panela', 'Oaxaca'],
            'Limpieza' => ['Jabón', 'Detergente', 'Cloro', 'Suavizante', 'Desinfectante', 'Escoba', 'Trapeador', 'Esponja', 'Papel Higiénico', 'Servilletas'],
            'Higiene Personal' => ['Shampoo', 'Jabón de Baño', 'Pasta Dental', 'Desodorante', 'Papel Higiénico', 'Cepillo de Dientes', 'Crema Corporal', 'Acondicionador', 'Gel', 'Talco'],
        ];

        $brands = [
            'Coca-Cola Company', 'PepsiCo', 'Nestlé', 'P&G', 'Unilever', 'Colgate-Palmolive', 
            'Johnson & Johnson', 'Kimberly-Clark', 'Danone', 'Mondelez', 'Bimbo', 'Lala', 'Alpura',
            'Sabritas', 'Gamesa', 'Barcel', 'Marinela', 'La Costeña', 'Del Valle', 'Jumex'
        ];

        $counter = 1;
        foreach ($categories as $category => $products) {
            foreach ($products as $productName) {
                for ($i = 1; $i <= 2; $i++) { // 2 variantes por producto
                    $purchasePrice = rand(10, 100);
                    $markup = rand(20, 50) / 100; // 20% a 50% de margen
                    $salePrice = $purchasePrice * (1 + $markup);

                    Product::create([
                        'code' => sprintf('PRD%04d', $counter++),
                        'name' => $productName . ' ' . $this->getVariant($i),
                        'brand' => $brands[array_rand($brands)],
                        'stock' => rand(10, 200),
                        'purchase_price' => $purchasePrice,
                        'sale_price' => ceil($salePrice), // Redondear hacia arriba
                    ]);
                }
            }
        }
    }

    private function getVariant($num)
    {
        $variants = [
            'Regular',
            'Grande',
            'Familiar',
            'Económico',
            'Premium'
        ];
        return $variants[$num % count($variants)];
    }
}