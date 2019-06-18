<?php

use CodeShopping\Models\Product;
use CodeShopping\Models\ProductInput;
use Illuminate\Database\Seeder;

class ProductInputTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        factory(ProductInput::class, 150)
            ->make() // Gera um new do ProductInput
            ->each(function($input) use($products){
                $product = $products->random();
                $input->product_id = $product->id;
                $input->save();
                //$product->stock += $input->amount;
                //$product->save();
            });
    }
}
