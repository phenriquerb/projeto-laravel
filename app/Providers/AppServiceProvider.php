<?php

namespace CodeShopping\Providers;

use CodeShopping\Models\ProductInput;
use CodeShopping\Models\ProductOutput;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //trabalhando com events do eloquent
        ProductInput::created(function($input){
            $product = $input->product;
            $product->stock += $input->amount;
            $product->save();

            //pode trabalhar tbm com observers
            //ou pode tbm trabalhar com events do eloquent, porem la no model.
        });

        ProductOutput::created(function($input){
            $product = $input->product;
            $product->stock -= $input->amount;
            if($product->stock < 0){
                throw new \Exception("Estoque de {$product->name} não pode ser negativo!");
            }
            $product->save();
        });
    }
}
